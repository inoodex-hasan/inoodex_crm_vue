<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_id')->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('university_id')->nullable()->constrained('universities')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('course_intake_id')->nullable()->constrained('course_intakes')->nullOnDelete();
            $table->decimal('tuition_fee', 12, 2)->nullable();
            $table->enum('tuition_fee_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->enum('service_charge_status', ['pending', 'paid', 'partial'])->default('pending');
            $table->enum('application_priority', ['normal', 'priority', 'vip'])->default('normal');
            $table->text('internal_notes')->nullable();
            $table->json('documents_checklist')->nullable();
            $table->enum('final_status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->boolean('security_deposit_status')->default(false);
            $table->boolean('cvu_fee_status')->default(false);
            $table->boolean('admission_fee_status')->default(false);
            $table->boolean('final_payment_status')->default(false);
            $table->boolean('emgs_payment_status')->default(false);
            $table->integer('emgs_score')->nullable();
            $table->decimal('total_fee', 10, 2)->default(0.00);
            $table->string('status')->default('pending');
            $table->boolean('offer_letter_received')->default(false);
            $table->date('offer_letter_received_date')->nullable();
            $table->boolean('vfs_appointment')->default(false);
            $table->date('vfs_appointment_date')->nullable();
            $table->string('vfs_result')->default('pending');
            $table->text('vfs_note')->nullable();
            $table->boolean('file_submission')->default(false);
            $table->date('file_submission_date')->nullable();
            $table->enum('visa_status', ['not_applied', 'pending', 'approved', 'rejected'])->default('not_applied');
            $table->date('visa_decision_date')->nullable();
            $table->date('visa_approval_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};