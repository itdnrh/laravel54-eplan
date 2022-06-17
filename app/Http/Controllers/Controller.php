<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Running;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getRunningByDocType($docType)
    {
        $running = Running::where('doc_type_id', $docType)->orderBy('running_no', 'DESC')->first();
        $newRunningNo = $running ? $running->running_no + 1 : 1;

        return $newRunningNo;
    }
}
