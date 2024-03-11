<?php
namespace App\psol\report_tree\registers;

use SPT\Application\IApp;
use SPT\Support\Loader;

class Installer
{
    public static function info()
    {
        return [
            'tags'=>['psol'],
            'type' => 'plugin',
            'solution' => 'psol',
            'folder_name' => 'report_tree',
            'name' => 'Plugin report tree of note',
            'dependencies' => [
                'pnote'
            ]
        ];
    }
    
    public static function name()
    {
        return 'Plugin report tree of note';
    }

    public static function detail()
    {
        return [
            'author' => 'Pham Minh',
            'created_at' => '2023-01-03',
            'description' => 'Plugin report tree of note'
        ];
    }

    public static function version()
    {
        return '0.0.1';
    }

    public static function install( IApp $app)
    {
        $container = $app->getContainer();
        Loader::findClass( 
            SPT_PLUGIN_PATH. 'psol/report_tree/entities', 
            'App\psol\report_tree\entities', 
            function($classname, $fullname) use (&$container)
            {
                $x = new $fullname($container->get('query'));
                $x->checkAvailability();
            });

        return true;
    }
    public static function uninstall( IApp $app)
    {
        // run sth to uninstall
        $container = $app->getContainer();
        Loader::findClass( 
            SPT_PLUGIN_PATH. 'psol/report_tree/entities', 
            'App\psol\report_tree\entities', 
            function($classname, $fullname) use (&$container)
            {
                $x = new $fullname($container->get('query'));
                $x->dropTable();
            });

        return true;
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