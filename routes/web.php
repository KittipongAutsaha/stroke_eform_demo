<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorNoteController;
use App\Http\Controllers\NurseNoteController;

// หน้าแรก
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'approved'])->name('dashboard');

// โปรไฟล์ผู้ใช้
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// หน้าคอยอนุมัติ
Route::get('/pending-approval', function () {
    return view('auth.pending-approval');
})->name('pending-approval');

// โซนแอดมิน
Route::middleware(['auth', 'verified', 'approved', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    });

// หน้าข้อมูลคนไข้พื้นฐาน + บันทึกแพทย์/พยาบาล (Nested Resource)
Route::middleware(['auth', 'verified', 'approved', 'deny.staff'])->group(function () {
    Route::resource('patients', PatientController::class);

    // ประกาศ nested resource ให้ได้ชื่อ route แบบ patients.doctor-notes.* / patients.nurse-notes.*
    Route::resource('patients.doctor-notes', DoctorNoteController::class);
    Route::resource('patients.nurse-notes', NurseNoteController::class);
});

require __DIR__ . '/auth.php';
