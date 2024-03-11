<?php
/**
 * SPT software - homeController
 *
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic controller
 *
 */

namespace App\psol\report_calendar\controllers;

use App\psol\report\libraries\ReportController;
use SPT\Web\ControllerMVVM;

class report extends ReportController 
{
    public function newform()
    {
        $this->app->set('layout', 'backend.report.form');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }
    
    public function detail()
    {
        $this->app->set('layout', 'backend.report.form');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function preview()
    {
        $this->app->set('layout', 'backend.report.preview');
        $this->app->set('page', 'backend');
        $this->app->set('format', 'html');
    }

    public function add()
    {
        //check title sprint
        $save_close = $this->request->post->get('save_close', '', 'string');
        
        $data = [
            'title' => $this->request->post->get('title', '', 'string'),
            'status' => 1,
            'milestone' => $this->request->post->get('milestone', [], 'array'),
            'tags' => $this->request->post->get('tags', [], 'array'),
        ];

        $try = $this->CalendarModel->add($data);
        
        if( !$try )
        {
            $this->session->setform('report_calendar', $data);
            $this->session->set('flashMsg', $this->CalendarModel->getError());
            return $this->app->redirect(
                $this->router->url('new-report/calendar')
            );
        }
        else
        {
            // save struct
            $this->session->set('flashMsg', 'Created Successfully!');
            $link = $save_close ? 'reports' : 'report/detail/'. $try;
            return $this->app->redirect(
                $this->router->url($link)
            );
        }
    }

    public function update()
    {
        $id = $this->validateID();

        // TODO valid the request input
        $save_close = $this->request->post->get('save_close', '', 'string');

        $data = [
            'id' => $id,
            'title' => $this->request->post->get('title', '', 'string'),
            'status' => 1,
            'milestone' => $this->request->post->get('milestone', [], 'array'),
            'tags' => $this->request->post->get('tags', [], 'array'),
        ];

        $try = $this->CalendarModel->update($data);
        
        if($try)
        {
            $this->session->set('flashMsg', 'Updated successfully');
            $link = $save_close ? 'reports' : 'report/detail/'. $id;
            return $this->app->redirect(
                $this->router->url($link)
            );
        }
        else
        {
            $this->session->set('flashMsg', $this->CalendarModel->getError());
            return $this->app->redirect(
                $this->router->url('report/detail/'. $id)
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
                if( $this->CalendarModel->remove($id) )
                {
                    $count++;
                }
            }
        }
        elseif( is_numeric($ids) )
        {
            if( $this->CalendarModel->remove($ids) )
            {
                $count++;
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
        $id = (int) $urlVars['id'];

        if(empty($id))
        {
            $ids = $this->request->post->get('ids', [], 'array');
            if(count($ids)) return $ids;

            $this->session->set('flashMsg', 'Invalid calendar diagram');
            return $this->app->redirect(
                $this->router->url('reports'),
            );
        }

        return $id;
    }
}
