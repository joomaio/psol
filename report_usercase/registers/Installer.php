<?php
namespace App\psol\report_usercase\registers;

use SPT\Application\IApp;

class Installer
{
    public static function info()
    {
        return [
            'author' => 'Pham Minh',
            'created_at' => '2023-01-03',
            'description' => 'Plugin used to for report',
            'tags' => ['etadoc'],
            'type' => 'plugin',
            'solution' => 'psol',
            'folder_name' => 'report_usercase',
            'name' => 'Plugin report usercase',
            'dependencies' => [
                'pnote'
            ]
        ];
    }
    
    public static function name()
    {
        return 'Plugin report usercase';
    }

    public static function detail()
    {
        return [
            'author' => 'Pham Minh',
            'created_at' => '2023-01-03',
            'description' => 'Plugin used to demo how the SPT works'
        ];
    }

    public static function version()
    {
        return '0.0.1';
    }

    public static function install( IApp $app)
    {
        // run sth to prepare the install
    }
    public static function uninstall( IApp $app)
    {
        // run sth to uninstall
    }
    public static function active( IApp $app)
    {
        // run sth to prepare the install
    }
    public static function deactive( IApp $app)
    {
        // run sth to uninstall
    }
}