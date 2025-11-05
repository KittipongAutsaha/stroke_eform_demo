<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nurse_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('nurse_id')->constrained('users')->cascadeOnDelete();

            $table->enum('status', ['planned', 'in_progress', 'signed_off', 'cancelled'])->default('planned');

            $table->dateTime('scheduled_for')->nullable();
            $table->dateTime('recorded_at')->nullable();
            $table->dateTime('signed_off_at')->nullable();

            $table->text('nursing_assessment')->nullable(false);
            $table->text('vital_signs_summary')->nullable(false);
            $table->text('nursing_diagnosis')->nullable();
            $table->text('nursing_care_plan')->nullable(false);
            $table->text('interventions_summary')->nullable();
            $table->text('progress_note')->nullable(false);
            $table->text('education_or_safety_note')->nullable();
            $table->string('sign_note')->nullable();

            $table->string('created_by_ip', 45)->nullable();
            $table->string('updated_by_ip', 45)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('patient_id');
            $table->index('nurse_id');
            $table->index(['patient_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nurse_notes');
    }
};
