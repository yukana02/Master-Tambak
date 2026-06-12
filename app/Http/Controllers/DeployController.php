<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DeployController extends Controller
{
    public function __invoke(Request $request)
    {
        $output = [];

        $output[] = 'Running migrate...';
        Artisan::call('migrate', ['--force' => true]);
        $output[] = Artisan::output();

        $output[] = 'Running optimize:clear...';
        Artisan::call('optimize:clear');
        $output[] = Artisan::output();

        $output[] = 'Running view:clear...';
        Artisan::call('view:clear');
        $output[] = Artisan::output();

        $result = implode("\n", $output);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'output' => $result,
            ]);
        }

        return response("<pre>" . e($result) . "</pre>");
    }
}
