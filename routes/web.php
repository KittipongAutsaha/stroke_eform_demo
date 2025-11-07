<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorNoteController;
use App\Http\Controllers\NurseNoteController;

// หน้าแรก
Route::get('/', fn() => view('welcome'))->name('home');

// Dashboard
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified', 'approved'])
    ->name('dashboard');

// โปรไฟล์ผู้ใช้
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// หน้าคอยอนุมัติ
Route::get('/pending-approval', fn() => view('auth.pending-approval'))->name('pending-approval');

// โซนแอดมิน
Route::middleware(['auth', 'verified', 'approved', 'role:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    });

// ---------------------------------------------------------
// Patients
// - index: staff เข้าได้ (search)
// - show:  staff เข้าได้ (Basic-only ตาม policy/view)
// - อื่นๆ บล็อค staff ด้วย deny.staff
// ---------------------------------------------------------

// index → staff เข้าได้
Route::middleware(['auth', 'verified', 'approved'])
    ->get('/patients', [PatientController::class, 'index'])
    ->name('patients.index');

// show → staff เข้าได้
Route::middleware(['auth', 'verified', 'approved'])
    ->get('/patients/{patient}', [PatientController::class, 'show'])
    ->name('patients.show');

// เส้นทางอื่นของ patients + notes → บล็อค staff
Route::middleware(['auth', 'verified', 'approved', 'deny.staff'])->group(function () {
    // patient CRUD (ยกเว้น index และ show)
    Route::resource('patients', PatientController::class)->except(['index', 'show']);

    // Doctor Notes: บล็อค nurse ด้วย
    Route::middleware('deny.nurse.doctor-notes')->group(function () {
        Route::resource('patients.doctor-notes', DoctorNoteController::class);
    });

    // Nurse Notes
    Route::resource('patients.nurse-notes', NurseNoteController::class);
});

require __DIR__ . '/auth.php';
