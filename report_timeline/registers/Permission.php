<?php

namespace App\psol\report_timeline\registers;

use SPT\Application\IApp;

class Permission
{
    public static function registerAccess()
    {
        return [
            'timeline_manager', 'timeline_read', 'timeline_create', 'timeline_update', 'timeline_delete' 
        ];
    }
}
