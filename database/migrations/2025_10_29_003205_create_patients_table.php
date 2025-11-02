<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // ระบุตัวตนพื้นฐาน
            $table->string('hn', 20)->unique();
            $table->string('cid', 32)->nullable()->index();

            // ชื่อ-สกุล/วันเกิด/เพศ
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('dob')->nullable();
            $table->enum('sex', ['male', 'female', 'other', 'unknown'])->nullable();

            // ชีวข้อมูล
            $table->enum('blood_group', ['A', 'B', 'AB', 'O'])->nullable()->index();
            $table->enum('rh_factor', ['+', '-'])->nullable();

            // การติดต่อ/ที่อยู่
            $table->string('phone', 32)->nullable()->index();
            $table->string('address_short', 255)->nullable();
            $table->string('address_full', 1000)->nullable();
            $table->string('postal_code', 20)->nullable()->index();

            // ผู้ติดต่อฉุกเฉิน
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_relation', 80)->nullable();
            $table->string('emergency_contact_phone', 32)->nullable();

            // สัญชาติ/ภาษา
            $table->string('nationality', 100)->nullable();
            $table->string('preferred_language', 20)->nullable();

            // สิทธิ์ประกัน
            $table->string('insurance_scheme', 120)->nullable()->index();
            $table->string('insurance_no', 100)->nullable()->index();

            // ความยินยอม
            $table->timestamp('consent_at')->nullable();
            $table->text('consent_note')->nullable();

            // บันทึกทั่วไป
            $table->text('note_general')->nullable();

            // ผู้สร้าง/แก้ไข (FK -> users)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // ลบแบบอ่อน + เวลา
            $table->softDeletes();
            $table->timestamps();

            // ดัชนีรวมที่พบบ่อยในการค้นหา (ปรับได้ตามการใช้งานจริง)
            $table->index(['last_name', 'first_name', 'dob']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
