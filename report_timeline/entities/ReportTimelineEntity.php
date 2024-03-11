<?php
/**
 * SPT software - Entity
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic entity
 * 
 */

namespace App\psol\report_timeline\entities;

use SPT\Storage\DB\Entity;

class ReportTimelineEntity extends Entity
{
    protected $table = '#__report_timeline';
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
            'milestones' => [
                'type' => 'text',
            ],
            'tags' => [
                'type' => 'text',
            ],
            'report_id' => [
                'type' => 'int',
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

        if(empty($data['report_id'])) 
        {
            $this->error = "Report invalid";
            return false;
        }

        if(is_array($data['milestones'])) 
        {
            $data['milestones'] = json_encode($data['milestones']);
        } 

        if(is_array($data['tags'])) 
        {
            $data['tags'] = json_encode($data['tags']);
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
        $skips = isset($data['id']) && $data['id'] ? [] : ['id'];
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