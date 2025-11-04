<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DoctorNote extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Constants: Status
    |--------------------------------------------------------------------------
    */
    public const STATUS_PLANNED     = 'planned';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_SIGNED_OFF  = 'signed_off';
    public const STATUS_CANCELLED   = 'cancelled';

    /*
    |--------------------------------------------------------------------------
    | Fillable Fields
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'status',
        'scheduled_for',
        'recorded_at',
        'signed_off_at',
        'chief_complaint',
        'diagnosis',
        'differential_diagnosis',
        'clinical_summary',
        'physical_exam',
        'nihss_score',
        'gcs_score',
        'imaging_summary',
        'lvo_suspected',
        'treatment_plan',
        'orders',
        'prescription_note',
        'created_by_ip',
        'updated_by_ip',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'scheduled_for'  => 'datetime',
        'recorded_at'    => 'datetime',
        'signed_off_at'  => 'datetime',
        'lvo_suspected'  => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    public function scopeMine($query, $doctorId = null)
    {
        // ใช้ Auth::id() แทน auth()->id() และกันกรณี null
        $doctorId = $doctorId ?? Auth::id();

        // ถ้าไม่มีผู้ใช้ล็อกอิน (เช่นตอน factory/seeder) ให้คืน query ว่าง (ป้องกันดึงทั้งหมดโดยไม่ตั้งใจ)
        if (!$doctorId) {
            return $query->whereRaw('1=0');
        }

        return $query->where('doctor_id', $doctorId);
    }

    public function scopeEditable($query)
    {
        return $query->whereNull('signed_off_at');
    }

    public function scopePlanned($query)
    {
        return $query->where('status', self::STATUS_PLANNED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeSignedOff($query)
    {
        return $query->where('status', self::STATUS_SIGNED_OFF);
    }
}
