<?php
/**
 * SPT software - Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic model
 * 
 */

namespace App\plugins\convert_tool\models;

use SPT\Container\Client as Base;
use SPT\Support\Loader;

class ConvertToolModel extends Base
{ 
    public function convertDataNotes()
    {
        $where[] = "type = 'table'";
        $table_notes = $this->NoteEntity->list(0 , 0, $where);

        foreach($table_notes as $note)
        {
            $col_headers = [];
            $data_table = [];
            $data =json_decode($note['data']);
            $col_number = 0;

            if(is_array($data) && isset($data[0]->title)){
                foreach ($data as $index_col=> $col)
                {
                    $col_headers[] = $col->title;
                    foreach ($col->features as $index_row=>$row)
                    {
                        $data_table[$index_col][$index_row] = $row->content;
                    }
                    $temp = count($col_headers) < count($col->features) ? count($col->features) : count($col_headers);
                    $col_number = $col_number < $temp ? $temp : $col_number;
                }
            }



            for ($i=0;$i<$col_number;$i++)
            {
                if (!array_key_exists($i, $col_headers)) {
                    array_push($col_headers, '');
                }
            }
            
            $new_data = array(
                "colHeaders" => $col_headers,
                "data" => $data_table
            );
            $note['data'] = json_encode($new_data);
            
            $try = $this->NoteEntity->update($note);
            if (!$try)
            {
                $this->error = $this->NoteEntity->getError();
                return false;
            }
        }
        return true;
    }
}
