<?php

namespace App\psol\report_calendar\registers;

use SPT\Application\IApp;

class Permission
{
    public static function registerAccess()
    {
        return [
            'calendar_manager', 'calendar_read', 'calendar_create', 'calendar_update', 'calendar_delete' 
        ];
    }
}
