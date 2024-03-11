<?php
/**
 * SPT software - Model
 * 
 * @project: https://github.com/smpleader/spt
 * @author: Pham Minh - smpleader
 * @description: Just a basic model
 * 
 */

namespace App\psol\report_calendar\models;

use SPT\Container\Client as Base;
use SPT\Traits\ErrorString;

class CalendarModel extends Base
{ 
    use ErrorString; 

    public function remove($id)
    {
        // remove in tree structure
        if (!$id)
        {
            return false;
        }

        $try = $this->ReportEntity->remove($id);
        return $try;
    }

    public function validate($data)
    {
        if (!$data || !is_array($data))
        {
            $this->error = 'Invalid data format';
            return false;
        }

        if (!$data['title'])
        {
            $this->error = "title can't empry";
            return false;
        }

        return $data;
    }

    public function add($data)
    {
        $report = [
            'title' => $data['title'],
            'status' => 1,
            'data' => '',
            'type' => 'calendar',
            'created_by' => $this->user->get('id'),
            'created_at' => date('Y-m-d H:i:s'),
            'modified_by' => $this->user->get('id'),
            'modified_at' => date('Y-m-d H:i:s')
        ];
        $report = $this->ReportEntity->bind($report);

        if (!$report)
        {
            $this->error = $this->ReportEntity->getError();
            return false;
        }

        $newId = $this->ReportEntity->add($report);

        if (!$newId)
        {
            $this->error = $this->ReportEntity->getError();
            return false;
        }
        else
        {
            $calendar = [
                'milestones' => $data['milestones'],
                'tags' => $data['tags'],
                'report_id' => $newId,
            ];
            $calendar = $this->ReportCalendarEntity->bind($calendar);

            if (!$calendar)
            {
                $this->error = $this->ReportCalendarEntity->getError();
                return false;
            }

            $try = $this->ReportCalendarEntity->add($calendar);
            
        }

        return $newId;
    }

    public function update($data)
    {
        $report = [
            'title' => $data['title'],
            'status' => 1,
            'data' => '',
            'id' => $data['id'],
            'type' => 'calendar',
            'modified_by' => $this->user->get('id'),
            'modified_at' => date('Y-m-d H:i:s')
        ];
        $report = $this->ReportEntity->bind($report);

        if (!$report)
        {
            $this->error = $this->ReportEntity->getError();
            return false;
        }

        $try = $this->ReportEntity->update($report);

        if (!$try)
        {
            $this->error = $this->ReportEntity->getError();
            return false;
        }
        else
        {
            $find = $this->ReportCalendarEntity->findOne(['report_id' => $data['id']]);
            if (!$find)
            {
                $calendar = [
                    'milestones' => $data['milestone'],
                    'tags' => $data['tags'],
                    'report_id' => $data['id'],
                ];

                $calendar = $this->ReportCalendarEntity->bind($calendar);
                $try = $this->ReportCalendarEntity->add($calendar);
            }
            else
            {
                $calendar = [
                    'milestones' => $data['milestone'],
                    'tags' => $data['tags'],
                    'report_id' => $data['id'],
                    'id' => $find['id'],
                ];

                $calendar = $this->ReportCalendarEntity->bind($calendar);
                $try = $this->ReportCalendarEntity->update($calendar);
            }
        }

        return $try;
    }

    public function getDetail($id)
    {
        $find = $this->ReportEntity->findByPK($id);
        
        $find = $find ? $find : [];
        $data = [];
        $calendar = $this->ReportCalendarEntity->findOne(['report_id' => $id]);
        $data['milestones'] = $calendar && $calendar['milestones'] ? json_decode($calendar['milestones'], true) : [];
        $data['tags'] = $calendar && $calendar['tags'] ? json_decode($calendar['tags'], true) : [];
        
        if (!$id)
        {
            $data = $this->session->getform('report_calendar', []);
        }

        $find['milestone'] = $data ? $data['milestones'] : [];
        $find['tags'] = $data ? $data['tags'] : [];

        // get request
        $where = ['start_at IS NOT NULL'];
        
        if ($find['milestone'])
        {
            $where[] = 'milestone_id in ('. implode(',', $find['milestone']).')';
        }

        $filter_tag = [];
        if ($find['tags'])
        {
            $where_tag = [];
            foreach($find['tags'] as $tag)
            {
                if($tag)
                {
                    $where_tag[] = '(tags LIKE "'.$tag.'" OR tags LIKE "'.$tag.',%" OR tags LIKE "%,'.$tag.',%" OR tags LIKE "%,'.$tag.'")';
                }
                $tag_tmp = $this->TagEntity->findByPK($tag);
                if ($tag_tmp)
                {
                    $filter_tag[] = $tag_tmp;
                }
            }

            if ($where_tag)
            {
                $where[] = ['('. implode(" OR ", $where_tag) .')'];
            }
        }

        $find['filter_tag'] = $filter_tag;
        
        $requests = $this->RequestEntity->list(0, 0, $where, 'start_at asc');
        foreach($requests as &$request)
        {
            $tags = $request['tags'] ? explode(',', $request['tags']) : [];
            $request['tags'] = [];
            foreach($tags as $tag)
            {
                $tag_tmp = $this->TagEntity->findByPK($tag);
                if ($tag_tmp)
                {
                    $request['tags'][] = $tag_tmp['name'];
                }
            }

            $assigns = $request['assignment'] ? json_decode($request['assignment']) : [];
            $selected_tmp = [];
            foreach($assigns as $assign)
            {
                $user_tmp = $this->UserEntity->findByPK($assign);
                if ($user_tmp)
                {
                    $selected_tmp[] = $user_tmp['name'];
                }
            }
            $request['assignment'] = $selected_tmp;

            $request['status'] = $request['finished_at'] && $request['finished_at'] != '0000-00-00 00:00:00' && strtotime($request['finished_at']) <= strtotime('now') ? 1 : 0;
        }
        $find['requests'] = $requests ? $requests : [];
        $find['rang_day'] = $this->getRangeDay($where);

        return $find;
    }

    public function getRangeDay($where = [])
    {
        $where[10] = 'start_at IS NOT NULL && start_at > 0';
        $start = $this->query->table('#__requests')->detail($where, 'UNIX_TIMESTAMP(MIN(start_at)) as minstart, UNIX_TIMESTAMP(MAX(start_at)) as maxstart');

        $where[10] = 'finished_at IS NOT NULL';
        $finish = $this->query->table('#__requests')->detail($where, 'UNIX_TIMESTAMP(MAX(finished_at)) as maxfinish, UNIX_TIMESTAMP(NOW() - INTERVAL 3 DAY) as minstart, UNIX_TIMESTAMP(NOW() + INTERVAL 3 DAY) as maxstart');
        
        return [
            min($start['minstart'], $finish['minstart']),
            max($start['maxstart'],  $finish['maxfinish']) // $finish['maxstart'],
        ];
    }
}
