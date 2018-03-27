<?php

class TempReportsController extends AppController {

    public $name = 'TempReports';
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler');

/////////////////////////////////////////////////////////////////////////	

    public function beforeFilter() {
        $this->set('cpage', 'temp_report');
        parent::beforefilter();
        $this->__validateLoginStatus();
    }

/////////////////////////////////////////////////////////////////////////	
    //***********carefull**********//
    //common function for dailystatus and pending reports 
    public function check_daily_reports($user_id = NULL, $date = NULL) {
        $temp_reports = $this->TempReport->findAllByUserIdAndDate($user_id, $date, array(), array('DATE_FORMAT(TempReport.start_time,"%H:%i:%s")' => 'asc'));

        $success = array('success' => 1, 'error_row_1' => 0, 'error_row_2' => 0);
        for ($i = 1; $i < count($temp_reports); $i++) {
            if (date('H:i', strtotime($temp_reports[$i]['TempReport']['start_time'])) != date('H:i', strtotime($temp_reports[$i - 1]['TempReport']['end_time']))) {
                $success['success'] = -1;
                $success['error_row_1'] = $i;
                $success['error_row_2'] = $i + 1;
                break;
            }
        }

        if ($success['success'] == 1):
            $this->loadModel('Permission');
            $permission_exists = $this->Permission->find('first', array('conditions' => array('Permission.date' => $date, 'Permission.user_id' => $user_id, 'Permission.approved !=' => 2)));

            if (!empty($permission_exists)) {
                $permission_added = false;

                for ($i = 0; $i < count($temp_reports); $i++) {
                    if ($temp_reports[$i]['TempReport']['category_id'] == 24) {
                        if ((date('H:i', strtotime($temp_reports[$i]['TempReport']['start_time'])) == date('H:i', strtotime($permission_exists['Permission']['from_time']))) && date('H:i', strtotime(($temp_reports[$i]['TempReport']['end_time'])) == date('H:i', strtotime($permission_exists['Permission']['to_time'])))) {
                            $success['success'] = 1;
                        } else {
                            $success['success'] = -2;
                            $success['from_time'] = date('g:i:a', strtotime($permission_exists['Permission']['from_time']));
                            $success['to_time'] = date('g:i:a', strtotime($permission_exists['Permission']['to_time']));

                            $success['error_from'] = date('g:i:a', strtotime($temp_reports[$i]['TempReport']['start_time']));
                            $success['error_to'] = date('g:i:a', strtotime($temp_reports[$i]['TempReport']['end_time']));
                        }
                        $permission_added = true;
                        //break;
                    }
                }

                if ($permission_added == false) {
                    $success['success'] = -3;
                }
            } else {
                //check timer on time and report start time
                $this->loadModel('Entry');
                $entry_time =$exit_time= $this->Entry->findByUserIdAndDate($user_id, $date);
                if (empty($entry_time)) {
                    $this->loadModel('PendingReport');
                    $entry_time = $this->PendingReport->findByUserIdAndDate($user_id, $date);
                    
                    $datetime1 = new DateTime($entry_time['PendingReport']['start_time']);
                    $correct_time = $entry_time['PendingReport']['start_time'];
                } else {
                    $datetime1 = new DateTime($entry_time['Entry']['time_in']);
                    $correct_time = $entry_time['Entry']['time_in'];
                }

                $datetime2 = new DateTime($temp_reports[0]['TempReport']['start_time']);
                $interval = $datetime1->diff($datetime2);
                $diff_time = ($interval->format('%h') * 60) + ($interval->format('%i'));

                if ($diff_time > 5) {
                    $success['success'] = -4;
                    $success['correct_time'] = date('h:i:s A', strtotime($correct_time)) . ' diff time : ' . $diff_time;
                    $success['wrong_time'] = date('h:i:s A', strtotime($temp_reports[0]['TempReport']['start_time']));
                }
                if ($exit_time) {
                    $this->loadModel('PendingReport');
                    $exit_time = $this->PendingReport->findByUserIdAndDate($user_id, $date);
                    if ($date != date('Y-m-d')) {
                        $endtime = new DateTime($exit_time['PendingReport']['end_time']);

                        $correct_endtime = $exit_time['PendingReport']['end_time'];
                        foreach ($temp_reports as $temp_report) {
                            $endtime2 = new DateTime($temp_report['TempReport']['end_time']);
                        }
                        $interval1 = $endtime->diff($endtime2);
                        $diff_endtime = ($interval1->format('%h') * 60) + ($interval1->format('%i'));

                        if ($diff_endtime > 0) {
                            $success['success'] = -5;
                            $success['correct_endtime'] = date('h:i:s A', strtotime($correct_endtime)) . ' diff endtime : ' . $diff_endtime;
                            $success['wrong_endtime'] = date('h:i:s A', strtotime($temp_report['TempReport']['end_time']));
                        }
                    }
                }
            } 
        endif;
        echo json_encode($success);
        exit;
    }

/////////////////////////////////////////////////////////////////////////	
    //checks end time - ex: current time 7:30, report time 7:40 - not sending
    public function check_daily_report_time($user_id = NULL, $date = NULL) {

        $this->loadModel('Permission');
        $permission_exists = false;
        
//        $permissions = $this->Permission->find('first', array(
//            'conditions' => array(
//                'Permission.date' => $date, 
//                'Permission.user_id' => $user_id, 
//                'Permission.approved !=' => 2)));
        $reports = $this->TempReport->findAllByUserIdAndDate($user_id, $date, array(), array('TempReport.start_time' => 'asc'));
        $end_report = end($reports);
        
        if($end_report['TempReport']['category_id'] == 24){
            $permission_exists = true;
        }
        
        $return = array();
        if ($permission_exists == false) {
            $temp_reports = $this->TempReport->findAllByUserIdAndDate($user_id, $date, array(), array('TempReport.start_time' => 'asc'));
            $end_report = end($temp_reports);

            $datetime1 = new DateTime(date('Y-m-d H:i:s'));
            $datetime2 = new DateTime($end_report['TempReport']['end_time']);
            $interval = $datetime1->diff($datetime2);

            $worked_hours = ($interval->format('%h') * 60) + ($interval->format('%i'));

            if (gmdate("H:i:s", ($worked_hours * 60)) <= '00:05:00' || date('Y-m-d H:i:s') > $end_report['TempReport']['end_time']) {
                $return['success'] = 1;
            } else {
                $return['success'] = -1;
                $return['late_time'] = $interval->format('%h') . ' hours ' . gmdate("i:s", ($worked_hours * 60));
                $return['current_time'] = date('h:i');
                $return['lead_lag'] = 'leading';
                //date('Y-m-d H:i:s') > $end_report['TempReport']['end_time'] ? $return['lead_lag'] = 'lagging' : $return['lead_lag'] = 'leading';
            }
        } else {
            $return['success'] = 1;
        }

        echo json_encode($return);
        exit;
    }

/////////////////////////////////////////////////////////////////////////	
    //This Rule disabled
    // permission and late entry checking.... (if any one works more than 8:00 hours, permission or late entry will declined else permission added)
    public function check_permission_late_entry($user_id = NULL, $date = NULL) {
        $reports = $this->TempReport->findAllByUserIdAndDate($user_id, $date, array(), array('TempReport.start_time' => 'asc'));
        $worked_hours = 0;

        foreach ($reports as $key => $report) {
            $datetime1 = new DateTime($report['TempReport']['start_time']);
            $datetime2 = new DateTime($report['TempReport']['end_time']);
            $interval = $datetime1->diff($datetime2);

            if ($report['TempReport']['category_id'] != 23 && $report['TempReport']['category_id'] != 22 && $report['TempReport']['category_id'] != 24) {
                $worked_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
            }
        }

        $office_hours = date("H:i", strtotime('08:00'));
        $grace_hours = date("H:i", strtotime('07:45'));
        $half_day_hours = date("H:i", strtotime('06:00'));
        $permission_max_hours = date("H:i", strtotime('02:00'));
        $worked_hours = gmdate("H:i", ($worked_hours * 60));
        
        $this->loadModel('Permission');
        $permission_exists = $this->Permission->find('first', array('conditions' => array('Permission.date' => $date, 'Permission.user_id' => $user_id, 'Permission.approved !=' => 2)));

        $this->loadModel('LateEntry');
        $late_entry_exists = $this->LateEntry->find('first', array('conditions' => array('LateEntry.date' => $date, 'LateEntry.user_id' => $user_id, 'LateEntry.approved !=' => 2)));

        $this->loadModel('Leave');
        $this->loadModel('SubLeave');

        $check_leave = $this->Leave->findByUserIdAndDate($this->Session->read('User.id'),$date);
                                
        if (strtotime($worked_hours) >= strtotime($office_hours)) {
            //worked more than 8:00 hours and also takes permission or late entry ( this permission or late entry will be declined )....
            //check permission exists

            if (!empty($permission_exists)) {
                $this->Permission->id = $permission_exists['Permission']['id'];
                $this->Permission->saveField('approved', 2);
                $this->Permission->saveField('remarks', 'Your worked hours (' . $worked_hours . ') on ' . $date . ' is more than 08:00 hours. So this permission declined');

                $add_to = $this->requestAction('emails/all_to_email');
                foreach ($add_to as $to) {
                    $array = explode(',', $to['Email']['options']);

                    foreach ($array as $key => $value) {
                        if ($value == 2) {
                            $all_to[$to['Email']['id']] = $to['Email']['email'];
                        }
                    }
                }

                if (empty($all_to)) {
                    $all_to = "";
                }

                $user = $this->requestAction('users/get_user', array('pass' => array('User.id' => $user_id)));

                $this->Email->to = $this->Session->read('User.email');
                $this->Email->cc = $all_to;
                $this->Email->subject = 'Permission Declined';
                $this->Email->replyTo = 'admin@arkinfotec.com';
                $this->Email->from = 'admin@arkinfotec.com';
                $this->Email->template = 'permissionaccept';
                $this->Email->sendAs = 'html';
                $this->set('user', $user);
                $this->set('permission', $permission_exists);
                $this->set('status', 2);
                $this->Email->send();
            }

            //check late entry exists
            if (!empty($late_entry_exists)) {
                $this->LateEntry->id = $late_entry_exists['LateEntry']['id'];
                $this->LateEntry->saveField('approved', 2);

                $this->Email->to = $this->Session->read('User.email');
//				$this->Email->cc = $all_to;
                $this->Email->subject = 'Late Entry Declined';
                $this->Email->replyTo = 'admin@arkinfotec.com';
                $this->Email->from = 'admin@arkinfotec.com';
                $this->Email->sendAs = 'html';
                $message = '<b>Dear ' . $this->Session->read('User.name') . '</b><br><br>';
                $message .= 'Your late entry on ' . $date . ' is declined. Because your worked hours (' . $worked_hours . ') more than '.$office_hours.' hours';
                $this->Email->send($message);
            }
            
            //If user take leave then it declined
            if (!empty($check_leave)) {
                $add_to = $this->requestAction('emails/all_to_email');
                foreach ($add_to as $to) {
                    $array = explode(',', $to['Email']['options']);

                    foreach ($array as $key => $value) {
                        if ($value == 2) {
                            $all_to[$to['Email']['id']] = $to['Email']['email'];
                        }
                    }
                }

                if (empty($all_to)) {
                    $all_to = "";
                }
                
                $this->Leave->id = $check_leave['Leave']['id'];
                $this->Leave->saveField('approved', 2);

                foreach ($check_leave['SubLeave'] as $subleave) {
                    $this->SubLeave->id = $subleave['id'];
                    $this->SubLeave->saveField('status', '-');
                }

                $this->Email->to = $this->Session->read('User.email');
                $this->Email->cc = $all_to;
                $this->Email->subject = 'Leave Declined';
                $this->Email->replyTo = 'admin@arkinfotec.com';
                $this->Email->from = 'admin@arkinfotec.com';
                $this->Email->sendAs = 'html';
                $message = '<b>Dear ' . $this->Session->read('User.name') . '</b><br><br>';
                $message .= 'Your leave on ' . $date . ' is declined. Because your worked hours (' . $worked_hours . ') more than 08:00 hours';
                $this->Email->send($message);
            }
        } else {
            // not worked 8:00 hours and no permission found
            if (empty($permission_exists) && strtotime($worked_hours) < strtotime($grace_hours)) {
                $permission_count = $this->requestAction('permission/user_get_current_month_permission_new');

                if ($permission_count >= Permission::MAX_PERMISSION) {
                    $this->add_half_day_leave($date, $worked_hours, 1);
                } else {
                    //worked less than 06:00 hours
                    if (strtotime($worked_hours) < strtotime($half_day_hours)) {
                        $this->add_half_day_leave($date, $worked_hours, 2);
                    } else {
                        $end_report = end($reports);

                        $time1 = new DateTime(date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' ' . $worked_hours . ':00')));
                        $time2 = new DateTime(date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' ' . $office_hours . ':00')));
                        $time_interval = $time1->diff($time2);
                        $diff_time = ($time_interval->format('%h') * 60) + ($time_interval->format('%i'));
                        $diff_time = gmdate("H:i", ($diff_time * 60));

                        if (strtotime($diff_time) > strtotime($permission_max_hours)) {
                            //permission hours greater than 2 hours - so half a day leave
                            $this->add_half_day_leave($date, $worked_hours, 2);
                        } else {
                            //permission sent
                            $from_time = $end_report['TempReport']['end_time'];

                            $time_ob_1 = new DateTime($from_time);
                            $time_ob_1->add(new DateInterval('PT' . $time_interval->format('%h') . 'H' . $time_interval->format('%i') . 'M'));
                            $to_time = $time_ob_1->format('Y-m-d H:i:s');

                            $reason = 'Permission on ' . $date . '. My worked hours (' . $worked_hours . ') on ' . $date . ' is less than 08:00 hours';
                            $remarks = 'Your worked hours (' . $worked_hours . ') on ' . $date . ' is less than 08:00 hours. So this is considered as a permission';
                            
                            $add_permission = array('Permission' => array('user_id' => $this->Session->read('User.id'),
                                    'request' => 'current',
                                    'date' => $date,
                                    'from_time' => $from_time,
                                    'to_time' => $to_time,
                                    'reason' => $reason,
                                    'remarks' => $remarks,
                                    'approved' => 1
                            ));

                            $this->Permission->save($add_permission);
                            $permission_id = $this->Permission->getLastInsertId();
                            $this->requestAction('permission/permission_request_mail/' . $permission_id);

                            $add_temp_report = array('TempReport' => array('user_id' => $user_id,
                                    'date' => $date,
                                    'category_id' => 24,
                                    'start_time' => $from_time,
                                    'end_time' => $to_time,
                                    'comments' => $reason));

                            $this->TempReport->save($add_temp_report);

                            $this->Email->to = $this->Session->read('User.email');
                            $this->Email->subject = 'Permission : ' . date('jS F Y', strtotime($date));
                            $this->Email->replyTo = 'admin@arkinfotec.com';
                            $this->Email->from = 'admin@arkinfotec.com';
                            $this->Email->sendAs = 'html';
                            $message = '<b>Dear ' . $this->Session->read('User.name') . '</b><br><br>';
                            $message .= $remarks;
                            $this->Email->send($message);
                            
                            // worked greater or equals to 06:00 hours and late entry exists
                            if (strtotime($worked_hours) >= strtotime($half_day_hours)) {
                                if (!empty($late_entry_exists)) {
                                    $this->LateEntry->id = $late_entry_exists['LateEntry']['id'];
                                    $this->LateEntry->saveField('approved', 2);

                                    $this->Email->to = $this->Session->read('User.email');
                                    $this->Email->subject = 'Late Entry Declined';
                                    $this->Email->replyTo = 'admin@arkinfotec.com';
                                    $this->Email->from = 'admin@arkinfotec.com';
                                    $this->Email->sendAs = 'html';
                                    $message = '<b>Dear ' . $this->Session->read('User.name') . '</b><br><br>';
                                    $message .= 'Your late entry on ' . $date . ' is declined. Because your worked hours (' . $worked_hours . ') more than '.$half_day_hours.' hours';
                                    $this->Email->send($message);
                                }
                                
                                //If user take leave then it declined
                                if (!empty($check_leave)) {
                                    $this->Leave->id = $check_leave['Leave']['id'];
                                    $this->Leave->saveField('approved', 2);
                                    
                                    foreach ($check_leave['SubLeave'] as $subleave) {
                                        $this->SubLeave->id = $subleave['id'];
                                        $this->SubLeave->saveField('status', '-');
                                    }

                                    $this->Email->to = $this->Session->read('User.email');
                                    $this->Email->subject = 'Leave Declined';
                                    $this->Email->replyTo = 'admin@arkinfotec.com';
                                    $this->Email->from = 'admin@arkinfotec.com';
                                    $this->Email->sendAs = 'html';
                                    $message = '<b>Dear ' . $this->Session->read('User.name') . '</b><br><br>';
                                    $message .= 'Your leave on ' . $date . ' is declined. Because your worked hours (' . $worked_hours . ') more than 08:00 hours';
                                    $this->Email->send($message);
                                }
                            }
                        }
                    }
                }
            }

            // worked greater or equals to 06:00 hours and takes permission
            if (!empty($permission_exists) && strtotime($worked_hours) >= strtotime($half_day_hours)) {
                if (!empty($late_entry_exists)) {
                    $this->LateEntry->id = $late_entry_exists['LateEntry']['id'];
                    $this->LateEntry->saveField('approved', 2);

                    $this->Email->to = $this->Session->read('User.email');
//				$this->Email->cc = $all_to;
                    $this->Email->subject = 'Late Entry Declined';
                    $this->Email->replyTo = 'admin@arkinfotec.com';
                    $this->Email->from = 'admin@arkinfotec.com';
                    $this->Email->sendAs = 'html';
                    $message = '<b>Dear ' . $this->Session->read('User.name') . '</b><br><br>';
                    $message .= 'Your late entry on ' . $date . ' is declined. Because your worked hours (' . $worked_hours . ') more than 08:00 hours';
                    $this->Email->send($message);
                }
            }
        }

        exit;
    }

/////////////////////////////////////////////////////////////////////////	

    public function add_half_day_leave($date = NULL, $worked_hours = NULL, $type = NULL) {
        $this->loadModel('Leave');
        $this->loadModel('SubLeave');
        
        $check_leave = false;
        $sub_leaves = $this->SubLeave->findAllByDate($date);
        
        foreach ($sub_leaves as $sub_leave) {
            if($sub_leave['Leave']['user_id'] == $this->Session->read('User.id')){
                $check_leave = true;
            }
        }

        if($check_leave == FALSE){
            $timings = $this->requestAction('entries/office_times');
            $reason = 'Half day leave on ' . $date . '. My worked hours (' . $worked_hours . ') on ' . $date . ' is less than '.$timings['office_hours'].' hours';

            $remarks = 'Your worked hours (' . $worked_hours . ') on ' . $date . ' is less than '.$timings['office_hours'].' hours.';
            if ($type == 1) {
                $remarks .= 'You already take '.Permission::MAX_PERMISSION.' permission on ' . date('F', strtotime($date)) . '.';
            }
            $remarks .= ' So this is considered as a half a day leave';

            $add_half_day_leave = array(
                'Leave' => array(
                    'user_id' => $this->Session->read('User.id'),
                    'request' => 'current',
                    'date' => $date,
                    'days' => '0.5',
                    'reason' => $reason,
                    'approved' => 1,
                    'remarks' => $remarks));

            $leave_count = $this->requestAction('leave/user_get_all_leave_count_per_year/' . $this->Session->read('User.id') . '/' . date('Y', strtotime($date)));
            $user_casual_leave = $this->Session->read('User.casual_leave');

            $leave_count >= $user_casual_leave ? $status = 'P' : $status = 'C';

            $this->Leave->save($add_half_day_leave);
            $leave_id = $this->Leave->getLastInsertId();

            $add_half_day_sub_leave = array('SubLeave' => array('leave_id' => $leave_id,
                    'date' => $date,
                    'day' => '0.5',
                    'status' => $status));

            $this->SubLeave->save($add_half_day_sub_leave);
            $this->requestAction('leave/leave_request_mail/' . $leave_id);

            $this->Email->to = $this->Session->read('User.email');
            $this->Email->subject = 'Half a day Leave : ' . date('jS F Y', strtotime($date));
            $this->Email->replyTo = 'admin@arkinfotec.com';
            $this->Email->from = 'admin@arkinfotec.com';
            $this->Email->sendAs = 'html';
            $message = '<b>Dear ' . $this->Session->read('User.name') . '</b><br><br>';
            $message .= $remarks;
            $this->Email->send($message);
        }
    }

/////////////////////////////////////////////////////////////////////////
    //Final Checking the reports
    public function check_report_entries($user_id = NULL, $date = NULL) {
        $reports = $this->TempReport->findAllByUserIdAndDate($user_id, $date, array(), array('TempReport.start_time' => 'asc'));
        $end_report = end($reports);
        $worked_hours = $total_hours = 0;

        foreach ($reports as $key => $report) {
            $datetime1 = new DateTime($report['TempReport']['start_time']);
            $datetime2 = new DateTime($report['TempReport']['end_time']);
            $interval = $datetime1->diff($datetime2);
            $total_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
            
            if ($report['TempReport']['category_id'] != 23 && 
                $report['TempReport']['category_id'] != 22 /*&& 
                $report['TempReport']['category_id'] != 24*/) {
                $worked_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
            }
        }
        $total_office_hours = gmdate("H:i:s", ($total_hours * 60));
        $total_worked_hours = gmdate("H:i:s", ($worked_hours * 60));
        $timings = $this->requestAction('entries/office_times');

        $is_permission_saturday = $this->requestAction('entries/check_permission_saturday');
        if($is_permission_saturday){
            exit;
        }

        //Eg (08:00:00 < 09:30:00)
        if($total_office_hours < $timings['total_hours_in_office']){
            $this->loadModel('Permission');
            $permission_exists = $this->Permission->find('first', array(
                'conditions' => array(
                    'Permission.date' => $date, 
                    'Permission.user_id' => $user_id, 
                    'Permission.approved !=' => 2)));

            $this->loadModel('Leave');
            $this->loadModel('SubLeave');
            $this->loadModel('LateEntry');
            $late_entry = $this->LateEntry->find('first', array(
                    'conditions' => array(
                        'LateEntry.date' => $date,
                        'LateEntry.user_id' => $user_id
                    )
                ));

            $check_leave = false;
            $sub_leaves = $this->SubLeave->findAllByDate($date);
            
            foreach ($sub_leaves as $sub_leave) {
                if($sub_leave['Leave']['user_id'] == $user_id){
                    $check_leave = true;
                }
            }

            //get difference hours Eg(09:30:00 - 09:15:00 => 00:15:00)
//            $time2 = new DateTime(date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($date)) . ' ' . $total_worked_hours)));
//            $time1 = new DateTime(date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($date)) . ' ' . $timings['office_hours'])));
            $time1 = new DateTime(date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($date)) . ' ' . $total_office_hours)));
            $time2 = new DateTime(date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($date)) . ' ' . $timings['total_hours_in_office'])));

            $time_interval = $time1->diff($time2);

            $diff_time = ($time_interval->format('%h') * 60) + ($time_interval->format('%i'));
            $diff_time = gmdate("H:i:s", ($diff_time * 60));
            
            if(empty($permission_exists) && $check_leave == false){
                
                //If he has late entry today, then check late entry time
                if(!empty($late_entry)){
                    //if he go earlier
                    if(strtotime($timings['late_entry_end_time']) > strtotime($end_report['TempReport']['end_time'])){
                        $lt_time1 = new DateTime(date('Y-m-d H:i:s', strtotime(date('Y-m-d', strtotime($date)) . ' ' . $timings['late_entry_end_time'])));
                        $lt_time2 = new DateTime(date('Y-m-d H:i:s', strtotime($end_report['TempReport']['end_time'])));
                        $lt_time_interval = $lt_time1->diff($lt_time2);
                        $lt_diff_time = ($lt_time_interval->format('%h') * 60) + ($lt_time_interval->format('%i'));
                        $lt_diff_time = gmdate("H:i:s", ($lt_diff_time * 60));

                        //If he exceeds report sending grace time
                        if($lt_diff_time > $timings['report_send_grace_time']){
                            $this->add_permission_entry($date, $total_worked_hours, $lt_time_interval);
                        }
                    }
                }else{
                    //If he exceeds report sending grace time
                    if($diff_time > $timings['report_send_grace_time']){
                        //If he send report within permission hours
                        if($diff_time <= $timings['permission_hours']){
                            $this->add_permission_entry($date, $total_worked_hours, $time_interval);
                        }
                        elseif($diff_time > $timings['permission_hours']){
                            $this->add_half_day_leave($date, $total_worked_hours, 2);
                        }
                    }
                }
            }
            else{
                if($check_leave == false && !empty($permission_exists)){
                    if($total_worked_hours < $timings['permission_back_time']){
                        $this->Permission->id = $permission_exists['Permission']['id'];
                        $this->Permission->saveField('approved', 2);
                        $this->add_half_day_leave($date, $total_worked_hours, 2);
                    }
                }
            }
        }
        exit;
    }
    
/////////////////////////////////////////////////////////////////////////
    
    public function add_permission_entry($date = NULL, $worked_hours = NULL, $time_interval = NULL) {
        $permission_count = $this->requestAction('permission/user_get_current_month_permission_new');

        if ($permission_count >= Permission::MAX_PERMISSION) {
            $this->add_half_day_leave($date, $worked_hours, 1);
        } else {
            $timings = $this->requestAction('entries/office_times');
            $reports = $this->TempReport->findAllByUserIdAndDate($this->Session->read('User.id'), $date, array(), array('TempReport.start_time' => 'asc'));
            $end_report = end($reports);
            
            //permission sent
            $from_time = $end_report['TempReport']['end_time'];

            $time_ob_1 = new DateTime($from_time);
            $time_ob_1->add(new DateInterval('PT' . $time_interval->format('%h') . 'H' . $time_interval->format('%i') . 'M'));
            $to_time = $time_ob_1->format('Y-m-d H:i:s');

//            $reason = 'Permission on ' . $date . '. My Total Office hours (' . $worked_hours . ') on ' . $date . ' is less than '.$timings['total_hours_in_office'].' hours';
            $reason = 'Permission on ' . $date . '. You sent report earlier from Office End time.';
            $remarks = 'Report Sent Earlier from Office End time. So this is considered as a permission';
//            $remarks = 'Your Total office hours (' . $worked_hours . ') on ' . $date . ' is less than '.$timings['total_hours_in_office'].' hours. So this is considered as a permission';

            $add_permission = array(
                'Permission' => array(
                    'user_id' => $this->Session->read('User.id'),
                    'request' => 'current',
                    'date' => $date,
                    'from_time' => $from_time,
                    'to_time' => $to_time,
                    'reason' => $reason,
                    'remarks' => $remarks,
                    'approved' => 1
            ));

            $this->Permission->save($add_permission);
            $permission_id = $this->Permission->getLastInsertId();
            $this->requestAction('permission/permission_request_mail/' . $permission_id);

            $add_temp_report = array(
                'TempReport' => array(
                    'user_id' => $this->Session->read('User.id'),
                    'date' => $date,
                    'category_id' => 24,
                    'start_time' => $from_time,
                    'end_time' => $to_time,
                    'comments' => $reason));

            $this->TempReport->save($add_temp_report);
            
            $this->Email->to = $this->Session->read('User.email');
            $this->Email->subject = 'Permission Sent: ' . date('jS F Y', strtotime($date));
            $this->Email->replyTo = 'admin@arkinfotec.com';
            $this->Email->from = 'admin@arkinfotec.com';
            $this->Email->sendAs = 'html';
            $message = '<b>Dear ' . $this->Session->read('User.name') . '</b><br><br>';
            $message .= $remarks;
            $this->Email->send($message);
        }
    }
}