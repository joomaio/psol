<?php
namespace Tests\note_table\viewmodels;

use App\plugins\note_table\viewmodels\TableWidget;
use Tests\Test as TestCase;

class TableWidgetTest extends TestCase
{
    private $TableWidget;

    protected function setUp(): void
    {
        $app = $this->prepareApp();
        $container = $app->getContainer();
        $request = $container->get('request');
        $request->set('urlVars', ['id' => 1]);

        $this->TableWidget = new TableWidget($container);
    }

    public function testPreview()
    {
        $layoutData = array();
        $viewData = array(
            'currentId' => 1
        );
        $try = $this->TableWidget->preview($layoutData, $viewData);

        $this->assertIsArray($try);
    }
}