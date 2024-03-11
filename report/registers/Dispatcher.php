<?php
namespace App\psol\report\registers;

use SPT\Application\IApp;
use SPT\Response;
use App\psol\report\libraries\ReportDispatch;

class Dispatcher
{
    public static function dispatch(IApp $app)
    {
        // Check Permission
        $app->plgLoad('permission', 'CheckSession');
        
        $reportDispatcher = new ReportDispatch($app->getContainer());
        $reportDispatcher->execute();
    }
}