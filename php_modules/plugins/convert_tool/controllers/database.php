<?php
/**
 * SPT software - homeController
 *
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic controller
 *
 */

namespace App\plugins\convert_tool\controllers;

use SPT\Response;
use SPT\Web\ControllerMVVM;

class database extends ControllerMVVM
{
    public function convert_data_notes()
    {
        $try = $this->ConvertToolModel->convertDataNotes();
        if ($try) 
        {
            echo "Convert data note table successfully.\n";
        } else
        {
            echo $this->DbToolModel->getError(). "\n";
            echo "Convert data note table failed.";
        }
        return;
    }
}