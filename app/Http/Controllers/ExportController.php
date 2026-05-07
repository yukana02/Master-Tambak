<?php

namespace App\Http\Controllers;

use App\Exports\FinanceTransactionsExport;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function finance(): BinaryFileResponse
    {
        return Excel::download(new FinanceTransactionsExport, 'laporan-keuangan.xlsx');
    }

    public function sales(): BinaryFileResponse
    {
        return Excel::download(new SalesExport, 'laporan-penjualan.xlsx');
    }
}
