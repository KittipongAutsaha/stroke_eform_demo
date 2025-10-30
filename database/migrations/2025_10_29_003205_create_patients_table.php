<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            // รหัสเวชระเบียน (HN) — เป็นคีย์เฉพาะ, ใช้ค้นหาผู้ป่วย
            $table->string('hn', 20)->unique()->index();

            // เลขบัตรประชาชน (13 หลัก) — อาจว่างได้
            $table->string('cid', 13)->nullable();

            // ชื่อและนามสกุล
            $table->string('first_name', 100);
            $table->string('last_name', 100);

            // วันเกิด (อาจไม่ระบุได้)
            $table->date('dob')->nullable();

            // เพศ (ชาย, หญิง, อื่น ๆ, ไม่ระบุ)
            $table->enum('sex', ['male', 'female', 'other', 'unknown'])->nullable();

            // ที่อยู่ย่อ (เช่น บ้านเลขที่ / ตำบล / อำเภอ)
            $table->string('address_short', 255)->nullable();

            // หมายเหตุทั่วไป (สำหรับบันทึกเพิ่มเติม)
            $table->text('note_general')->nullable();

            // ผู้สร้างและผู้แก้ไขข้อมูล (อ้างอิงตาราง users)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Soft Delete (ใช้เมื่อผู้ดูแลระบบลบข้อมูล)
            $table->softDeletes();

            // วันที่สร้าง/อัปเดตอัตโนมัติ
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
