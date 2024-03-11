<?php

/**
 * SPT software - ViewModel
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: A simple View Model
 * 
 */

namespace App\psol\report_timeline\viewmodels;

use SPT\Web\ViewModel;
use SPT\Web\Gui\Form;

class AdminTimeline extends ViewModel
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

        $data = $this->TimelineModel->getDetail($id);
        $data_form = $this->session->getform('report_timeline', []);
        $this->session->setform('report_timeline', []);
        $data = $data_form ? $data_form : $data;
        
        return $data;
    }

    public function form()
    {
        $data = $this->getItem();
        $data = $data ? $data : [];
        $id  = $data && isset($data['id']) ? $data['id'] : '';
        $rang_day = $data['rang_day'];
        $start_date = $rang_day[0];
        $end_date = $rang_day[1] + 24 * 60 * 60;
        $filter_tag = isset($data['filter_tag']) ? $data['filter_tag'] : [];
        $form = new Form($this->getFormFields(), $data);

        return [
            'id' => $id,
            'form' => $form,
            'data' => $data,
            'start_date' => $start_date,
            'filter_tag' => $filter_tag,
            'end_date' => $end_date,
            'title_page_edit' => $data && $id && $data['title'] ? $data['title'] : 'New Diagrams',
            'url' => $this->router->url(),
            'link_request' => $this->router->url('report/find-request'),
            'link_detail_request' => $this->router->url('detail-request'),
            'link_note' => $this->router->url('note-detail'),
            'link_list' => $this->router->url('reports'),
            'link_tag' => $this->router->url('tag/search'),
            'link_form' => $id ? $this->router->url('report/detail') : $this->router->url('new-report/timeline'),
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
