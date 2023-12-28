<?php

namespace App\plugins\convert_tool\registers;

use SPT\Application\IApp;

class Cli
{
    public static function registerCommands()
    {
        return [
            'convert-data-notes' => [
                'description' => 'Convert Data Notes To New Format',
                'fnc' => 'convert_tool.database.convert_data_notes'
            ]
        ];
    }
}
