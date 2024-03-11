<?php
/**
 * SPT software - Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic model
 * 
 */

namespace App\psol\milestone\models;

use SPT\Container\Client as Base;

class DocumentModel extends Base 
{ 
    use \SPT\Traits\ErrorString;

    // Write your code here
    public function remove($id)
    {
        $try = $this->DocumentEntity->remove($id);

        return $try;
    }   

    public function save($data)
    {
        $find = $this->DocumentEntity->findOne(['request_id' => $data['request_id']]);
        if ($find)
        {
            $data['id'] = $find['id'];
        }

        $data = $this->DocumentEntity->bind($data);
        if (!$data)
        {
            $this->error = $this->DocumentEntity->getError();
            return false;
        }

        if ($find && $data['readyUpdate'])
        {
            $try = $this->DocumentEntity->update($data);
            $document_id = $find['id'];
        }
        else
        {
            $try =  $this->DocumentEntity->add($data);
            $document_id = $try;
        }

        if (!$try)
        {
            $this->error = $this->DocumentEntity->getError();
            return false;
        }
        return $try;
    }

    public function getHistory($request_id)
    {
        if (!$request_id)
        {
            return false;
        }

        $document = $this->DocumentEntity->findOne(['request_id' => $request_id]);
        if (!$document)
        {
            return false;
        }

        $list = $this->DocumentHistoryEntity->list(0 ,0 ,['document_id' => $document['id']], 'id DESC');
        if ($list)
        {
            foreach($list as &$item)
            {
                $user_tmp = $this->UserEntity->findByPK($item['modified_by']);
                if ($user_tmp)
                {
                    $item['modified_by'] = $user_tmp['name'];
                }
            }
        }
        
        return $list;
    }

    public function rollback($id)
    {
        $document = $this->HistoryModel->detail($id);
        if (!$document)
        {
            return false;
        }
        
        $find_document = $this->DocumentEntity->findOne(['request_id' => $document['object_id']]);
        if (!$find_document)
        {
            return false;
        }
        $find_document['description'] = $document['data'];

        $try = $this->DocumentEntity->update($find_document);

        if ($try)
        {
            $remove_list = $this->HistoryEntity->list(0, 0, ['id > '. $id, 'object_id = '. $document['object_id'], 'object' => 'request']);
            if ($remove_list)
            {
                foreach($remove_list as $item)
                {
                    $this->HistoryEntity->remove($item['id']);
                } 
            }
        }
        
        return $try ? $document : false;
    }
}
