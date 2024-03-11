<?php
namespace App\psol\report_usercase\models;

use SPT\Container\Client as Base; 

class UserCaseModel extends Base
{ 
    use \SPT\Traits\ErrorString;

    public function remove($id)
    {
        // remove in tree structure
        $try = $this->ReportEntity->remove($id);
        return $try;
    }

    public function validate($data)
    {
        if (!$data)
        {
            $this->error = 'Invalid Data';
            return false;
        }

        if (!$data['title'])
        {
            $this->error = 'Title required empty!';
            return false;
        }

        return true;
    }

    public function add($data)
    {
        $try = $this->validate($data);
        if (!$try)
        {
            return false;
        }
        
        $config = json_encode([
            'note_diagrams' => $data['note_diagrams'],
            'note_description' => $data['note_description'],
        ]);

        $newId =  $this->ReportEntity->add([
            'title' => $data['title'],
            'status' => 1,
            'type' => 'usercase',
            'data' => $config,
            'created_by' => $this->user->get('id'),
            'created_at' => date('Y-m-d H:i:s'),
            'modified_by' => $this->user->get('id'),
            'modified_at' => date('Y-m-d H:i:s')
        ]);

        if (!$newId)
        {
            $this->error = 'Create report failed';
            return false;
        }

        return $newId;
    }

    public function update($data)
    {
        $try = $this->validate($data);
        if (!$try)
        {
            return false;
        }
        
        $config = json_encode([
            'note_diagrams' => $data['note_diagrams'],
            'note_description' => $data['note_description'],
        ]);

        $try =  $this->ReportEntity->update([
            'title' => $data['title'],
            'data' => $config,
            'modified_by' => $this->user->get('id'),
            'modified_at' => date('Y-m-d H:i:s'),
            'id' => $data['id']
        ]);

        if (!$try)
        {
            $this->error = 'Update report failed';
            return false;
        }

        return $try;
    }

    public function getDetail($id)
    {
        if(!$id)
        {
            return false;
        }

        $detail = $this->ReportEntity->findByPK($id);
        if (!$detail)
        {
            return false;
        }

        $config = $detail['data'] ? json_decode($detail['data'], true) : [];
        $note_diagrams = $config && isset($config['note_diagrams']) ? $config['note_diagrams'] : [];
        $note_description = $config && isset($config['note_description']) ? $config['note_description'] : [];
        $detail['note_diagrams'] = $detail['note_description'] = [];

        foreach($note_diagrams as $note)
        {
            $tmp = $this->NoteEntity->findByPK($note);
            if($tmp && $tmp['status'] > -1)
            {
                $detail['note_diagrams'][] = $tmp;
            }
        }

        foreach($note_description as $note)
        {
            $tmp = $this->NoteEntity->findByPK($note);
            if($tmp && $tmp['status'] > -1)
            {
                $detail['note_description'][] = $tmp;
            }
        }

        return $detail;
    }
}
