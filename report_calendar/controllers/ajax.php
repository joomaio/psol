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

class ajax extends ReportController 
{
    public function find()
    {
        $urlVars = $this->request->get('urlVars');
        $id = (int) $urlVars['id'];
       
        $current_day = $this->request->post->get('current_day', 0, 'int');
        $action = $this->request->post->get('action', 0, 'int');
        $data =  $this->CalendarModel->getDetail($id);

        if (!$action)
        {
            // previos month
            $day = strtotime('-1 month', $current_day);
        }
        elseif ($action == 1) 
        {
            $day = strtotime('+1 month', $current_day);
        }
        else
        {
            $day = strtotime('now');
        }

        $first_day_this_month = date('01-m-Y', $day);
        $last_day_this_month  = date('t-m-Y', $day);
        $start_date = strtotime("sunday -1 week", strtotime($first_day_this_month));
        $end_date = strtotime("saturday 0 week", strtotime($last_day_this_month));

        $days = [];
        $date = $start_date;
        while($date <= $end_date)
        {
            $class = date('m', $day) != date('m', $date) ? 'other-month' : '';
            $class = date('d-m-Y', $day) == date('d-m-Y', $date) ? 'today' : $class;

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
                'date' => date('d', $date),
                'class' => $class,
                'day' => date('l', $date),
            ];

            $date += 86400;
        }

        $this->app->set('format', 'json');
        $this->set('status' , 'success');
        $this->set('current_day' , $day);
        $this->set('month' , date('F Y', $day));
        $this->set('data' , $days);
        $this->set('message' , '');
        return;
    }
}
