<?php
/**
 * SPT software - Entity
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic entity
 * 
 */

namespace App\psol\report\entities;

use SPT\Storage\DB\Entity;

class ReportEntity extends Entity
{
    protected $table = '#__reports';
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
            'status' => [
                'type' => 'tinyint',
            ],
            'type' => [
                'type' => 'varchar',
                'limit' => 255,
            ],
            'data' => [
                'type' => 'text',
                'null' => 'YES',
            ],
            'assignment' => [
                'type' => 'text',
                'null' => 'YES',
            ],
            'created_at' => [
                'type' => 'datetime',
                'default' => 'NOW()',
            ],
            'created_by' => [
                'type' => 'int',
                'option' => 'unsigned',
            ],
            'modified_at' => [
                'type' => 'datetime',
                'default' => 'NOW()',
            ],
            'modified_by' => [
                'type' => 'int',
                'option' => 'unsigned',
            ],
        ];
    }

    public function validate( $data )
    {
        if (!$data || !is_array($data))
        {
            $this->error = "Data invalid format";
            return false;
        }

        if(empty($data['title'])) 
        {
            $this->error = "Title can't empty";
            return false;
        }

        unset($data['readyUpdate']);
        unset($data['readyNew']);
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