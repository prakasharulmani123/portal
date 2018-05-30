<?php

class LateEntriesController extends AppController {

    public $name = 'LateEntries';

/////////////////////////////////////////////////////////////////////////	

    public function beforeFilter() {
        $this->set('cpage', 'late_entry');
        parent::beforefilter();
        $this->__validateLoginStatus();
    }

/////////////////////////////////////////////////////////////////////////	

    public function late_entry($get_in_time = NULL, $date = NULL) {

        $this->loadModel('Permission');
        $this->loadModel('Leave');
        $this->loadModel('SubLeave');

        $get_in_time = str_replace('-', ':', $get_in_time);
        
        $timings = $this->requestAction('entries/office_times');
        
        $time1 = new DateTime($timings['office_start_time']);
        $time2 = new DateTime($get_in_time);
        $interval = $time1->diff($time2);

//        $permission_start_time = date('H:i:s', strtotime($timings['permission_start_time']));
        $permission_start_time = date('H:i', strtotime($timings['permission_start_time']));
        $permission_max_time = date('H:i:s', strtotime($timings['permission_max_time']));
        $half_day_excuse_time = date('H:i:s', strtotime($timings['half_day_excuse_time']));

        $max_date_time = $date . ' ' . $permission_max_time;

        $message = '';

        //check permission exists for who sent permission for less than 12:00 PM
        $permission_exists = $this->Permission->find('first', array(
            'conditions' => array(
                'Permission.user_id' => $this->Session->read('User.id'), 
                'Permission.date' => date('Y-m-d', strtotime($date)), 
                'Permission.approved !=' => 2, 
                'Permission.to_time <=' => $max_date_time)));

        if (!empty($permission_exists)) {
            $permission_from_time = new DateTime(date('H:i:s', strtotime($permission_exists['Permission']['from_time'])));
            $permission_to_time = new DateTime(date('H:i:s', strtotime($permission_exists['Permission']['to_time'])));

            $permission_interval = $permission_from_time->diff($permission_to_time);
            $permission_time = $permission_interval->format('%H:%I:%S');

            $permission_late_interval = $permission_from_time->diff($time2);
            $permission_late_time = $permission_late_interval->format('%H:%I:%S');

            //check if exceeds permission time
            if (strtotime($permission_late_time) > strtotime($permission_time)) {
                $permission_exceed_interval = $permission_to_time->diff($time2);
                $permission_exceed_time = $permission_exceed_interval->format('%H:%I:%S');

                //chech if exceeds grace time
                if (strtotime($permission_exceed_time) > strtotime($timings['half_day_grace_time'])) {

                    //check if takes less than or greater than 2 hours
                    if (strtotime($permission_late_time) <= strtotime($timings['permission_hours'])) {
                        $message = $this->add_late_entry($date);
                    } else {
                        //change permission to half a day leave
                        $this->Permission->delete($permission_exists['Permission']['id']);
                        $message = $this->add_half_day_leave($date, 0);
                    }
                }
            }
        } else {
            //check leave exists
            $leave_exists = $this->SubLeave->find('all', array(
                'conditions' => array(
                    'SubLeave.date' => date('Y-m-d', strtotime($date)),
                    'Leave.user_id' => $this->Session->read('User.id'),
                    'Leave.approved !=' => 2,
                    )));

            //check leave 
            if (empty($leave_exists)) {
                //check the time more than 12:15 PM 
                if (strtotime($get_in_time) > strtotime($half_day_excuse_time)+60) {
                    //add half a day leave
                    $check_permission_exists = $this->Permission->find('first', array(
                        'conditions' => array(
                            'Permission.user_id' => $this->Session->read('User.id'), 
                            'Permission.date' => date('Y-m-d', strtotime($date)), 
                            'Permission.approved !=' => 2)));

                    if (!empty($check_permission_exists)) {
                        $this->Permission->delete($check_permission_exists['Permission']['id']);
                    }

                    $message = $this->add_half_day_leave($date, 0);
                }
                //check the time more than 11:00 AM
                elseif (strtotime($get_in_time) > strtotime($permission_start_time)+60) {
//				elseif((strtotime($get_in_time) > strtotime($permission_max_time)) && (strtotime($get_in_time) <= strtotime($half_day_excuse_time))){
                    $message = $this->add_permission($date);
                }
                //add late entry
                else {
                    $message = $this->add_late_entry($date);
                }
            }
        }

        $message != '' ? $this->Session->write('LateEntry', 'You are <b>' . $interval->format('%h hours %I minutes %S second(s)') . '</b> late. <br>' . $message) : '';
    }

///////////////////////////////////////////////////////////////////////////////

    public function add_late_entry($date = NULL) {
        $late_entries = $this->LateEntry->find('all', array(
            'conditions' => array(
                'LateEntry.user_id' => $this->Session->read('User.id'), 
                'MONTH(LateEntry.date)' => date('m', strtotime($date)), 
                'YEAR(LateEntry.date)' => date('Y', strtotime($date)), 
                'LateEntry.approved' => 1)));
        
        $insert_new_late_entry = array('LateEntry' => array(
            'user_id' => $this->Session->read('User.id'),
            'date' => date('Y-m-d', strtotime($date)),
            'approved' => 1,
            'amount' => '0.00'
            ));
        
        if (empty($late_entries)) {
            $insert_new_late_entry['LateEntry']['amount'] = 0;

            $message = 'This is your <b class="badge badge-info">1st</b> warning';
        } else {
            $count = count($late_entries) + 1;
            $num = array(
                '2' => '2nd',
                '3' => '3rd',
                '4' => '4th',
                '5' => '5th',
                '6' => '6th',
                '7' => '7th',
                '8' => '8th',
                '9' => '9th');
            $suffix_message = '';
            
            if (in_array($count, array(3,6,9)) || $count > 9) {
                $message = $this->add_permission($date, 2);
                $this->loadModel('Leave');
                $check_today_leave = $this->Leave->find('first', array(
                    'conditions' => array(
                        'Leave.user_id' => $this->Session->read('User.id'),
                        'Leave.date' => date('Y-m-d', strtotime($date)),
                        'Leave.days' => '0.50'
                    )
                    ));
                
                if($count > 9 || !empty($check_today_leave)){
                    $this->LateEntry->save($insert_new_late_entry);
                    return $message;
                }else{
                    $badge = 'important';
                    $suffix_message = '<br><b style="color:red">So One Permission added</b>';
                }
            } else {
                $badge = 'warning';
            }
            $message = 'This is <b class="badge badge-'.$badge.'">'.$num[$count].'</b> late entry'.$suffix_message;
        }

        $this->LateEntry->save($insert_new_late_entry);
        return $message;
    }

///////////////////////////////////////////////////////////////////////////////

    public function add_half_day_leave($date = NULL, $permission_cum_leave = NULL) {
        $this->loadModel('Leave');
        $this->loadModel('SubLeave');

        $add_half_day_leave = array('Leave' => array('user_id' => $this->Session->read('User.id'),
                'request' => 'current',
                'date' => date('Y-m-d', strtotime($date)),
                'days' => '0.5',
                'reason' => 'Half day leave for late entry on ' . date('Y-m-d', strtotime($date)),
                'approved' => 1));

        $leave_count = $this->requestAction('leave/user_get_all_leave_count_per_year/' . $this->Session->read('User.id') . '/' . date('Y', strtotime($date)));
        $user_casual_leave = $this->Session->read('User.casual_leave');

        $leave_count >= $user_casual_leave ? $status = 'P' : $status = 'C';

        $this->Leave->save($add_half_day_leave);
        $leave_id = $this->Leave->getLastInsertId();

        $add_half_day_sub_leave = array('SubLeave' => array('leave_id' => $leave_id,
                'date' => date('Y-m-d', strtotime($date)),
                'day' => '0.5',
                'status' => $status));

        $this->SubLeave->save($add_half_day_sub_leave);

        if ($permission_cum_leave == 0) {
            $message = 'You are late more than 2 hours 15 minutes. <br>';
            $message .= 'So this is considered as <span style="color:red">Half a day Leave</span>';
        } elseif ($permission_cum_leave == 1) {
            $message = 'Late more than 1 Hour consider as a permission<br>';
            $message .= 'You already take '.Permission::MAX_PERMISSION.' permissions. <br>';
            $message .= 'So this is considered as <span style="color:red">Half a day Leave</span>';
        } elseif ($permission_cum_leave == 2) {
            $message = 'You already take '.Permission::MAX_PERMISSION.' permissions. <br>';
            $message .= 'So this is considered as <span style="color:red">Half a day Leave</span>';
        }

        $this->requestAction('leave/leave_request_mail/' . $leave_id);
        return $message;
    }

///////////////////////////////////////////////////////////////////////////////

    public function add_permission($date = NULL, $type = 1) {
        $permission_count = $this->requestAction('permission/user_get_current_month_permission_new');

        if ($permission_count >= Permission::MAX_PERMISSION) {
            $message = $this->add_half_day_leave($date, $type);
        } else {
            $this->loadModel('Permission');
            $timings = $this->requestAction('entries/office_times');
            
            $from_time = date('Y-m-d H:i:s', strtotime($date . ' '.$timings['office_start_time']));
//			$to_time = date('Y-m-d H:i:s', strtotime($date.' 12:00:00'));

            $time_in_hour = date('H');
            if ($time_in_hour > 12) {
                $time_in_hour = ($time_in_hour - 12);
            }
            $time_in_minute = date('i');
            $minute_part_one = substr(date('i'), 0, 1);
            $minute_part_two = substr(date('i'), 1, 2);

            if ($minute_part_two < 5) {
                if ($minute_part_two < 3) {
                    $minute_part_two = 0;
                } else {
                    $minute_part_two = 5;
                }
            } elseif ($minute_part_two > 5) {
                if ($minute_part_one == '5') {
                    if ($minute_part_two < 7) {
                        $minute_part_two = 5;
                    } else {
                        if ($time_in_hour == '12') {
                            $time_in_hour = 1;

                            $minute_part_one = 0;
                            $minute_part_two = 0;
                        } else {
                            $time_in_hour = $time_in_hour + 1;

                            $minute_part_one = 0;
                            $minute_part_two = 0;
                        }
                    }
                } else {
                    if ($minute_part_two < 7) {
                        $minute_part_two = 5;
                    } else {
                        $minute_part_two = 0;
                        $minute_part_one = $minute_part_one + 1;
                    }
                }
            }
            $time_in_minute = $minute_part_one . $minute_part_two;
            $to_time = date('Y-m-d ') . $time_in_hour . ':' . $time_in_minute . ':00';

            $add_permission = array('Permission' => array('user_id' => $this->Session->read('User.id'),
                    'request' => 'current',
                    'date' => date('Y-m-d', strtotime($date)),
                    'from_time' => $from_time,
                    'to_time' => $to_time,
                    'reason' => 'Permission on ' . $date . ' Late Entry Permission',
                    'approved' => 1));

            $this->Permission->save($add_permission);
            $permission_id = $this->Permission->getLastInsertId();

            $message = 'You are late more than 1 hour. <br>';
            $message .= 'So this is considered as <span style="color:red">Permission</span>';

            $this->requestAction('permission/permission_request_mail/' . $permission_id);
        }
        return $message;
    }

/////////////////////////////////////////////////////////////////////////	

    public function admin_index() {
  $this->layout = "admin-inner";
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('LateReport.user_id', '');
            $this->Session->write('LateReport.from_date', '');
            $this->Session->write('LateReport.to_date', '');
            $this->Session->write('LateReport.approved', '');

            if ($this->request->data['LateEntry']['user_id'] != '') {
                $this->Session->write('LateReport.user_id', $this->request->data['LateEntry']['user_id']);
            }

            if (!empty($this->request->data['LateEntry']['from_date']) && !empty($this->request->data['LateEntry']['to_date'])) {
                $this->Session->write('LateReport.from_date', date('Y-m-d', strtotime($this->request->data['LateEntry']['from_date'])));
                $this->Session->write('LateReport.to_date', date('Y-m-d', strtotime($this->request->data['LateEntry']['to_date'])));
            }

            if ($this->request->data['LateEntry']['approved'] != '') {
                $this->Session->write('LateReport.approved', $this->request->data['LateEntry']['approved']);
            }

            return $this->redirect(array('action' => 'admin_index'));
        }

        if ($this->Session->check('LateReport')) {
            $all = $this->Session->read('LateReport');

            $approve_query = '';
            if ($all['approved'] != '') {
                $approve_query = 'LateEntry.approved = ' . $all['approved'];
            }

            if ($all['user_id'] == '' && $all['from_date'] == '') {
                $late_entries = $this->LateEntry->find('all', array(
                    'conditions' => array($approve_query), 
                    'order' => array('LateEntry.created DESC')));
            } elseif ($all['user_id'] != '' && $all['from_date'] == '') {
                $late_entries = $this->LateEntry->find('all', array(
                    'conditions' => array(
                        'LateEntry.user_id' => $all['user_id'], $approve_query), 
                    'order' => array('LateEntry.created DESC')));
            } elseif ($all['user_id'] == '' && $all['from_date'] != '') {
                $late_entries = $this->LateEntry->find('all', array(
                    'conditions' => array(
                        'LateEntry.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), $approve_query), 'order' => array('LateEntry.created DESC')));
            } elseif ($all['user_id'] != '' && $all['from_date'] != '') {
                $late_entries = $this->LateEntry->find('all', array(
                    'conditions' => array(
                        'LateEntry.user_id' => $all['user_id'], 
                        'LateEntry.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), $approve_query), 'order' => array('LateEntry.created DESC')));
            }
        } else {
            $all = array('user_id' => '', 'from_date' => '', 'to_date' => '', 'approved' => '');
            $late_entries = $this->LateEntry->find('all', array('order' => array('LateEntry.created DESC')));
        }

        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('late_entries'));
    }
    
////////////////////////////////////////////////////////////////////////////////

    
    public function admin_add() {
        $this->layout = "admin-inner";
        if ($this->request->is('put') || $this->request->is('post')) {
           
            if ($this->data['LateEntry']['start']['meridian'] == 'pm') {
                if ($this->data['LateEntry']['start']['hours'] != '12') {
                    $start_time = date('Y-m-d', strtotime($this->data['LateEntry']['date'])) . ' ' . ($this->data['LateEntry']['start']['hours'] + 12) . ':' . $this->data['LateEntry']['start']['minutes'] . ':' . '00';
                } else {
                    $start_time = date('Y-m-d', strtotime($this->data['LateEntry']['date'])) . ' ' . ($this->data['LateEntry']['start']['hours']) . ':' . $this->data['LateEntry']['start']['minutes'] . ':' . '00';
                }
            } else {
                $start_time = date('Y-m-d', strtotime($this->data['LateEntry']['date'])) . ' ' . ($this->data['LateEntry']['start']['hours']) . ':' . $this->data['LateEntry']['start']['minutes'] . ':' . '00';
            }
            foreach ($this->data['LateEntry']['user'] as $user_id) {
                $insert_late_entries = array('LateEntry' => array('user_id' => $user_id,
                        'date' => date('Y-m-d', strtotime($this->data['LateEntry']['date'])),
                        'approved' => 1,
                        'created' => $start_time));
                $this->LateEntry->saveAll($insert_late_entries);
            }
            return $this->redirect('/admin/late_entries');
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function reset() {
        $this->Session->delete('LateReport');
        return $this->redirect(array('action' => 'index'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_reset() {
        $this->Session->delete('LateReport');
        return $this->redirect(array('action' => 'admin_index'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_add_remarks() {  
        $this->layout = "admin-inner";
                $update = array(
            'LateEntry' => array(
                'id' => $this->data['id'],
                'approved' => $this->data['status'],
            )
        );

        if ($this->LateEntry->save($update)) {
            $status = $this->data['status'];
            $return = array();

            if ($status == 0) {
                $return['class'] = 'btn btn-mini btn-danger dropdown-toggle';
                $return['status'] = 'Pending';
            }
            if ($status == 1) {
                $return['class'] = 'btn btn-mini btn-success dropdown-toggle';
                $return['status'] = 'Approved';
            }
            if ($status == 2) {
                $return['class'] = 'btn btn-mini btn-inverse dropdown-toggle';
                $return['status'] = 'Declined';
            }

            echo json_encode($return);
        }
        exit;
    }

///////////////////////////////////////////////////////////////////////////////

    public function index() {
        $this->layout = "user-inner";
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('LateReport.from_date', '');
            $this->Session->write('LateReport.to_date', '');
            $this->Session->write('LateReport.approved', '');

            if (!empty($this->request->data['LateEntry']['from_date']) && !empty($this->request->data['LateEntry']['to_date'])) {
                $this->Session->write('LateReport.from_date', date('Y-m-d', strtotime($this->request->data['LateEntry']['from_date'])));
                $this->Session->write('LateReport.to_date', date('Y-m-d', strtotime($this->request->data['LateEntry']['to_date'])));
            }

            if ($this->request->data['LateEntry']['approved'] != '') {
                $this->Session->write('LateReport.approved', $this->request->data['LateEntry']['approved']);
            }


            return $this->redirect('/late_entries');
        }

        if ($this->Session->check('LateReport')) {
            $all = $this->Session->read('LateReport');

            $approve_query = '';
            if ($all['approved'] != '') {
                $approve_query = 'LateEntry.approved = ' . $all['approved'];
            }

            if ($all['from_date'] != '') {
                $late_entries = $this->LateEntry->find('all', array(
                    'conditions' => array(
                        'LateEntry.user_id' => $this->Session->read('User.id'), 
                        'LateEntry.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), $approve_query), 'order' => array('LateEntry.created DESC')));
            } else {
                $late_entries = $this->LateEntry->find('all', array(
                    'conditions' => array('LateEntry.user_id' => $this->Session->read('User.id'), $approve_query), 
                    'order' => array('LateEntry.created DESC')));
            }
        } else {
            $all = array('from_date' => '', 'to_date' => '', 'approved' => '');
            $late_entries = $this->LateEntry->find('all', array(
                'conditions' => array('LateEntry.user_id' => $this->Session->read('User.id')), 
                'order' => array('DATE(LateEntry.created) DESC')));
        }

        $this->set(compact('all'));
        $this->set(compact('late_entries'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_late_entry_by_user_id($user_id = NULL) {
        return $this->LateEntry->find('all', array(
            'conditions' => array(
                'LateEntry.user_id' => $user_id, 
                'MONTH(LateEntry.date)' => date('m'), 
                'YEAR(LateEntry.date)' => date('Y'), 
                'LateEntry.approved' => 1)));
    }

}

?>