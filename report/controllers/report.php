<?php namespace App\psol\report\controllers;

use SPT\Web\ControllerMVVM;
use SPT\Response;

class report extends ControllerMVVM 
{
    public function list()
    {
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
        $this->app->set('layout', 'backend.report.list');
    }

    public function updateStatus()
    {
        $id = $this->request->post->get('id', '', 'string');
        $find = $this->ReportEntity->findByPK($id);
        if (!$find)
        {
            $this->session->set('flashMsg', 'Invalid Report');
            return $this->app->redirect(
                $this->router->url('reports'),
            );
        }

        $try = $this->ReportModel->updateStatus([
            'id' => $id,
            'status' => $find['status'] ? 0 : 1,
        ]);

        $msg = $try ? 'Update Successfull' : 'Update Fail';
       
        $this->session->set('flashMsg', $msg);
        return $this->app->redirect(
            $this->router->url('reports'),
        );
    }

    public function update()
    {
        $ids = $this->validateID(); 
        $link = 'reports';

        if(is_numeric($ids) && $ids)
        {
            $data = [
                'title' => $this->request->post->get('title', '', 'string'),
                'id' => $ids,
                'modified_by' => $this->user->get('id'),
                'modified_at' => date('Y-m-d H:i:s')
            ];
            $assignment = $this->request->post->get('assignment', [], 'array');

            $data['assignment'] = "[" . implode(",", $assignment) . "]";
            $try = $this->ReportEntity->update($data);
            
            $msg = $try ? 'Edit Successfully!' : 'Error: '. $this->ReportEntity->getError();
            $this->session->set('flashMsg', $msg);
            return $this->app->redirect(
                $this->router->url($link)
            );
        }
    }

    public function delete()
    {
        $ids = $this->validateID();
        $count = 0;
        if( is_array($ids))
        {
            foreach($ids as $id)
            {
                //Delete file in source
                $try = $this->ReportModel->remove($id);
                if ($try)
                {
                    $count ++;
                }
            }
        }
        elseif( is_numeric($ids) )
        {
            $id = $ids;
            $try = $this->ReportModel->remove($id);
            if ($try)
            {
                $count ++;
            }
        }  
        

        $this->session->set('flashMsg', $count.' deleted record(s)');
        return $this->app->redirect(
            $this->router->url('reports'), 
        );
    }

    public function validateID()
    {
        $urlVars = $this->request->get('urlVars');
        $id = $urlVars ? (int) $urlVars['id'] : [];

        if(empty($id))
        {
            $ids = $this->request->post->get('ids', [], 'array');
            if(count($ids)) return $ids;

            $this->session->set('flashMsg', 'Invalid Report');
            return $this->app->redirect(
                $this->router->url('reports'),
            );
        }

        return $id;
    }

    public function findUser()
    {
        $search = trim($this->request->get->get('search', '', 'string'));

        $where = [];

        if( !empty($search) )
        {
            $query_search = "(`name` LIKE '%".$search."%' ) OR (`email` LIKE '%".$search."%' ) OR (`username` LIKE '%".$search."%' )";
        }

        $user_access = $this->PermissionModel->getAccessByUser();

        if (in_array('user_manager', $user_access)) {
            $where[] = $query_search;
        } else {
            $group_ids = $this->UserGroupEntity->list(0,0,['user_id = ' . $this->user->get('id')]);
            $group_id_arr = '(';
            foreach ($group_ids as $idx => $group) {
                if ($idx != 0) {
                    $group_id_arr .= ',';
                }
                $group_id_arr .= $group['group_id'];
            }
            $group_id_arr .= ')';

            $user_ids = $this->UserGroupEntity->list(0,0,['group_id IN ' . $group_id_arr]);
            $user_id_arr = '(';
            if (is_array($user_ids)) {
                foreach ($user_ids as $idx => $user) {
                    if ($idx != 0) {
                        $user_id_arr .= ',';
                    }
                    $user_id_arr .= $user['user_id'];
                }
            }
            $user_id_arr .= ')';
            $where[] = "(`id` IN ". $user_id_arr .") AND (". $query_search .")";
        }
        $users = $this->UserEntity->list(0, 0, $where);
        
        $this->app->set('format', 'json');
        $this->set('status' , 'success');
        $this->set('data' , $users);
        $this->set('message' , '');
        return;
    }
}