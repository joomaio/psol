<?php
/**
 * SPT software - Entity
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic entity
 * 
 */

namespace App\psol\milestone\entities;

use SPT\Storage\DB\Entity;

class RequestEntity extends Entity
{
    protected $table = '#__requests';
    protected $pk = 'id';

    public function getFields()
    {
        return [
                'id' => [
                    'type' => 'int',
                    'pk' => 1,
                    'option' => 'unsigned',
                    'extra' => 'auto_increment',
                ],
                'milestone_id' => [
                    'type' => 'int',
                    'option' => 'unsigned',
                ],
                'title' => [
                    'type' => 'varchar',
                    'limit' => 255,
                ],
                'start_at' => [
                    'type' => 'datetime',
                    'null' => 'YES',
                ],
                'tags' => [
                    'type' => 'text',
                    'null' => 'YES',
                ],
                'assignment' => [
                    'type' => 'text',
                    'null' => 'YES',
                ],
                'description' => [
                    'type' => 'text',
                    'null' => 'YES',
                ],
                'deadline_at' => [
                    'type' => 'datetime',
                    'null' => 'YES',
                ],
                'finished_at' => [
                    'type' => 'datetime',
                    'null' => 'YES',
                ],
                'created_at' => [
                    'type' => 'datetime',
                    'null' => 'YES',
                ],
                'created_by' => [
                    'type' => 'int',
                    'option' => 'unsigned',
                ],
                'modified_at' => [
                    'type' => 'datetime',
                    'null' => 'YES',
                ],
                'modified_by' => [
                    'type' => 'int',
                    'option' => 'unsigned',
                ],
        ];
    }

    public function toggleStatus( $id, $action)
    {
        $item = $this->findByPK($id);
        return $this->db->table( $this->table )->update([
            'status' => $status,
        ], ['id' => $id ]);
    }

    public function validate($data)
    {
        if (!$data || !is_array($data))
        {
            return false;
        }

        if (!$data['milestone_id'])
        {
            $this->error = 'Invalid Milestone';
            return false;
        }

        if (!$data['title'])
        {
            $this->error = 'Error: Title can\'t empty! ';
            return false;
        }

        $data['assignment'] = $data['assignment'] ? json_encode($data['assignment']) : '';
        $data['start_at'] = $data['start_at'] ? $data['start_at'] : null;
        $data['finished_at'] = $data['finished_at'] ? $data['finished_at'] : null;
        $data['deadline_at'] = $data['deadline_at'] ? $data['deadline_at'] : null;
        
        return $data;
    }

    public function bind($data = [], $returnObject = false)
    {
        $row = [];
        $data = (array) $data;
        $fields = $this->getFields();
        $skips = isset($data['id']) && $data['id'] ? ['created_at', 'created_by'] : ['id'];
        foreach ($fields as $key => $field)
        {
            if (!in_array($key, $skips))
            {
                $default = isset($field['default']) ? $field['default'] : '';
                $row[$key] = isset($data[$key]) ? $data[$key] : $default;
            }
        }

        if (isset($data['id']) && $data['id'])
        {
            $row['readyUpdate'] = true;
        }
        else{
            $row['readyNew'] = true;
        }

        return $returnObject ? (object)$row : $row;
    }
}