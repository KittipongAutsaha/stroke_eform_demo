<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'hn',
        'cid',
        'first_name',
        'last_name',
        'dob',
        'sex',
        'address_short',
        'note_general',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    // Accessor: ชื่อเต็ม
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
