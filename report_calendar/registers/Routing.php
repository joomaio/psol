<?php

namespace App\psol\report_calendar\registers;

use SPT\Application\IApp;

class Routing
{
    public static function registerEndpoints()
    {
        return [
            'calendar/ajax' => [
                'fnc' => [
                    'post' => 'report_calendar.ajax.find',
                ],
                'parameters' => ['id'],
            ],
        ];
    }
}
