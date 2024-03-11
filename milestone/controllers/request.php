<?php namespace App\psol\milestone\controllers;

use SPT\Web\ControllerMVVM;
use SPT\Response;

class request extends ControllerMVVM 
{
    public function detail()
    {
        
        $urlVars = $this->request->get('urlVars');
        $id = (int) $urlVars['id'];
        
        $milestone_id = $this->validateMilestoneID();
        $exist = $this->RequestEntity->findByPK($id);
        if(!empty($id) && !$exist) 
        {
            $this->session->set('flashMsg', "Invalid Request");
            return $this->app->redirect(
                $this->router->url('requests/'. $milestone_id)
            );
        }
        $this->app->set('layout', 'backend.request.form');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function detail_request()
    {
        
        $urlVars = $this->request->get('urlVars');
        $id = (int) $urlVars['request_id'];
        
        $exist = $this->RequestEntity->findByPK($id);

        if(!empty($id) && !$exist) 
        {   
            $this->session->set('flashMsg', "Invalid Request");
            return $this->app->redirect(
                $this->router->url('milestones/')
            );
        }

        $this->app->set('layout', 'backend.request.detail_request');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function list()
    {
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
        $this->app->set('layout', 'backend.request.list');
    }

    public function add()
    {
        $milestone_id = $this->validateMilestoneID();

        $data = [
            'title' => $this->request->post->get('title', '', 'string'),
            'tags' => $this->request->post->get('tags', '', 'string'),
            'description' => $this->request->post->get('description', '', 'string'),
            'start_at' => $this->request->post->get('start_at', '', 'string'),
            'finished_at' => $this->request->post->get('finished_at', '', 'string'),
            'assignment' => $this->request->post->get('assignment', [], 'array'),
            'milestone_id' => $milestone_id,
            'created_by' => $this->user->get('id'),
            'created_at' => date('Y-m-d H:i:s'),
            'modified_by' => $this->user->get('id'),
            'modified_at' => date('Y-m-d H:i:s')
        ];

        $try = $this->RequestModel->add($data);

        $msg = $try ? 'Create Successfully!' : 'Error: '. $this->RequestModel->getError();
        $this->session->set('flashMsg', $msg);
        
        return $this->app->redirect(
            $this->router->url('requests/'. $milestone_id)
        );
    }

    public function update()
    {
        $ids = $this->validateID(); 
        $milestone_id = $this->validateMilestoneID();
        // TODO valid the request input
        $detail_request =  $this->request->post->get('detail_request', '', 'string');
        $link = $detail_request ? 'detail-request/'. $ids : 'requests/'. $milestone_id;

        if(is_numeric($ids) && $ids)
        {
            $data = [
                'title' => $this->request->post->get('title', '', 'string'),
                'tags' => $this->request->post->get('tags', '', 'string'),
                'description' => $this->request->post->get('description', '', 'string'),
                'start_at' => $this->request->post->get('start_at', '', 'string'),
                'finished_at' => $this->request->post->get('finished_at', '', 'string'),
                'assignment' => $this->request->post->get('assignment', [], 'array'),
                'milestone_id' => $milestone_id,
                'id' => $ids,
                'modified_by' => $this->user->get('id'),
                'modified_at' => date('Y-m-d H:i:s')
            ];

            $try = $this->RequestModel->update($data);
            
            $msg = $try ? 'Edit Successfully!' : 'Error: '. $this->RequestModel->getError();
            $this->session->set('flashMsg', $msg);
            return $this->app->redirect(
                $this->router->url($link)
            );
        }
    }

    public function delete()
    {
        $ids = $this->validateID();
        $milestone_id = $this->validateMilestoneID();
        $count = 0;
        if( is_array($ids))
        {
            foreach($ids as $id)
            {
                //Delete file in source
                if( $this->RequestEntity->remove( $id ) )
                {
                    $count++;
                }
            }
        }
        elseif( is_numeric($ids) )
        {
            if( $this->RequestEntity->remove($ids ) )
            {
                $count++;
            }
        }  
        

        $this->session->set('flashMsg', $count.' deleted record(s)');
        return $this->app->redirect(
            $this->router->url('requests/'. $milestone_id), 
        );
    }

    public function validateID()
    {
        $milestone_id = $this->validateMilestoneID();
        $urlVars = $this->request->get('urlVars');
        $id = $urlVars ? (int) $urlVars['id'] : 0;

        if(empty($id))
        {
            $ids = $this->request->post->get('ids', [], 'array');
            if(count($ids)) return $ids;

            $this->session->set('flashMsg', 'Invalid request');
            return $this->app->redirect(
                $this->router->url('requests/'. $milestone_id),
            );
        }

        return $id;
    }

    public function validateMilestoneID()
    {
        
        $urlVars = $this->request->get('urlVars');

        $id = (int) $urlVars['milestone_id'];
        if(empty($id))
        {
            $this->session->set('flashMsg', 'Invalid Milestone');
            return $this->app->redirect(
                $this->router->url('milestones'),
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