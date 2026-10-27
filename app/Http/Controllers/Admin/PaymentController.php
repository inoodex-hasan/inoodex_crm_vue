<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\{Application, Invoice, OfficeAccount, Payment, Setting, Student, User, JournalEntry, JournalEntryItem, AccountingPeriod, ChartOfAccount};
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('*accountant');

        $query = Payment::with(['student', 'application', 'collector']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'student',
                    function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    }
                )->orWhere('receipt_number', 'like', "%{$search}%")
                    ->orWhereHas(
                        'application',
                        function ($aq) use ($search) {
                            $aq->where('application_id', 'like', "%{$search}%");
                        }
                    );
            });
        }

        if ($type = $request->get('payment_type')) {
            $query->where('payment_type', $type);
        }

        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only(['search', 'payment_type', 'payment_status']),
        ]);
    }

    public function create(Request $request)
    {
        $this->authorize('*accountant');

        $applications = Application::with('student')->latest()->get();
        $users = User::orderBy('name')->get(['id', 'name']);
        $selected_application_id = $request->get('application_id');
        $accounts = OfficeAccount::where('status', 'active')->get();

        return Inertia::render('Payments/Create', [
            'applications' => $applications,
            'users' => $users,
            'selected_application_id' => $selected_application_id,
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('*accountant');

        $validated = $this->validatePayment($request);

        $application = Application::findOrFail($validated['application_id']);
        $validated['student_id'] = $application->student_id;

        try {
            DB::beginTransaction();

            $payment = Payment::create($validated);

            // Double-Entry Ledger Posting
            if ($payment->payment_status === 'completed') {
                $this->syncJournalEntry($payment, $application);
            }

            DB::commit();
            return redirect()
                ->route('admin.payments.index')
                ->with('success', 'Payment recorded and Ledger posted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Posting failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Payment $payment)
    {
        $this->authorize('*accountant');

        $applications = Application::with('student')->latest()->get();
        $users = User::orderBy('name')->get(['id', 'name']);
        $accounts = OfficeAccount::where('status', 'active')->get();

        return Inertia::render('Payments/Edit', [
            'payment' => $payment,
            'applications' => $applications,
            'users' => $users,
            'accounts' => $accounts,
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorize('*accountant');

        $validated = $this->validatePayment($request);

        $application = Application::findOrFail($validated['application_id']);
        $validated['student_id'] = $application->student_id;

        try {
            DB::beginTransaction();

            $payment->update($validated);

            if ($payment->payment_status === 'completed') {
                $this->syncJournalEntry($payment, $application);
            } elseif ($payment->journal_entry_id) {
                JournalEntry::find($payment->journal_entry_id)?->delete();
                $payment->update(['journal_entry_id' => null]);
            }

            DB::commit();
            return redirect()
                ->route('admin.payments.index')
                ->with('success', 'Payment updated and Ledger synchronized successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Update failed: ' . $e->getMessage()])->withInput();
        }
    }

    private function syncJournalEntry(Payment $payment, Application $application): void
    {
        $officeAccount = OfficeAccount::find($payment->office_account_id);
        if (!$officeAccount || !$officeAccount->chart_of_account_id) {
            return;
        }

        $period = AccountingPeriod::where('status', 'open')
            ->orWhere('is_closed', false)
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$period) {
            throw new \Exception("Cannot post ledger: No open accounting period found. Please open an accounting period under Finance > Accounting Periods.");
        }

        $incomeHead = ChartOfAccount::where('name', 'like', '%Student Fee%')
            ->orWhere('code', '4001')
            ->first() ?? ChartOfAccount::where('type', 'revenue')->first();

        if (!$incomeHead) {
            $incomeHead = ChartOfAccount::create([
                'name' => 'Student Fees Revenue',
                'code' => '4001',
                'type' => 'revenue',
                'is_active' => true,
            ]);
        }

        // Remove existing journal entry if any before reposting
        if ($payment->journal_entry_id) {
            JournalEntry::find($payment->journal_entry_id)?->delete();
        }

        $studentName = $application->student ? ($application->student->first_name . ' ' . $application->student->last_name) : 'Student';

        $entry = JournalEntry::create([
            'period_id' => $period->id,
            'application_id' => $application->id,
            'date' => $payment->payment_date ?? now(),
            'reference_number' => 'JV-PAY-' . ($payment->receipt_number ?: $payment->id),
            'note' => 'Automated post for Student Payment: ' . ($payment->receipt_number ?: $payment->id),
            'status' => 'posted',
            'created_by' => Auth::id() ?? 1,
        ]);

        $payment->update(['journal_entry_id' => $entry->id]);

        // Debit Bank/Cash
        JournalEntryItem::create([
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $officeAccount->chart_of_account_id,
            'debit' => $payment->amount,
            'credit' => 0,
            'description' => 'Payment received in ' . $officeAccount->account_name . ' (' . $studentName . ')',
        ]);

        // Credit Revenue
        JournalEntryItem::create([
            'journal_entry_id' => $entry->id,
            'chart_of_account_id' => $incomeHead->id,
            'debit' => 0,
            'credit' => $payment->amount,
            'description' => 'Student Fee Credit: ' . $studentName,
        ]);
    }

    public function destroy(Payment $payment)
    {
        $this->authorize('*accountant');

        try {
            DB::beginTransaction();
            if ($payment->journal_entry_id) {
                JournalEntry::find($payment->journal_entry_id)?->delete();
            }
            $payment->delete();
            DB::commit();

            return redirect()
                ->route('admin.payments.index')
                ->with('success', 'Payment and ledger entry deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['msg' => 'Deletion failed: ' . $e->getMessage()]);
        }
    }

    // public function downloadInvoice(Payment $payment)
    // {
    //     $this->authorize('*accountant');

    //     $payment->load(['student', 'collector', 'application.university.country', 'application.course', 'application.intake']);
    //     $settings = Setting::pluck('value', 'key')->all();

    //     $pdf = Pdf::loadView('pdf.payment_receipt', compact('payment', 'settings'));

    //     $filename = 'Invoice_' . ($payment->receipt_number ?: $payment->id) . '.pdf';

    //     return $pdf->download($filename);
    // }

      public function downloadInvoice(Payment $payment)
    {
        $this->authorize('*accountant');

        $payment->load(['student', 'collector', 'account', 'application.university.country', 'application.course', 'application.intake', 'invoice.payments', 'invoice.items']);
        $settings = Setting::pluck('value', 'key')->all();

        // Calculate remaining balance considering all payments
        $totalPaid = $payment->invoice ? $payment->invoice->payments->sum('amount') : $payment->amount;
        $remainingBalance = $payment->invoice ? max(0, $payment->invoice->total_amount - $totalPaid) : 0;

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);
        $mpdf->WriteHTML(view('pdf.payment_receipt', compact('payment', 'settings', 'remainingBalance', 'totalPaid'))->render());

        $filename = 'Invoice_' . ($payment->receipt_number ?: $payment->id) . '.pdf';

        return $mpdf->Output($filename, 'D');
    }

    private function validatePayment(Request $request): array
    {
        return $request->validate([
            'application_id' => ['required', 'exists:applications,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_type' => ['required', Rule::in(['advance', 'partial', 'final'])],
            'payment_date' => ['nullable', 'date'],
            'receipt_number' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['required', Rule::in(['pending', 'completed'])],
            'office_account_id' => ['nullable', 'exists:office_accounts,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    public function getApplicationBalance(Request $request)
    {
        $request->validate(['application_id' => 'required|exists:applications,id']);

        $application = Application::with('payments')->findOrFail($request->application_id);

        $totalPaid = $application->payments()
            ->whereIn('payment_status', ['pending', 'completed'])
            ->sum('amount');

        return response()->json([
            'total_fee' => $application->total_fee,
            'total_paid' => $totalPaid,
            'balance' => $application->total_fee - $totalPaid
        ]);
    }

    public function getApplicationInvoices(Request $request)
    {
        $request->validate(['application_id' => 'required|exists:applications,id']);

        $invoices = Invoice::where('application_id', $request->application_id)
            ->with(['payments' => function ($q) {
                $q->whereIn('payment_status', ['pending', 'completed']);
            }])
            ->get()
            ->map(function ($invoice) {
                $totalPaid = $invoice->payments->sum('amount');
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'total_amount' => $invoice->total_amount,
                    'total_paid' => $totalPaid,
                    'remaining' => $invoice->total_amount - $totalPaid,
                    'status' => $invoice->status,
                ];
            });

        return response()->json([
            'invoices' => $invoices
        ]);
    }

    public function report(Request $request)
    {
        $this->authorize('*accountant');

        $query = Payment::with(['student', 'application', 'collector']);

        // Date range filter
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('payment_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('payment_date', '<=', $endDate);
        }

        // Status filter
        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }

        // Type filter
        if ($type = $request->get('payment_type')) {
            $query->where('payment_type', $type);
        }

        $payments = $query->latest()->get();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);

        $html = view('pdf.payments_list', compact('payments', 'request'))->render();
        $mpdf->WriteHTML($html);

        $outputMode = $request->get('output') === 'download' ? 'D' : 'I';

        return response($mpdf->Output('payments-report.pdf', $outputMode), 200)
            ->header('Content-Type', 'application/pdf');
    }
}
