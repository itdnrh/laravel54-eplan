<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Running;
use App\Models\DocType;

class RunningController extends Controller
{
    public function getByDocType($docType)
    {
        return [
            'running' => $this->getRunningByDocType($docType)
        ];
    }
}
