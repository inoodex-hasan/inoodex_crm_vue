<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'application_id',
        'invoice_id',
        'amount',
        'payment_type',
        'payment_date',
        'collected_by',
        'receipt_number',
        'payment_status',
        'office_account_id',
        'journal_entry_id',
        'notes',
    ];

    protected static function booted()
    {
        static::creating(function ($payment) {
            if (!$payment->payment_date) {
                $payment->payment_date = now();
            }
            if (auth()->check()) {
                $payment->collected_by = auth()->id();
            }
            if (!$payment->receipt_number) {
                $dateStr = date('Ymd');
                $lastPayment = static::where('receipt_number', 'like', "REC-{$dateStr}-%")->latest('id')->first();
                $nextNumber = 1;
                if ($lastPayment && preg_match('/-(\d+)(?:-[A-Z0-9]+)?$/', $lastPayment->receipt_number, $matches)) {
                    $nextNumber = ((int) $matches[1]) + 1;
                }
                $proposed = 'REC-' . $dateStr . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                if (static::where('receipt_number', $proposed)->exists()) {
                    $proposed .= '-' . strtoupper(bin2hex(random_bytes(1)));
                }
                $payment->receipt_number = $proposed;
            }
        });
    }

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function account()
    {
        return $this->belongsTo(OfficeAccount::class, 'office_account_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
