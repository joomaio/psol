<?php
namespace App\psol\report_usercase\registers;

use SPT\Application\IApp;
use SPT\Support\Loader;

class Report
{
    public static function registerType( IApp $app )
    {
        $container = $app->getContainer();
        $router = $container->get('router');
        $permission = $container->exists('PermissionModel') ? $container->get('PermissionModel') : null;
        $allow = $permission ? $permission->checkPermission(['usercase_manager']) : true;
        if (!$allow)
        {
            return [];
        }
        
        return [
            'usercase' => [
                'title' => 'Usercase Diagram',
                'namespace' => 'App\psol\report_usercase\\',
                'remove_object' => 'UserCaseModel',
            ],
        ];
    }
}