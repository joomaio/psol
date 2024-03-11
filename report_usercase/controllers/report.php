<?php
namespace App\psol\report_usercase\controllers;

use App\psol\report\libraries\ReportController;

class report extends ReportController 
{
    public function newform()
    {
        $this->app->set('layout', 'backend.form');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function detail()
    {
        $this->app->set('layout', 'backend.form');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function preview()
    {
        $this->app->set('layout', 'backend.preview');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }


    public function list()
    {
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
        $this->app->set('layout', 'backend.list');
    }

    public function add()
    {
        //check title sprint
        $save_close = $this->request->post->get('save_close', '', 'string');
        $data = [
            'title' => $this->request->post->get('title', '', 'string'),
            'note_diagrams' => $this->request->post->get('note_diagrams', [], 'array'),
            'note_description' => $this->request->post->get('note_description', [], 'array'),
        ];

        $try = $this->UserCaseModel->add($data);
        if(!$try)
        {
            $msg = 'Error: '. $this->UserCaseModel->getError();
            $link = 'new-report/usercase';
        }
        else
        {
            $msg = 'Created Successfully!';
            $link = $save_close ? 'reports' : 'report/detail/'. $try;
        }
        
        $this->session->set('flashMsg', $msg);
        return $this->app->redirect(
            $this->router->url($link)
        );
    }

    public function update()
    {
        $ids = $this->validateID();

        // TODO valid the request input

        if(is_numeric($ids) && $ids)
        {
            $save_close = $this->request->post->get('save_close', '', 'string');
            $data = [
                'title' => $this->request->post->get('title', '', 'string'),
                'note_diagrams' => $this->request->post->get('note_diagrams', [], 'array'),
                'note_description' => $this->request->post->get('note_description', [], 'array'),
                'id' => $ids,
            ];

            $try = $this->UserCaseModel->update($data);
            if(!$try)
            {
                $msg = 'Error: '. $this->UserCaseModel->getError();
                $link = 'report/detail/'. $ids;
            }
            else
            {
                $msg = 'Created Successfully!';
                $link = $save_close ? 'reports' : 'report/detail/'. $ids;
            }
            
            $this->session->set('flashMsg', $msg);
            return $this->app->redirect(
                $this->router->url($link)
            );
        }
    }

    public function validateID()
    {

        $urlVars = $this->request->get('urlVars');
        $id = (int) $urlVars['id'];

        if(empty($id))
        {
            $ids = $this->request->post->get('ids', [], 'array');
            if(count($ids)) return $ids;

            $this->session->set('flashMsg', 'Invalid note diagram');
            return $this->app->redirect(
                $this->router->url('reports'),
            );
        }

        return $id;
    }
}
