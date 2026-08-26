<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [

        'company_name',
        'trade_name',
        'ruc',

        'phone',
        'email',
        'address',

        'logo_path',

        'currency',
        'timezone',

        'document_alert_days',
        'license_alert_days',

        'work_order_prefix',
        'trip_prefix',
        'transfer_prefix',
        'settlement_prefix',

        'vat_percentage',
        'decimal_places',

        'updated_by',
    ];


    protected function casts(): array
    {
        return [

            'document_alert_days' =>
            'integer',

            'license_alert_days' =>
            'integer',

            'vat_percentage' =>
            'decimal:2',

            'decimal_places' =>
            'integer',
        ];
    }


    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN ÚNICA
    |--------------------------------------------------------------------------
    */

    public static function current(): self
    {
        return self::query()
            ->firstOrCreate(
                [
                    'id' => 1,
                ],
                [
                    'company_name' =>
                    'KARPAN TRANST S.A.',

                    'trade_name' =>
                    'Karpan Transt',

                    'currency' =>
                    'USD',

                    'timezone' =>
                    'America/Guayaquil',

                    'document_alert_days' =>
                    30,

                    'license_alert_days' =>
                    30,

                    'work_order_prefix' =>
                    'OT',

                    'trip_prefix' =>
                    'VIA',

                    'transfer_prefix' =>
                    'TRA',

                    'settlement_prefix' =>
                    'LIQ',

                    'vat_percentage' =>
                    15,

                    'decimal_places' =>
                    2,
                ]
            );
    }
}
