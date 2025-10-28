<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('hn', 20)->unique()->index();      // รหัสเวชระเบียน (immutable หลังสร้าง)
            $table->string('cid', 25)->nullable();            // เลขบัตร/เลขประจำตัว (ถ้ามี)
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('dob');                               // วันเกิด (ต้องก่อนวันนี้)
            $table->enum('sex', ['male', 'female', 'other', 'unknown']);
            $table->string('address_short', 255)->nullable(); // ที่อยู่ย่อ
            $table->text('note_general')->nullable();         // บันทึกทั่วไป
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();                            // ลบแบบกู้คืนได้ (Admin เท่านั้น)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
