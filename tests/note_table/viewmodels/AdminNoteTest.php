<?php
namespace Tests\note_table\viewmodels;

use App\plugins\note_table\viewmodels\AdminNote;
use Tests\Test as TestCase;

class AdminNoteTest extends TestCase
{
    private $AdminNote;
    static $data;

    protected function setUp(): void
    {
        $app = $this->prepareApp();
        $container = $app->getContainer();
        $request = $container->get('request');
        $request->set('urlVars', ['id' => 1]);

        $this->AdminNote = new AdminNote($container);
        $NoteEntity = $container->get('NoteEntity');
        $HistoryEntity = $container->get('HistoryEntity');

        if (!static::$data)
        {
            $history = $HistoryEntity->findByPK(1);
            if (!$history)
            {
                $try = $HistoryEntity->add([
                    'id' => 1,
                    'object_id' => 2,
                    'object' => 'request',
                    'data' => '',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 1,
                ]);
            }

            $note_table = $NoteEntity->findByPK(1);
            if(!$note_table)
            {
                $NoteEntity->add([
                    'title' => 'test table',
                    'public_id' => '',
                    'id' => 1,
                    'alias' => '',
                    'data' => '{"colHeaders":["test"],"data":[["test"]]}',
                    'tags' => '',
                    'type' => 'table',
                    'note_ids' => '',
                    'notice' => '',
                    'status' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => 0,
                    'locked_at' => date('Y-m-d H:i:s'),
                    'locked_by' => 0,
                ]);
            }
            static::$data = true;
        }
    }

    public function testForm()
    {
        $try = $this->AdminNote->form();

        $this->assertIsArray($try);
    }

    public function testHistory()
    {
        $try = $this->AdminNote->history();

        $this->assertIsArray($try);
    }

    public function testGetFormFields()
    {
        $try = $this->AdminNote->getFormFields();

        $this->assertIsArray($try);
    }

    public function testPreview()
    {
        $try = $this->AdminNote->preview();

        $this->assertIsArray($try);
    }
}