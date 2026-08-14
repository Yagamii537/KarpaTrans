<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'first_names',
        'last_names',
        'identification',
        'birth_date',
        'phone',
        'secondary_phone',
        'email',
        'address',
        'license_number',
        'license_type',
        'license_issue_date',
        'license_expiration_date',
        'license_points',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'photo',
        'identification_document',
        'license_document',
        'hire_date',
        'employee_code',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'license_issue_date' => 'date',
            'license_expiration_date' => 'date',
            'hire_date' => 'date',
            'license_points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_names . ' ' . $this->last_names);
    }

    public function getInitialsAttribute(): string
    {
        $firstInitial = mb_substr($this->first_names, 0, 1);
        $lastInitial = mb_substr($this->last_names, 0, 1);

        return mb_strtoupper($firstInitial . $lastInitial);
    }

    public function getLicenseStatusAttribute(): string
    {
        if (!$this->license_expiration_date) {
            return 'unknown';
        }

        $today = Carbon::today();
        $expiration = $this->license_expiration_date;

        if ($expiration->isBefore($today)) {
            return 'expired';
        }

        if ($expiration->diffInDays($today) <= 30) {
            return 'expiring';
        }

        return 'valid';
    }

    public function getLicenseStatusLabelAttribute(): string
    {
        return match ($this->license_status) {
            'expired' => 'Vencida',
            'expiring' => 'Próxima a vencer',
            'valid' => 'Vigente',
            default => 'Sin información',
        };
    }

    public function getLicenseDaysRemainingAttribute(): ?int
    {
        if (!$this->license_expiration_date) {
            return null;
        }

        return Carbon::today()->diffInDays(
            $this->license_expiration_date,
            false
        );
    }

    public function restrictions()
    {
        return $this->hasMany(DriverRestriction::class);
    }
}
