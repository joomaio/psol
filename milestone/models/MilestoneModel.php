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

class MilestoneModel extends Base 
{ 
    use \SPT\Traits\ErrorString;

    // Write your code here
    public function remove($id)
    {
        $requests = $this->RequestEntity->list(0, 0, ['milestone_id = '. $id]);
        $try = $this->MilestoneEntity->remove($id);
        if ($try)
        {
            foreach ($requests as $request)
            {
                $this->RequestModel->remove($request['id']);
            }
        }
        return $try;
    }   

    public function add($data)
    {
        $data = $this->MilestoneEntity->bind($data);

        if (!$data || !isset($data['readyNew']) || !$data['readyNew'])
        {
            $this->error = $this->MilestoneEntity->getError();
            return false;
        }
        unset($data['readyNew']);

        $newId =  $this->MilestoneEntity->add($data);
        if (!$newId)
        {
            $this->error = $this->MilestoneEntity->getError();
            return false;
        }

        return $newId;
    }

    public function update($data)
    {
        $data = $this->MilestoneEntity->bind($data);

        if (!$data || !isset($data['readyUpdate']) || !$data['readyUpdate'])
        {
            $this->error = $this->MilestoneEntity->getError();
            return false;
        }
        unset($data['readyUpdate']);

        $try = $this->MilestoneEntity->update($data);
        if (!$try)
        {
            $this->error = $this->MilestoneEntity->getError();
            return false;
        }

        return $try;
    }
}
