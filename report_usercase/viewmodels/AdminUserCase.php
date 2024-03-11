<?php

namespace App\psol\report_usercase\viewmodels;

use SPT\Web\ViewModel;
use SPT\Web\Gui\Form;

class AdminUserCase extends ViewModel
{
    public static function register()
    {
        return [
            'layout' => [
                'backend.form',
                'backend.preview',
            ],
        ];
    }

    private function getItem()
    {
        $urlVars = $this->request->get('urlVars');
        $id = $urlVars && isset($urlVars['id']) ? (int) $urlVars['id'] : 0;

        $data = $this->UserCaseModel->getDetail($id);
        $data = $data ? $data : [];

        return $data;
    }

    public function form()
    {
        $data = $this->getItem();
        $id = $data ? $data['id'] : 0;

        $form = new Form($this->getFormFields(), $data);
        return [
            'id' => $id,
            'form' => $form,
            'data' => $data,
            'link_preview' => $id ? $this->router->url('report/view/'. $id) : '',
            'title_page_edit' => $data && $data['title'] ? $data['title'] : 'New Diagrams',
            'url' => $this->router->url(),
            'link_list' => $this->router->url('reports'),
            'link_form' => $this->router->url($id ? 'report/detail' : 'new-report/usercase'),
            'link_search' => $this->router->url('note/search'),
        ];
        
    }

    public function preview()
    {
        $data = $this->getItem();
        $id = $data ? $data['id'] : 0;

        $form = new Form($this->getFormFields(), $data);

        $permission = $this->container->get('PermissionModel');
        $allow_edit = true;
        if (is_object($permission))
        {
            $allow_edit = $permission->checkPermission(['usercase_manager']);
        }

        $button_header = [
            [
                'link' => $this->router->url('reports'),
                'class' => 'btn btn-outline-secondary',
                'title' => 'Cancel',
            ],
        ];
        if ($allow_edit)
        {
            $button_header[] = [
                'link' => $this->router->url('report/detail/'. $id),
                'class' => 'btn ms-2 btn-outline-success',
                'title' => 'Edit',
            ];
        }
                        
        $form = new Form($this->getFormFields(), $data);
        return [
            'id' => $id,
            'form' => $form,
            'button_header' => $button_header,
            'data' => $data,
            'title_page' => $data && $data['title'] ? $data['title'] : '',
            'url' => $this->router->url(),
            'link_list' => $this->router->url('reports'),
            'link_form' => $this->router->url('report/detail'),
        ];
        
    }

    public function getFormFields()
    {
        $fields = [
            'file' => [
                'file',
                'showLabel' => false,
                'formClass' => 'form-control',
            ],
            'title' => [
                'text',
                'showLabel' => false,
                'placeholder' => 'New Title',
                'formClass' => 'form-control border-0 border-bottom fs-2 py-0',
                'required' => 'required',
            ],
            'note_mermaidjs' =>  [
                'option',
                'label' => 'Diagrams',
                'default' => 0,
                'formClass' => 'form-select',
            ],
            'note_html' =>  [
                'option',
                'label' => 'Description',
                'default' => 0,
                'formClass' => 'form-select',
            ],
            'token' => ['hidden',
                'default' => $this->token->value(),
            ],
        ];

        return $fields;
    }
}
