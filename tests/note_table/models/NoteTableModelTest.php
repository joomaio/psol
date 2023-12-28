<?php
namespace Tests\note_table\models;

use Tests\Test as TestCase;

class NoteTableModelTest extends TestCase
{
    private $NoteTableModel;
    static $data;

    protected function setUp(): void
    {
        $app = $this->prepareApp();
        $container = $app->getContainer();

        $this->NoteTableModel = $container->get('NoteTableModel');
        $NoteEntity = $container->get('NoteEntity');
        $HistoryEntity = $container->get('HistoryEntity');

        if (!static::$data)
        {
            $history = $HistoryEntity->findByPK(1);
            if (!$history)
            {
                $try = $HistoryEntity->add([
                    'id' => 1,
                    'object_id' => 1,
                    'object' => 'request',
                    'data' => '{"colHeaders":["test"],"data":[["test"]]}',
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

    public function testReplaceContent()
    {
        $try = $this->NoteTableModel->replaceContent('Test');
        $this->assertIsString($try);
    }

    /**
     * @dataProvider dataAdd
     */
    public function testAdd($data, $result)
    {
        $try = $this->NoteTableModel->add($data);
        $this->assertEquals($try , $result);
    }

    public function dataAdd()
    {
        return [
            [[
                'title' => '', 
                'data' => '{"colHeaders":["test"],"data":[["test"]]}',
                'tags' => [], 
                'notice' => '', 
                'status' => 0, 
            ], false],
            [[
                'title' => 'test table', 
                'data' => '{"colHeaders":["test"],"data":[["test"]]}',
                'tags' => [], 
                'notice' => '', 
                'status' => 0, 
            ], true],
        ];
    }

    /**
     * @dataProvider dataUpdate
     */
    public function testUpdate($data, $result)
    {
        $try = $this->NoteTableModel->update($data);
        $this->assertEquals($try , $result);
    }

    public function dataUpdate()
    {
         return [
            [[
                'id' => 0,
                'title' => 'test table', 
                'data' => 'test table', 
                'structure' => '{"colHeaders":["test"],"data":[["test"]]}',
                'tags' => [], 
                'notice' => '', 
                'status' => 0, 
            ], false],
            [[
                'id' => 1,
                'title' => '', 
                'data' => 'test table', 
                'structure' => '{"colHeaders":["test"],"data":[["test"]]}',
                'tags' => [], 
                'notice' => '', 
                'status' => 0,          
            ], false],
            [[
                'id' => 1,
                'title' => 'test table', 
                'data' => 'test table', 
                'structure' => '{"colHeaders":["test"],"data":[["test"]]}',
                'tags' => [], 
                'notice' => '', 
                'status' => 0, 
            ], true],
        ];
    }

    /**
     * @dataProvider dataGetDetail
     */
    public function testGetDetail($id, $result)
    {
        $try = $this->NoteTableModel->getDetail($id);
        $try = is_array($try) ? true : false;
        $this->assertEquals($try , $result);
    }

    public function dataGetDetail()
    {
        return [
            [0, true],
            [1, true],
        ];
    }

    /**
     * @dataProvider dataRollback
     */
    public function testRollback($id, $result)
    {
        $try = $this->NoteTableModel->rollback($id);
        $this->assertEquals($try , $result);
    }

    public function dataRollback()
    {
        return [
            [0, false],
            [1, true],
        ];
    }

    /**
     * @dataProvider dataSearch
     */
    public function testSearch($search, $ignore, $result)
    {
        $try = $this->NoteTableModel->search($search, $ignore);
        $this->assertEquals($try , $result);
    }

    public function dataSearch()
    {
        return [
            ['test', 'XYZ', []],
        ];
    }

    /**
     * @dataProvider dataRemove
     */
    public function testRemove($id, $result)
    {
        $try = $this->NoteTableModel->remove($id);
        $this->assertEquals($try , $result);
    }

    public function dataRemove()
    {
        return [
            [0, false],
            [1, true],
        ];
    }
}