<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class NurseNote extends Model
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
        'nurse_id',
        'status',
        'scheduled_for',
        'recorded_at',
        'signed_off_at',
        'nursing_assessment',
        'vital_signs_summary',
        'nursing_diagnosis',
        'nursing_care_plan',
        'interventions_summary',
        'progress_note',
        'education_or_safety_note',
        'sign_note',
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

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
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

    public function scopeMine($query, $nurseId = null)
    {
        $nurseId = $nurseId ?? Auth::id();

        if (!$nurseId) {
            return $query->whereRaw('1=0');
        }

        return $query->where('nurse_id', $nurseId);
    }

    public function scopeEditable($query)
    {
        return $query->whereNotIn('status', [self::STATUS_SIGNED_OFF, self::STATUS_CANCELLED]);
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

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function isLocked(): bool
    {
        return in_array($this->status, [self::STATUS_SIGNED_OFF, self::STATUS_CANCELLED], true);
    }
}
