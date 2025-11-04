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
        Schema::create('doctor_notes', function (Blueprint $table) {
            $table->id();

            // ความสัมพันธ์หลัก
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->restrictOnDelete();

            // สถานะและเวลา
            $table->enum('status', ['planned', 'in_progress', 'signed_off', 'cancelled'])->default('planned');
            $table->dateTime('scheduled_for')->nullable()->comment('วันและเวลานัดหมาย');
            $table->dateTime('recorded_at')->comment('วันและเวลาเริ่มตรวจจริง');
            $table->dateTime('signed_off_at')->nullable()->comment('วันและเวลาที่เซ็นปิดเคส');

            // ข้อมูลทางการแพทย์
            $table->string('chief_complaint')->comment('อาการหลักที่ผู้ป่วยบอก');
            $table->text('diagnosis')->comment('การวินิจฉัยหลัก');
            $table->text('differential_diagnosis')->nullable()->comment('การวินิจฉัยแยกโรค');
            $table->text('clinical_summary')->nullable()->comment('สรุปภาพรวมทางคลินิก');
            $table->text('physical_exam')->comment('ผลการตรวจร่างกาย');
            $table->unsignedTinyInteger('nihss_score')->nullable()->comment('คะแนน NIHSS');
            $table->unsignedTinyInteger('gcs_score')->nullable()->comment('คะแนน GCS');
            $table->text('imaging_summary')->nullable()->comment('สรุปผลภาพถ่ายรังสี');
            $table->boolean('lvo_suspected')->nullable()->comment('สงสัย LVO หรือไม่');
            $table->text('treatment_plan')->nullable()->comment('แผนการรักษา');
            $table->text('orders')->nullable()->comment('คำสั่งทางการแพทย์');
            $table->text('prescription_note')->nullable()->comment('หมายเหตุเกี่ยวกับยา');

            // Audit & IP Tracking
            $table->string('created_by_ip', 45)->nullable();
            $table->string('updated_by_ip', 45)->nullable();

            // timestamps & soft delete
            $table->timestamps();
            $table->softDeletes();

            // indexes เพื่อประสิทธิภาพ
            $table->index('patient_id');
            $table->index('doctor_id');
            $table->index('status');
            $table->index('recorded_at');
            $table->index('scheduled_for');
            $table->index(['patient_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_notes');
    }
};
