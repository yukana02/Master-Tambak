<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Date::serializeUsing(function (\Carbon\Carbon $date) {
            return $date->format('d/m/Y');
        });

        // Global date format conversion for incoming requests
        $request = app('request');
        if ($request && ! $request->isMethod('get')) {
            $dateFields = ['stocking_date', 'harvest_date', 'fed_at', 'harvested_at', 'transaction_date'];
            $data = $request->all();
            $changed = false;

            foreach ($dateFields as $field) {
                if ($request->filled($field)) {
                    $value = $request->input($field);
                    // Check if format is dd/mm/yyyy
                    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $value);
                            $data[$field] = $date->format('Y-m-d');
                            $changed = true;
                        } catch (\Exception $e) {}
                    }
                }
            }

            if ($changed) {
                $request->merge($data);
            }
        }
    }
}
