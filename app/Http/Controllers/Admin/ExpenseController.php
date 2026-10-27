<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\{Expense, ChartOfAccount, OfficeAccount, Salary};
use Mpdf\Mpdf;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('*accountant');

        $query = Expense::with(['creator', 'chartOfAccount']);

        // Search filter
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        // Category filter (using chart_of_account)
        if ($category = $request->get('category')) {
            $query->whereHas('chartOfAccount', function ($q) use ($category) {
                $q->where('name', 'like', "%{$category}%");
            });
        }

        // Date range filter
        if ($startDate = $request->get('start_date')) {
            $query->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('expense_date', '<=', $endDate);
        }

        $expenses = $query->latest()->paginate(15)->withQueryString();

        // Fetch expense accounts
        $categories = ChartOfAccount::where('is_active', true)
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'start_date', 'end_date']),
        ]);
    }

    public function create()
    {
        $this->authorize('*accountant');
        $categories = ChartOfAccount::where('is_active', true)
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();
        $accounts = OfficeAccount::where('status', 'active')->get();
        $pendings_salaries = Salary::whereIn('payment_status', ['pending', 'partial'])->get();

        return Inertia::render('Expenses/Create', [
            'categories' => $categories,
            'accounts' => $accounts,
            'pendings_salaries' => $pendings_salaries,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('*accountant');

        $validated = $this->validateExpense($request);

        $expense = Expense::create($validated);

        // Double-Entry Ledger Posting
        $officeAccount = $expense->office_account_id ? OfficeAccount::find($expense->office_account_id) : null;
        $period = \App\Models\AccountingPeriod::where('status', 'open')
            ->orWhere('is_closed', false)
            ->orderBy('start_date', 'desc')
            ->first();

        if ($officeAccount && $officeAccount->chart_of_account_id && $period && $expense->chart_of_account_id) {
            $entry = \App\Models\JournalEntry::create([
                'period_id' => $period->id,
                'date' => $expense->expense_date ?? now(),
                'reference_number' => 'JV-EXP-' . str_pad($expense->id, 5, '0', STR_PAD_LEFT),
                'note' => 'Automated post for Expense: ' . $expense->description,
                'status' => 'posted',
                'created_by' => Auth::id() ?? 1,
            ]);

            $expense->update(['journal_entry_id' => $entry->id]);

            // Debit Expense
            \App\Models\JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $expense->chart_of_account_id,
                'debit' => $expense->amount,
                'credit' => 0,
                'description' => 'Expense: ' . $expense->description,
            ]);

            // Credit Bank/Cash
            \App\Models\JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'chart_of_account_id' => $officeAccount->chart_of_account_id,
                'debit' => 0,
                'credit' => $expense->amount,
                'description' => 'Paid from: ' . $officeAccount->account_name,
            ]);
        }

        // Update salary payment status if linked
        if ($expense->salary_id) {
            $salary = Salary::find($expense->salary_id);
            if ($salary) {
                $totalPaid = Expense::where('salary_id', $expense->salary_id)->sum('amount');
                $salary->paid_amount = $totalPaid;

                if ($totalPaid >= $salary->net_salary) {
                    $salary->payment_status = 'paid';
                } elseif ($totalPaid > 0) {
                    $salary->payment_status = 'partial';
                }
                $salary->save();
            }
        }

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $this->authorize('*accountant');
        $categories = ChartOfAccount::where('is_active', true)
            ->where('type', 'expense')
            ->orderBy('code')
            ->get();
        $accounts = OfficeAccount::where('status', 'active')->get();

        return Inertia::render('Expenses/Edit', [
            'expense' => $expense,
            'categories' => $categories,
            'accounts' => $accounts,
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('*accountant');

        $validated = $this->validateExpense($request);

        $expense->update($validated);

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('*accountant');

        if ($expense->journal_entry_id) {
            \App\Models\JournalEntry::find($expense->journal_entry_id)?->delete();
        }

        return $this->safeDelete($expense, 'admin.expenses.index', [], 'Expense deleted successfully.');
    }

    public function downloadPdf(Expense $expense)
    {
        $this->authorize('*accountant');

        $expense->load(['creator', 'chartOfAccount', 'account']);

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);

        $html = view('pdf.expense_voucher', compact('expense'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="expense-' . $expense->id . '.pdf"');
    }

    // public function report(Request $request)
    // {
    //     $this->authorize('*accountant');

    //     $query = Expense::with(['creator', 'chartOfAccount']);

    //     // Apply same filters as index
    //     if ($search = $request->get('search')) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('description', 'like', "%{$search}%")
    //                 ->orWhere('payment_method', 'like', "%{$search}%");
    //         });
    //     }

    //     if ($category = $request->get('category')) {
    //         $query->whereHas('chartOfAccount', function ($q) use ($category) {
    //             $q->where('name', 'like', "%{$category}%");
    //         });
    //     }

    //     if ($startDate = $request->get('start_date')) {
    //         $query->whereDate('expense_date', '>=', $startDate);
    //     }
    //     if ($endDate = $request->get('end_date')) {
    //         $query->whereDate('expense_date', '<=', $endDate);
    //     }

    //     $expenses = $query->orderBy('expense_date', 'desc')->get();
    //     $totalAmount = $expenses->sum('amount');

    //     $pdf = Pdf::loadView('pdf.expense_report', compact('expenses', 'totalAmount', 'request'));
    //     return $pdf->download('expenses-report-' . now()->format('Y-m-d') . '.pdf');
    // }

    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'chart_of_account_id' => ['required', 'exists:chart_of_accounts,id'],
            'payment_method' => ['required', 'in:cash,bank_transfer,mobile_banking,cheque'],
            'office_account_id' => ['nullable', 'exists:office_accounts,id'],
            'salary_id' => ['nullable', 'exists:salaries,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }

       public function preview(Request $request)
    {
        $this->authorize('*accountant');

        $query = Expense::with(['creator', 'chartOfAccount']);

        // Apply same filters as index
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->whereHas('chartOfAccount', function ($q) use ($category) {
                $q->where('name', 'like', "%{$category}%");
            });
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('expense_date', '<=', $endDate);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $totalAmount = $expenses->sum('amount');

        // Generate PDF using mPDF for preview
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);

        $html = view('pdf.expense_report', compact('expenses', 'totalAmount', 'request'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="expenses-report-preview.pdf"');
    }

    public function download(Request $request)
    {
        $this->authorize('*accountant');

        $query = Expense::with(['creator', 'chartOfAccount']);

        // Apply same filters as index
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $query->whereHas('chartOfAccount', function ($q) use ($category) {
                $q->where('name', 'like', "%{$category}%");
            });
        }

        if ($startDate = $request->get('start_date')) {
            $query->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->whereDate('expense_date', '<=', $endDate);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $totalAmount = $expenses->sum('amount');

        // Generate PDF using mPDF for download
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ]);

        $html = view('pdf.expense_report', compact('expenses', 'totalAmount', 'request'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="expenses-report-' . now()->format('Y-m-d') . '.pdf"');
    }
}
