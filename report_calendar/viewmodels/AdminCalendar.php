<?php

/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A simple View Model
 * 
 */

namespace App\psol\report_calendar\viewmodels;

use SPT\Web\ViewModel;
use SPT\Web\Gui\Form;

class AdminCalendar extends ViewModel
{
    public static function register()
    {
        return [
            'layout'=>'backend.report.form'
        ];
    }

    private function getItem()
    {
        $urlVars = $this->request->get('urlVars');
        $id = $urlVars && isset($urlVars['id']) ? (int) $urlVars['id'] : 0;

        $data = $this->CalendarModel->getDetail($id);
        $data_form = $this->session->getform('report_calendar', []);
        $this->session->setform('report_calendar', []);
        $data = $data_form ? $data_form : $data;

        return $data;
    }

    public function form()
    {
        $data = $this->getItem();
        $data = $data ? $data : [];
        $id  = $data && isset($data['id']) ? $data['id'] : '';
        $filter_tag = isset($data['filter_tag']) ? $data['filter_tag'] : [];
        $form = new Form($this->getFormFields(), $data);
        $first_day_this_month = date('01-m-Y');
        $last_day_this_month  = date('t-m-Y');
        $start_date = strtotime("sunday -1 week", strtotime($first_day_this_month));
        $end_date = strtotime("saturday 0 week", strtotime($last_day_this_month));

        $days = [];
        $date = $start_date;
        while($date <= $end_date)
        {
            $event = [];
            foreach($data['requests'] as $request)
            {
                $tmp_start = strtotime(date('d-m-Y', strtotime($request['start_at'])));
                $tmp_end = strtotime(date('d-m-Y', strtotime($request['finished_at'])));

                if ($date >= $tmp_start && $date <= $tmp_end)
                {
                    $status = $date == $tmp_start ? 'start' : ($tmp_end == $date ? 'end' : '');
                    $request['status'] = $status;
                    $event[] = $request;
                }
            }
            $days[] = [
                'event' => $event,
                'date' => $date,
            ];

            $date += 86400;
        }

        return [
            'id' => $id,
            'form' => $form,
            'data' => $data,
            'start_date' => $start_date,
            'days' => $days,
            'end_date' => $end_date,
            'filter_tag' => $filter_tag,
            'title_page_edit' => $data && $id && $data['title'] ? $data['title'] : 'New Diagrams',
            'url' => $this->router->url(),
            'link_request' => $this->router->url('report/find-request'),
            'link_note' => $this->router->url('note-detail'),
            'link_list' => $this->router->url('reports'),
            'link_request' => $this->router->url('detail-request'),
            'link_ajax' => $this->router->url('calendar/ajax'),
            'link_tag' => $this->router->url('tag/search'),
            'link_form' => $id ? $this->router->url('report/detail') : $this->router->url('new-report/calendar'),
            'link_search' => $this->router->url('note/search'),
        ];
        
    }

    public function getFormFields()
    {
        $milestones = $this->MilestoneEntity->list(0, 0);
        $option_milestone = [];
        foreach ($milestones as $milestone) 
        {
            $option_milestone[] = [
                'text' => $milestone['title'],
                'value' => $milestone['id'],
            ];
        }

        $fields = [
            'title' => [
                'text',
                'showLabel' => false,
                'placeholder' => 'New Title',
                'formClass' => 'form-control border-0 border-bottom fs-2 py-0',
                'required' => 'required',
            ],
            'milestone' => [
                'option',
                'type' => 'multiselect',
                'default' => '0',
                'formClass' => '',
                'placeholder' => 'All Milestone',
                'options' => $option_milestone,
                'showLabel' => false
            ],
            'tags' => [
                'option',
                'type' => 'multiselect',
                'default' => '0',
                'formClass' => '',
                'placeholder' => 'All Tag',
                'options' => [],
                'showLabel' => false
            ],
            'token' => ['hidden',
                'default' => $this->container->get('token')->value(),
            ],
        ];

        return $fields;
    }
}
