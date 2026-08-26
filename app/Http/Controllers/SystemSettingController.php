<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN
    |--------------------------------------------------------------------------
    */

    public function edit(): View
    {
        $settings =
            SystemSetting::current();

        return view(
            'settings.edit',
            compact(
                'settings'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request
    ): RedirectResponse {

        $settings =
            SystemSetting::current();


        $validated =
            $request->validate([

                /*
                |--------------------------------------------------------------------------
                | EMPRESA
                |--------------------------------------------------------------------------
                */

                'company_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'trade_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'ruc' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'phone' => [
                    'nullable',
                    'string',
                    'max:50',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                ],

                'address' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

                'logo' => [
                    'nullable',
                    'image',
                    'max:2048',
                ],

                /*
                |--------------------------------------------------------------------------
                | GENERAL
                |--------------------------------------------------------------------------
                */

                'currency' => [
                    'required',

                    Rule::in([
                        'USD',
                    ]),
                ],

                'timezone' => [
                    'required',
                    'timezone',
                ],

                /*
                |--------------------------------------------------------------------------
                | ALERTAS
                |--------------------------------------------------------------------------
                */

                'document_alert_days' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:365',
                ],

                'license_alert_days' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:365',
                ],

                /*
                |--------------------------------------------------------------------------
                | NUMERACIONES
                |--------------------------------------------------------------------------
                */

                'work_order_prefix' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'trip_prefix' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'transfer_prefix' => [
                    'required',
                    'string',
                    'max:20',
                ],

                'settlement_prefix' => [
                    'required',
                    'string',
                    'max:20',
                ],

                /*
                |--------------------------------------------------------------------------
                | ECONÓMICO
                |--------------------------------------------------------------------------
                */

                'vat_percentage' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:100',
                ],

                'decimal_places' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:4',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'logo'
            )
        ) {

            if (
                $settings->logo_path
                &&
                Storage::disk('public')
                ->exists(
                    $settings->logo_path
                )
            ) {

                Storage::disk('public')
                    ->delete(
                        $settings->logo_path
                    );
            }


            $validated['logo_path'] =
                $request
                ->file('logo')
                ->store(
                    'settings',
                    'public'
                );
        }


        unset(
            $validated['logo']
        );


        $validated['updated_by'] =
            Auth::id();


        $settings->update(
            $validated
        );


        return redirect()

            ->route(
                'settings.edit'
            )

            ->with(
                'success',
                'Configuración actualizada correctamente.'
            );
    }
}
