<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Exports\SubscriberExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class SubscriberController extends Controller
{
    //  Export subscribers
    public function exportSubsCSVFile()
    {

        $fileName = 'Subscribers_' . date('Y_m_d_H_i_s') . '.xlsx';
        return Excel::download(new SubscriberExport, $fileName);
    }
}
