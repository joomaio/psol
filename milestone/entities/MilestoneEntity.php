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

class MilestoneEntity extends Entity
{
    protected $table = '#__milestones';
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
                'title' => [
                    'type' => 'varchar',
                    'limit' => 255,
                ],
                'start_date' => [
                    'type' => 'datetime',
                    'null' => 'YES',
                ],
                'end_date' => [
                    'type' => 'datetime',
                    'null' => 'YES',
                ],
                'description' => [
                    'type' => 'text',
                    'null' => 'YES',
                ],
                'status' => [
                    'type' => 'tinyint',
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

        if (!$data['title']) 
        {
            $this->error = 'Error: Title can\'t empty! ';
            return false;
        }

        if ($data['start_date'] == '')
            $data['start_date'] = NULL;
        if ($data['end_date'] == '')
            $data['end_date'] = NULL;

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