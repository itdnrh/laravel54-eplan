<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Running;
use App\Models\DocType;

class RunningController extends Controller
{
    public function getByDocType($docType)
    {
        $running = Running::where('doc_type_id', $docType)->orderBy('running_no', 'DESC')->first();
        $newRunningNo = $running ? 1 : $running->running_no + 1;

        return [
            'running' => $newRunningNo
        ];
    }
}
