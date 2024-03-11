<?php namespace App\psol\version\controllers;

use SPT\Web\ControllerMVVM;
use SPT\Response;

class version extends ControllerMVVM 
{
    public function detail()
    {
        $urlVars = $this->request->get('urlVars');
        $id = (int) $urlVars['id'];

        $exist = $this->VersionEntity->findByPK($id);
        if(!empty($id) && !$exist) 
        {
            $this->session->set('flashMsg', "Invalid Version");
            return $this->app->redirect(
                $this->router->url('versions')
            );
        }
        $this->app->set('layout', 'backend.version.form');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function list()
    {
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
        $this->app->set('layout', 'backend.version.list');
    }

    public function add()
    {
        //check title sprint
        $data = [
            'name' => $this->request->post->get('name', '', 'string'),
            'release_date' => $this->request->post->get('release_date', '', 'string'),
            'description' => $this->request->post->get('description', '', 'string'),
            'status' => $this->request->post->get('status', 1, 'string'),
        ];

        $newId = $this->VersionModel->add($data);
        if( !$newId )
        {
            $this->session->setform('version', $data);
            $this->session->set('flashMsg', 'Error: '. $this->VersionModel->getError());
            return $this->app->redirect(
                $this->router->url('versions')
            );
        }
        else
        {
            $this->session->set('flashMsg', 'Created Successfully!');
            return $this->app->redirect(
                $this->router->url('versions')
            );
        }
    }

    public function update()
    {
        $ids = $this->validateID(); 
       
        // TODO valid the request input

        if( is_array($ids) && $ids != null)
        {
            $this->session->set('flashMsg', 'Invalid Version');
            return $this->app->redirect(
                $this->router->url('versions')
            );
        }
        if(is_numeric($ids) && $ids)
        {
            $data = [
                'name' => $this->request->post->get('name', '', 'string'),
                'release_date' => $this->request->post->get('release_date', '', 'string'),
                'description' => $this->request->post->get('description', '', 'string'),
                'status' => $this->request->post->get('status', 1, 'string'),
                'id' => $ids,
            ];
    
            $try = $this->VersionModel->update($data);
            
            if($try) 
            {
                $this->session->set('flashMsg', 'Updated Successfully');
                return $this->app->redirect(
                    $this->router->url('versions')
                );
            }
            else
            {
                $this->session->set('flashMsg', 'Error: '. $this->VersionModel->getError());
                return $this->app->redirect(
                    $this->router->url('versions')
                );
            }
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
                if( $this->VersionModel->remove( $id ) )
                {
                    $count++;
                }
            }
        }
        elseif( is_numeric($ids) )
        {
            if( $this->VersionModel->remove($ids ) )
            {
                $count++;
            }
        }  
        

        $this->session->set('flashMsg', $count.' deleted record(s)');
        return $this->app->redirect(
            $this->router->url('versions'), 
        );
    }

    public function validateID()
    {
        
        $urlVars = $this->request->get('urlVars');
        $id = $urlVars ? (int) $urlVars['id'] : 0;

        if(empty($id))
        {
            $ids = $this->request->post->get('ids', [], 'array');
            if(count($ids)) return $ids;

            $this->session->set('flashMsg', 'Invalid Version');
            return $this->app->redirect(
                $this->router->url('versions'),
            );
        }

        return $id;
    }

}