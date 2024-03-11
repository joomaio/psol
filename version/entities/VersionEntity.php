<?php
/**
 * SPT software - Entity
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic entity
 * 
 */

namespace App\psol\version\entities;

use SPT\Storage\DB\Entity;

class VersionEntity extends Entity
{
    protected $table = '#__versions'; //table name
    protected $pk = 'id'; //primary key

    public function getFields()
    {
        return [
            'id' => [
                'type' => 'int',
                'pk' => 1,
                'option' => 'unsigned',
                'extra' => 'auto_increment',
            ],
            'name' => [
                'type' => 'varchar',
                'limit' => 255,
            ],
            'version' => [
                'type' => 'text',
            ],
            'release_date' => [
                'type' => 'datetime',
                'null' => 'YES',
            ],
            'status' => [
                'type' => 'tinyint',
            ],
            'description' => [
                'type' => 'text',
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

    public function validate( $data )
    {
        if (!$data || !is_array($data))
        {
            $this->error = "Data invalid format";
            return false;
        }

        if(empty($data['name'])) 
        {
            $this->error = "title can't empty";
            return false;
        }

        if($data['release_date'] == '')
        {
            $data['release_date'] = null;
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