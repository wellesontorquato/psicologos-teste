<?php

namespace App\Helpers;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditHelper
{
    public static function log($action, $description)
    {
        Audit::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'description'=> $description,
            'ip_address' => Request::ip(),
        ]);
    }
}
