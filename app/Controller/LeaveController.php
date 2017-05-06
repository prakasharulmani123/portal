<?php

class LeaveController extends AppController {

    public $name = 'Leave';
    public $helpers = array('Html', 'Form', 'Js', 'Paginator');
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler');

///////////////////////////////////////////////////////////////////////////////

    public function beforeFilter() {
        parent::beforefilter();
        $this->__validateLoginStatus();
        $this->loadModel('Compensation');
        $all_messages = $this->Compensation->find('all'); //or whatever you need to do to get the data
        $this->set('all_messages', $all_messages);
    }

///////////////////////////////////////////////////////////////////////////////

    public function index() {
        $this->set('cpage', 'leave');
        $this->layout = "user-inner";
        $this->loadModel('SubLeave');
        $this->loadModel('Compensation');

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('LeaveForm.from_date', '');
            $this->Session->write('LeaveForm.to_date', '');
            $this->Session->write('LeaveForm.approved', '');

            if (!empty($this->request->data['Leave']['from_date']) && !empty($this->request->data['Leave']['to_date'])) {
                $this->Session->write('LeaveForm.from_date', date('Y-m-d', strtotime($this->request->data['Leave']['from_date'])));
                $this->Session->write('LeaveForm.to_date', date('Y-m-d', strtotime($this->request->data['Leave']['to_date'])));
            }

            if ($this->request->data['Leave']['approved'] != '') {
                $this->Session->write('LeaveForm.approved', $this->request->data['Leave']['approved']);
            }

            return $this->redirect('/leave');
        }
        if ($this->Session->check('LeaveForm')) {
            $all = $this->Session->read('LeaveForm');
// $this->set(compact('comday'));
            if ($all['from_date'] != '') {
                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id'), 'Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id'), 'Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), 'Leave.approved' => $all['approved']), 'order' => array('Leave.created DESC')));
                }
            } else {
                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id')), 'order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id'), 'Leave.approved' => $all['approved']), 'order' => array('Leave.created DESC')));
                }
            }
        } else {
            $all = array('from_date' => '', 'to_date' => '', 'approved' => '');
            $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id')), 'order' => array('Leave.created DESC')));
        }

        $this->set(compact('all'));
        $this->set(compact('leaves'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_index($id = NULL) {
        $this->layout = "admin-inner";
        $this->set('cpage', 'leave');

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('Leave.user_id', '');
            $this->Session->write('Leave.from_date', '');
            $this->Session->write('Leave.to_date', '');
            $this->Session->write('Leave.approved', '');

            if ($this->request->data['Leave']['user_id'] != '') {
                $this->Session->write('Leave.user_id', $this->request->data['Leave']['user_id']);
            }

            if (!empty($this->request->data['Leave']['from_date']) && !empty($this->request->data['Leave']['to_date'])) {
                $this->Session->write('Leave.from_date', date('Y-m-d', strtotime($this->request->data['Leave']['from_date'])));
                $this->Session->write('Leave.to_date', date('Y-m-d', strtotime($this->request->data['Leave']['to_date'])));
            }

            if ($this->request->data['Leave']['approved'] != '') {
                $this->Session->write('Leave.approved', $this->request->data['Leave']['approved']);
            }
            return $this->redirect(array('action' => 'admin_index'));
        }

        if (isset($_GET['approved'])) {
            $this->Session->write('Leave.approved', $_GET['approved']);
            return $this->redirect(array('action' => 'admin_index'));
        }
        if ($this->Session->check('Leave')) {
            $all = $this->Session->read('Leave');

            if (!isset($all['user_id'], $all['from_date']) || $all['user_id'] == '' && $all['from_date'] == '') {
                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.approved' => $all['approved']), 'order' => array('Leave.created DESC')));
                }
            } elseif (!isset($all['user_id'], $all['from_date']) || $all['user_id'] != '' && $all['from_date'] == '') {

                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $all['user_id']), 'order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $all['user_id'], 'Leave.approved' => $all['approved']), 'order' => array('Leave.created DESC')));
//                print_r($leaves);exit;
                }
            } elseif (!isset($all['user_id'], $all['from_date']) || $all['user_id'] == '' && $all['from_date'] != '') {

                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), 'Leave.approved' => $all['approved']), 'order' => array('Leave.created DESC')));
                }
            } elseif (!isset($all['user_id'], $all['from_date']) || $all['user_id'] != '' && $all['from_date'] != '') {

                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $all['user_id'], 'Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $all['user_id'], 'Leave.approved' => $all['approved'], 'Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Leave.created DESC')));
                }
            }
        } else {

            $all = array('user_id' => '', 'from_date' => '', 'to_date' => '', 'approved' => '');
            $leaves = $this->Leave->find('all', array('order' => array('Leave.created DESC')));
        }
        $this->set('leaves', $this->Leave->find('all', array('order' => array('Leave.created DESC'))));
        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('leaves'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function leaveform($id = NULL) {
        $this->set('cpage', 'leave');
        $this->layout = "user-inner";
        $u_id = $this->Session->read('User.id');
        $this->loadModel('Compensation');
        $this->loadModel('SubLeave');
        $lists = $this->Compensation->find('count', array('conditions' => array('AND' => array('Compensation.user_id=' . $u_id), array('Compensation.status' => 0), array('Compensation.type' => 'L'))));
        $this->set('lists', $lists);

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Leave']['date'] = date('Y-m-d', strtotime($this->data['Leave']['date']));
            $this->request->data['Leave']['user_id'] = $this->Session->read('User.id');
            $check_leave = $this->Leave->findByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d', strtotime($this->data['Leave']['date'])));
            if (empty($check_leave)) {
                $insert_data = array();
                $check_sun_day = false;
                $leave_count = $this->user_get_all_leave_count_per_year($this->Session->read('User.id'), date('Y', strtotime($this->data['Leave']['date'])));
//$leave_count = $this->user_get_all_leave_count_per_year($this->Session->read('User.id'),date('Y'));
                $insert_data = $this->request->data;
                $status = $insert_data['Leave']['status'];
                $leave_days = $insert_data['Leave']['days'];
                if ($status == 1) {
                    $com_list = $this->Compensation->find('all', array('recursive' => -1, 'conditions' => array('AND' => array('Compensation.user_id=' . $u_id), array('Compensation.status' => 0), array('Compensation.type' => 'L'))));
                    $records = array();
                    foreach ($com_list as $adddays) {
                        $remaining_days = 0;
                        $compensation_days = $adddays['Compensation']['days'];
                        $compensation_id = $adddays['Compensation']['id'];
                        $compensation_userid = $adddays['Compensation']['user_id'];
                        $compensation_comments = $adddays['Compensation']['comments'];
                        $compensation_date = $adddays['Compensation']['date'];
                        $remaining_days = $leave_days - $compensation_days;
                        $leave_days = $remaining_days;
                        $this->Compensation->id = $compensation_id;
                        $this->Compensation->saveField('status', '1');
                        $records[] = $adddays['Compensation']['id'];
                        if ($remaining_days == '0' || $remaining_days < '0')
                            break;
                    }
                    $string = serialize($records);

                    if ($remaining_days == 0) {
                        $string = serialize($records);
                        $insert_data['Leave']['compensation_id'] = $string;
                        $insert_data['Leave']['days'] = 0.00;
                    }
                    if ($remaining_days > 0) {
                        $string = serialize($records);
                        $insert_data['Leave']['compensation_id'] = $string;
                        $insert_data['Leave']['days'] = $remaining_days;
                    }
                    if ($remaining_days < 0) {
                        $this->Compensation->create();
                        $adddays['Compensation']['days'] = 0.50;
                        $adddays['Compensation']['status'] = 1;
                        $this->Compensation->save($adddays);
                        $get_com = $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('Compensation.id=' . $compensation_id)));
                        $com_data = array(
                            'Compensation' => array(
                                'user_id' => $compensation_userid,
                                'date' => $compensation_date,
                                'days' => '0.50',
                                'comments' => $compensation_comments,
                                'status' => '0',
                                'type' => 'L',
                            )
                        );

                        $this->Compensation->saveAll($com_data);
                        $insert_data['Leave']['compensation_id'] = $string;
                        $insert_data['Leave']['days'] = 0.00;
                    }
                }
                $days = $insert_data['Leave']['days'];
                $round_days = round($insert_data['Leave']['days']);
                if ($round_days == 0) {
                    $insert_data['SubLeave'][0] = array(
                        'date' => date('Y-m-d', strtotime($this->data['Leave']['date'])),
                        'day' => '0.00',
                        'paid_casual_this_day' => '0.00',
                        'status' => '-',
                    );
                } else {
                    for ($x = 1; $x <= $round_days; $x++) {
                        $sub_day = 1;

                        if ($x == $round_days) {
                            if ($round_days != $days) {
                                $sub_day = 0.5;
                            }
                        }

                        if ($x > 1) {
                            for ($z = 1; $z <= 7; $z++) {
                                $check_day = date('D', strtotime('+' . $z . ' day', strtotime($insert_data['SubLeave'][$x - 1]['date'])));

                                $this->loadModel('Holiday');
                                $holiday = $this->Holiday->findByDate(date('Y-m-d', strtotime('+' . $z . ' day', strtotime($insert_data['SubLeave'][$x - 1]['date']))));

                                if ($check_day != 'Sun' && empty($holiday)) {
                                    $insert_data['SubLeave'][$x]['date'] = date('Y-m-d', strtotime('+' . $z . ' day', strtotime($insert_data['SubLeave'][$x - 1]['date'])));
                                    break;
                                }
                            }
                        } else {
                            $insert_data['SubLeave'][$x]['date'] = date('Y-m-d', strtotime($this->data['Leave']['date']));
                        }

                        $insert_data['SubLeave'][$x]['day'] = $sub_day;
                        $insert_data['SubLeave'][$x]['status'] = '-';
                    }
                }

//delete pending report on leave days			
                if ($insert_data['Leave']['days'] > 0.5) {
                    $this->loadModel('PendingReport');

                    foreach ($insert_data['SubLeave'] as $subleave) {
                        $pending_report_exists = $this->PendingReport->findByUserIdAndDate($insert_data['Leave']['user_id'], $subleave['date']);
                        if (!empty($pending_report_exists)) {
                            $this->PendingReport->delete($pending_report_exists['PendingReport']['id']);
                        }
                    }
                }
//end

                if ($this->Leave->saveAll($insert_data)) {
                    $this->leave_request_mail($this->Leave->getLastInsertId());


                    $this->Session->setFlash('Leave Form Submitted Sucessfully', 'flash_success');
                    return $this->redirect('/leave');
                } else {
                    $this->Session->setFlash('Failed to Send your leave form', 'flash_error');
                    return $this->redirect('/leave/leaveform');
                }
            } else {
                $this->Session->setFlash('You already sent a leave request on this day', 'flash_error');
                return $this->redirect('/leave/leaveform');
            }
        } else {
            $this->data = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id'))));
            $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->Session->read('User.id')))));
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_reset() {
        $this->Session->delete('Leave');
        return $this->redirect(array('action' => 'admin_index'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_reset_toggle() {
        $this->Session->delete('Leave');
        return $this->redirect(array('action' => 'admin_customize_leave'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function leave_reset() {
        $this->Session->delete('LeaveForm');
        return $this->redirect(array('action' => 'index'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function reset_monthly_leave_report() {
        $this->Session->delete('Leave');
        return $this->redirect(array('action' => 'monthly_report'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function leave_request_mail($id = NULL) {
        $leave = $this->Leave->read(null, $id);

        $all_to = $all_cc = $all_bcc = array();

        $add_to = $this->requestAction('emails/all_to_email');
        $add_cc = $this->requestAction('emails/all_cc_email');
        $add_bcc = $this->requestAction('emails/all_bcc_email');

        foreach ($add_to as $to) {
            $array = explode(',', $to['Email']['options']);

            foreach ($array as $key => $value) {
                if ($value == 2) {
                    $all_to[$to['Email']['id']] = $to['Email']['email'];
                }
            }
        }

        foreach ($add_cc as $cc) {
            $array = explode(',', $cc['Email']['options']);

            foreach ($array as $key => $value) {
                if ($value == 2) {
                    $all_cc[$cc['Email']['id']] = $cc['Email']['email'];
                }
            }
        }

        foreach ($add_bcc as $bcc) {
            $array = explode(',', $bcc['Email']['options']);

            foreach ($array as $key => $value) {
                if ($value == 2) {
                    $all_bcc[$bcc['Email']['id']] = $bcc['Email']['email'];
                }
            }
        }

        if (empty($all_to) && empty($all_cc) && empty($all_bcc)) {
            $this->Session->setFlash('Admin need to specify at least one destination for to, cc or bcc.<br>Contact Admin', 'flash_error');
            return $this->redirect('/leave');
        }

        $leave_date = '';
        foreach ($leave['SubLeave'] as $value):
            $leave_date .= date('jS F Y', strtotime($value['date'])) . ' & ';
        endforeach;

        $leave_date = rtrim($leave_date, ' & ');

        $this->Email->to = $all_to;
        $this->Email->cc = $all_cc;
        $this->Email->bcc = $all_bcc;
        $this->Email->subject = 'Leave Request : ' . $leave_date;
        $this->Email->replyTo = $this->Session->read('User.email');
        $this->Email->from = $this->Session->read('User.email');
        $this->Email->template = 'leaveform';
        $this->Email->sendAs = 'html';
        $this->set('leave', $leave);
        $this->set('user', $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->Session->read('User.id')))));
        $this->Email->send();
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_view($id = NULL) {
        $this->layout = "admin-inner";
        $this->set('cpage', 'leave');

        $leave = $this->Leave->find('first', array('conditions' => array('Leave.id' => $id)));

        $user_id = $leave['Leave']['user_id'];

        $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $user_id))));
        $this->set('leave', $this->Leave->find('first', array('conditions' => array('Leave.id' => $id), 'order' => array('Leave.created DESC'))));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_get_leave_requests_count() {
        return $this->Leave->find('count', array('conditions' => array('Leave.approved' => 0)));
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_leave_requests_count() {
        return $this->Leave->find('count', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id'), 'Leave.approved' => 0)));
    }

///////////////////////////////////////////////////////////////////////////////
    public function user_get_compensation_counts() {
        $this->loadModel('Compensation');
        $sum = $this->Compensation->find('first', array(
            'conditions' => array(
                'Compensation.user_id' => $this->Session->read('User.id'), 'Compensation.status' => 0),
            'fields' => array('sum(Compensation.days) as total_sum'
            )
                )
        );
        return (float) $sum[0]["total_sum"];
    }

///////////////////////////////////////////////////////////////////////////////
    public function user_get_compensation_permission_counts() {
        $this->loadModel('Compensation');
        $u_id = $this->Session->read('User.id');
        return $this->Compensation->find('count', array('recursive' => -1, 'conditions' => array('AND' => array('Compensation.user_id=' . $u_id), array('Compensation.status' => 0), array('Compensation.type' => 'P'))));
    }

///////////////////////////////////////////////////////////////////////////////

    public function view($id = NULL) {
        $this->set('cpage', 'leave');
        $this->layout = 'user-inner';

        $user_id = $this->Session->read('User.id');

        $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $user_id))));
        $this->set('leave', $this->Leave->find('first', array('conditions' => array('Leave.id' => $id), 'order' => array('Leave.created DESC'))));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_add_remarks() {
        $this->layout = "admin-inner";
        $update = array(
            'Leave' => array(
                'id' => $this->data['id'],
                'approved' => $this->data['status'],
                'remarks' => $this->data['remarks'],
            )
        );
        $leave = $this->Leave->find('first', array('recursive' => 1, 'conditions' => array('Leave.id' => $this->data['id'])));
        $leave_count = $this->user_get_all_leave_count_per_year($leave['Leave']['user_id'], date('Y'));
        $user = $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->data['user_id'])));

        if ($this->data['status'] == 1) {
            $cum_sub_day = 1;
            foreach ($leave['SubLeave'] as $sub_leave) {
                $sub_day = $sub_leave['day'];
                $update['SubLeave'][$sub_leave['id']]['id'] = $sub_leave['id'];
                if ($leave_count + $sub_day <= $user['User']['casual_leave']) {
                    $update['SubLeave'][$sub_leave['id']]['status'] = 'C';
                } else {
//newly added for paid and casual leave on same day
                    if ($leave_count + $sub_day > $user['User']['casual_leave'] && $leave_count + $sub_day == $user['User']['casual_leave'] + 0.5 && count($insert_data['SubLeave']) != 1 && $insert_data['SubLeave'][$key]['day'] != '0.5') {
                        $update['SubLeave'][$sub_leave['id']]['paid_casual_this_day'] = 0.5;
                    }
//end
                    $update['SubLeave'][$sub_leave['id']]['status'] = 'P';
                }
                $leave_count = $leave_count + $sub_day;
            }
            /*
              foreach($leave['SubLeave'] as $sub_leave){
              $sub_day = $sub_leave['day'];

              $update['SubLeave'][$sub_leave['id']]['id'] = $sub_leave['id'];

              if($leave_count + $cum_sub_day <= 12){
              $update['SubLeave'][$sub_leave['id']]['status'] = 'C';
              }
              else{
              $update['SubLeave'][$sub_leave['id']]['status'] = 'P';
              }
              $cum_sub_day = $cum_sub_day + $sub_day;
              }
             */
        } else {
            foreach ($leave['SubLeave'] as $sub_leave) {
                $update['SubLeave'][$sub_leave['id']]['id'] = $sub_leave['id'];
                $update['SubLeave'][$sub_leave['id']]['status'] = '-';
            }
        }

        if ($this->Leave->saveAll($update)) {
            $this->loadModel('Compensation');
            if ($this->data['status'] == 1) {
                $leave = $this->Leave->find('first', array('recursive' => -1, 'conditions' => array('Leave.approved' => 1, 'Leave.id' => $this->data['id'])));
                $compensation_userid = $leave['Leave']['user_id'];
                $compensation_id = $leave['Leave']['compensation_id'];
                $string = unserialize($compensation_id);
                foreach ($string as $key => $value) {
                    $lists = $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('Compensation.id=' . $value, 'Compensation.type' => 'L', 'Compensation.status' => 0)));
                    if ($lists) {
                        $data1_com = array('Compensation' => array('id' => $value, 'status' => 1));
                        $this->Compensation->save($data1_com, true, array('status'));
                    }
                }
            }
            if ($this->data['status'] == 2) {
                $this->loadModel('Compensation');
                $leave = $this->Leave->find('first', array('recursive' => -1, 'conditions' => array('AND' => array('Leave.approved' => 2, 'Leave.id' => $this->data['id']))));
                $compensation_userid = $leave['Leave']['user_id'];
                $compensation_id = $leave['Leave']['compensation_id'];
                $string = unserialize($compensation_id);
                foreach ($string as $key => $value) {
                    $lists = $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('Compensation.id=' . $value, 'Compensation.type' => 'L', 'Compensation.status' => 1)));
                    if ($lists) {
                        $data1_com = array('Compensation' => array('id' => $value, 'status' => 0));
                        $this->Compensation->save($data1_com, false, array('status'));
                    }
                }
            }

            $status = $this->data['status'];
            $return = array();
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
            if ($status != 0) {
                if ($status == 1) {
                    $subject = 'Leave Accepted';
                } elseif ($status == 2) {
                    $subject = 'Leave Declined';
                }
//				$user = $this->requestAction('users/get_user',array('pass'=>array('User.id'=>$this->data['user_id'])));
                $this->Email->to = $user['User']['email'];
                $this->Email->cc = $all_to;
                $this->Email->subject = $subject;
                $this->Email->replyTo = $this->Session->read('User.email');
                $this->Email->from = $this->Session->read('User.email');
                $this->Email->template = 'leaveaccept';
                $this->Email->sendAs = 'html';
                $this->set('user', $user);
                $this->set('leave', $this->Leave->find('first', array('conditions' => array('Leave.id' => $this->data['id']))));
                $this->set('status', $status);
                $this->Email->send();
            }
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
            $return['remarks'] = $this->data['remarks'];

            echo json_encode($return);
        }
        exit;
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_all_leave() {
        return $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id')), 'order' => array('Leave.created DESC')));
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_current_month_leave() {
        return $this->Leave->find('all', array('conditions' => array(
                        'Leave.user_id' => $this->Session->read('User.id'),
                        'MONTH(Leave.date)' => date('m'),
                        'YEAR(Leave.date)' => date('Y')
                    ),
                    'order' => array('Leave.created DESC')));
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_all_leave_count() {
        $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id'), 'Leave.approved' => 1, 'YEAR(Leave.date)' => date('Y')), 'order' => array('Leave.created DESC')));
        if ($leaves) {
            $leave_count = 0;
            foreach ($leaves as $leave) {
                $leave_count += $leave['Leave']['days'];
            }
            return $leave_count;
        } else {
            return 0;
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function monthly_report() {
        $this->set('cpage', 'leave_month_report');
        $this->layout = 'user-inner';
        $this->loadModel('SubLeave');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('Leave.month', '');
            $this->Session->write('Leave.year', '');

            if (!empty($this->request->data['Leave']['month'])) {
                $this->Session->write('Leave.month', date('m', strtotime($this->request->data['Leave']['month'])));
                $this->Session->write('Leave.year', date('Y', strtotime($this->request->data['Leave']['month'])));
            }

            return $this->redirect(array('action' => 'monthly_report'));
        }

        if ($this->Session->check('Leave')) {
            $all = $this->Session->read('Leave');

            if ($all['month'] != '' && $all['year'] != '') {
                $sub_leaves = $this->SubLeave->find('all', array('recursive' => 1, 'conditions' => array('MONTH(SubLeave.date)' => $all['month'], 'YEAR(SubLeave.date)' => $all['year'])));
                $leaves_id = array();
                foreach ($sub_leaves as $key => $sub_leave) {
                    if ($sub_leave['Leave']['user_id'] == $this->Session->read('User.id')) {
                        $leaves_id[$key] = $sub_leave['Leave']['id'] . ',';
                    }
                }
                $leaves = $this->Leave->find('all', array('conditions' => array('Leave.id' => $leaves_id), 'order' => array('Leave.date ASC')));
            }
        } else {
            $all = array('month' => '', 'year' => '');
            $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id'), 'MONTH(Leave.date)' => date('m'), 'YEAR(Leave.date)' => date('Y')), 'order' => array('Leave.date ASC')));
        }

        $this->set(compact('all'));
        $this->set(compact('leaves'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_all_leave_count_per_month($month = NULL, $year = NULL) {
        $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id'), 'Leave.approved' => 1, 'MONTH(Leave.date)' => $month, 'YEAR(Leave.date)' => $year), 'order' => array('Leave.created DESC')));

        if ($leaves) {
            $leave_count = 0;
            foreach ($leaves as $leave) {
                $leave_count += $leave['Leave']['days'];
            }

            return $leave_count;
        } else {
            return 0;
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_all_leave_count_not_month($month = NULL, $year = NULL) {
        $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $this->Session->read('User.id'), 'Leave.approved' => 1, 'MONTH(Leave.date) !=' => $month, 'YEAR(Leave.date)' => $year), 'order' => array('Leave.created DESC')));

        if ($leaves) {
            $leave_count = 0;
            foreach ($leaves as $leave) {
                $leave_count += $leave['Leave']['days'];
            }

            return $leave_count;
        } else {
            return 0;
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_current_month_leave_approved($user_id = NULL, $month = NULL, $year = NULL) {
        $this->loadModel('SubLeave');

        $sub_leaves = $this->SubLeave->find('all', array('recursive' => 1, 'conditions' => array('MONTH(SubLeave.date)' => $month, 'YEAR(SubLeave.date)' => $year)));

        $leave_days = array();

        foreach ($sub_leaves as $sub_leave) {
            if ($sub_leave['Leave']['approved'] == 1 && $sub_leave['Leave']['user_id'] == $user_id) {
                $leave_days[$sub_leave['SubLeave']['date']] = $sub_leave['SubLeave']['day'];
            }
        }
        return $leave_days;
//		return $this->Leave->find('list', array('fields'=>array('Leave.days','Leave.date'), 'conditions'=>array('Leave.user_id'=>$user_id, 'MONTH(Leave.date)'=>$month, 'YEAR(Leave.date)'=>$year,'Leave.approved'=>1), 'order'=>array('Leave.created DESC')));
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_all_leave_count_per_year($user_id = NULL, $year = NULL) {
        $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $user_id, 'Leave.approved' => 1, 'YEAR(Leave.date)' => $year), 'order' => array('Leave.created DESC')));

        if ($leaves) {
            $leave_count = 0;
            foreach ($leaves as $leave) {
                $leave_count += $leave['Leave']['days'];
            }

            return $leave_count;
        } else {
            return 0;
        }
    }

///////////////////////////////////////////////////////////////////////////////
//changed on 22-02-2014
    public function get_all_leave_count_per_month_per_status($user_id = NULL, $month = NULL, $year = NULL, $status = NULL) {
        $this->loadModel('SubLeave');
        $sub_leaves = $this->SubLeave->find('all', array('recursive' => 1, 'conditions' => array('YEAR(SubLeave.date)' => $year, 'MONTH(SubLeave.date)' => $month, 'SubLeave.status' => $status), 'order' => array('SubLeave.created DESC')));

        $all_sub_leaves = $this->SubLeave->find('all', array('recursive' => 1, 'conditions' => array('YEAR(SubLeave.date)' => $year, 'MONTH(SubLeave.date)' => $month, 'SubLeave.status' => 'P'), 'order' => array('SubLeave.created DESC')));

        $paid_causual_this_day = 0;

        foreach ($all_sub_leaves as $all_sub_leave) {
            if ($all_sub_leave['Leave']['user_id'] == $user_id) {
                $paid_causual_this_day += $all_sub_leave['SubLeave']['paid_casual_this_day'];
            }
        }

        $count = 0;

        if ($sub_leaves) {
            foreach ($sub_leaves as $sub_leave) {
                if ($sub_leave['Leave']['user_id'] == $user_id) {
                    $count += $sub_leave['SubLeave']['day'];
                }
            }
        }

        $status == 'C' ? $count = $count + $paid_causual_this_day : $count = $count - $paid_causual_this_day;

        if ($count < 0) {
            $count = 0;
        }
        return $count;
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_monthly_report() {
        $this->layout = "admin-inner";
        $this->set('cpage', 'leave_month_report');
        $this->loadModel('SubLeave');

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('LeaveMonth.user_id', '');
            $this->Session->write('LeaveMonth.month', '');
            $this->Session->write('LeaveMonth.year', '');

            if ($this->request->data['Leave']['user_id'] != '') {
                $this->Session->write('LeaveMonth.user_id', $this->request->data['Leave']['user_id']);
            }

            if (!empty($this->request->data['Leave']['month'])) {
                $this->Session->write('LeaveMonth.month', date('m', strtotime($this->request->data['Leave']['month'])));
                $this->Session->write('LeaveMonth.year', date('Y', strtotime($this->request->data['Leave']['month'])));
            }

            return $this->redirect(array('action' => 'admin_monthly_report'));
        }

        if ($this->Session->check('LeaveMonth')) {
            $all = $this->Session->read('LeaveMonth');

            if ($all['user_id'] != '' && $all['month'] != '' && $all['year'] != '') {
                $sub_leaves = $this->SubLeave->find('all', array('recursive' => 1, 'conditions' => array('MONTH(SubLeave.date)' => $all['month'], 'YEAR(SubLeave.date)' => $all['year'])));
                $leaves_id = array();
                foreach ($sub_leaves as $key => $sub_leave) {
                    if ($sub_leave['Leave']['user_id'] == $all['user_id']) {
                        $leaves_id[$key] = $sub_leave['Leave']['id'] . ',';
                    }
                }
                $leaves = $this->Leave->find('all', array('conditions' => array('Leave.id' => $leaves_id), 'order' => array('Leave.date ASC')));
            }
        } else {
            $all = array('user_id' => '', 'month' => '', 'year' => '');
        }
        $this->set(compact('User'));
        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('leaves'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_reset_monthly_leave_report() {
        $this->Session->delete('LeaveMonth');
        return $this->redirect(array('action' => 'monthly_report', 'admin' => true));
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_all_leave_count($user_id = NULL) {
        $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $user_id, 'Leave.approved' => 1, 'YEAR(Leave.date)' => date('Y')), 'order' => array('Leave.created DESC')));

        if ($leaves) {
            $leave_count = 0;
            foreach ($leaves as $leave) {
                $leave_count += $leave['Leave']['days'];
            }

            return $leave_count;
        } else {
            return 0;
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_leave_by_userid_date($user_id = NULL, $date = NULL) {
        $this->loadModel('SubLeave');
        $sub_leaves = $this->SubLeave->find('all', array('recursive' => 1, 'conditions' => array('SubLeave.date' => date('Y-m-d', strtotime($date)))));

        $leave_id = '';
        foreach ($sub_leaves as $sub_leave) {
            if ($sub_leave['Leave']['user_id'] == $user_id) {
                $leave_id = $sub_leave['SubLeave']['leave_id'];
                break;
            }
        }

        return $this->Leave->find('first', array('conditions' => array('Leave.id' => $leave_id)));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_customize_leave() {
        $this->layout = "admin-inner";
        $this->set('cpage', 'leave');

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('Leave.user_id', '');
            $this->Session->write('Leave.from_date', '');
            $this->Session->write('Leave.to_date', '');
            $this->Session->write('Leave.approved', '');

            if ($this->request->data['Leave']['user_id'] != '') {
                $this->Session->write('Leave.user_id', $this->request->data['Leave']['user_id']);
            }

            if (!empty($this->request->data['Leave']['from_date']) && !empty($this->request->data['Leave']['to_date'])) {
                $this->Session->write('Leave.from_date', date('Y-m-d', strtotime($this->request->data['Leave']['from_date'])));
                $this->Session->write('Leave.to_date', date('Y-m-d', strtotime($this->request->data['Leave']['to_date'])));
            }

            if ($this->request->data['Leave']['approved'] != '') {
                $this->Session->write('Leave.approved', $this->request->data['Leave']['approved']);
            }
            return $this->redirect(array('action' => 'admin_customize_leave'));
        }

        if ($this->Session->check('Leave')) {
            $all = $this->Session->read('Leave');

            if ($all['user_id'] == '' && $all['from_date'] == '') {
                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.approved' => $all['approved']), 'order' => array('Leave.created DESC')));
                }
            } elseif ($all['user_id'] != '' && $all['from_date'] == '') {
                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $all['user_id']), 'order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $all['user_id'], 'Leave.approved' => $all['approved']), 'order' => array('Leave.created DESC')));
                }
            } elseif ($all['user_id'] == '' && $all['from_date'] != '') {
                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), 'Leave.approved' => $all['approved']), 'order' => array('Leave.created DESC')));
                }
            } elseif ($all['user_id'] != '' && $all['from_date'] != '') {
                if ($all['approved'] == '') {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $all['user_id'], 'Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Leave.created DESC')));
                } else {
                    $leaves = $this->Leave->find('all', array('conditions' => array('Leave.user_id' => $all['user_id'], 'Leave.approved' => $all['approved'], 'Leave.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Leave.created DESC')));
                }
            }
        } else {
            $all = array('user_id' => '', 'from_date' => '', 'to_date' => '', 'approved' => '');
            $leaves = $this->Leave->find('all', array('order' => array('Leave.created DESC')));
        }
        $this->set('leaves', $this->Leave->find('all', array('order' => array('Leave.created DESC'))));
        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('leaves'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_get_sub_leave_days_ajax($leave_id) {
        $this->layout = "admin-inner";
        $this->loadModel('SubLeave');
        $this->SubLeave->recursive = -1;
        $sub_leaves = $this->SubLeave->findAllByLeaveId($leave_id);

        $return = '';

        foreach ($sub_leaves as $sub_leave):
            if ($sub_leave['SubLeave']['status'] == 'C') {
                $casual_selected = 'selected';
                $paid_selected = '';
            } else {
                $casual_selected = '';
                $paid_selected = 'selected';
            }

            $return .= '<div><b>' . date('d-m-Y', strtotime($sub_leave['SubLeave']['date'])) . '</b> :&nbsp;(' . $sub_leave['SubLeave']['day'] . ' day) &nbsp;';
            $return .= '<select style="width:90px; margin-top:6px;" class="sub-leave-select" id="' . $sub_leave['SubLeave']['id'] . '" onchange="toggle_leave(this.value,' . $sub_leave['SubLeave']['id'] . ')">';
            $return .= '<option value="C" ' . $casual_selected . '>Casual</option>';
            $return .= '<option value="P" ' . $paid_selected . '>Paid</option>';
            $return .= '</select>';
            $return .= '<img id="loader_' . $sub_leave['SubLeave']['id'] . '" title="w_loader_ge.gif" src="' . $this->base . '/img/admin/loaders/w_loader_ge.gif" style="display:none">&nbsp; &nbsp;<span id="span_' . $sub_leave['SubLeave']['id'] . '"></span>';
            $return .= '</div>';
        endforeach;

        echo json_encode($return);
        exit;
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_update_sub_leave_days_ajax() {
        $this->layout = "admin-inner";
        $this->loadModel('SubLeave');

        $update = array('SubLeave' => $this->data);

        $sub_leave = $this->SubLeave->findById($this->data['id']);

        $return = array();
        if ($this->SubLeave->save($update)) {
            $return['result'] = '<img alt="success" src="' . $this->base . '/img/icon_1.png">';
        } else {
            $return['result'] = '<img alt="fails" src="' . $this->base . '/img/icon_0.png">';
        }
        $return['date'] = date('d-m-Y', strtotime($sub_leave['SubLeave']['date']));
        ;

        echo json_encode($return);
        exit;
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_add() {
        $this->layout = "admin-inner";
        $this->set('cpage', 'leave');
        $this->layout = "admin-inner";
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Leave']['date'] = date('Y-m-d', strtotime($this->data['Leave']['date']));
            $this->request->data['Leave']['user_id'] = $this->data['Leave']['user_id'];

            $user = $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->data['Leave']['user_id'])));

            $insert_data = array();
            $check_sun_day = false;

            $leave_count = $this->user_get_all_leave_count_per_year($this->data['Leave']['user_id'], date('Y', strtotime($this->data['Leave']['date'])));
//			$leave_count = $this->user_get_all_leave_count_per_year($this->data['Leave']['user_id'],date('Y'));
            $insert_data = $this->request->data;

            $days = $this->request->data['Leave']['days'];
            $round_days = round($this->request->data['Leave']['days']);

            for ($x = 1; $x <= $round_days; $x++) {
                $sub_day = 1;

                if ($x == $round_days) {
                    if ($round_days != $days) {
                        $sub_day = 0.5;
                    }
                }

                if ($x > 1) {
                    for ($z = 1; $z <= 7; $z++) {
                        $check_day = date('D', strtotime('+' . $z . ' day', strtotime($insert_data['SubLeave'][$x - 1]['date'])));

                        $this->loadModel('Holiday');
                        $holiday = $this->Holiday->findByDate(date('Y-m-d', strtotime('+' . $z . ' day', strtotime($insert_data['SubLeave'][$x - 1]['date']))));

                        if ($check_day != 'Sun' && empty($holiday)) {
                            $insert_data['SubLeave'][$x]['date'] = date('Y-m-d', strtotime('+' . $z . ' day', strtotime($insert_data['SubLeave'][$x - 1]['date'])));
                            break;
                        }
                    }
                } else {
                    $insert_data['SubLeave'][$x]['date'] = date('Y-m-d', strtotime($this->data['Leave']['date']));
                }

                $insert_data['SubLeave'][$x]['day'] = $sub_day;
                $insert_data['SubLeave'][$x]['status'] = '-';
            }

            if ($this->data['Leave']['paid'] == 'P') {
                foreach ($insert_data['SubLeave'] as $key => $sub_leave) {
                    $insert_data['SubLeave'][$key]['status'] = 'P';
                }
            } else {
                foreach ($insert_data['SubLeave'] as $key => $sub_leave) {
                    $sub_day = $sub_leave['day'];

                    if ($leave_count + $sub_day <= $user['User']['casual_leave']) {
                        $insert_data['SubLeave'][$key]['status'] = 'C';
                    } else {
//newly added for paid and casual leave on same day
                        if ($leave_count + $sub_day > $user['User']['casual_leave'] && $leave_count + $sub_day == $user['User']['casual_leave'] + 0.5 && count($insert_data['SubLeave']) != 1 && $insert_data['SubLeave'][$key]['day'] != '0.5') {
                            $insert_data['SubLeave'][$key]['paid_casual_this_day'] = 0.5;
                        }
//end
                        $insert_data['SubLeave'][$key]['status'] = 'P';
                    }
                    $leave_count = $leave_count + $sub_day;
                }
            }

//delete pending report on leave days			
            if ($insert_data['Leave']['days'] > 0.5) {
                $this->loadModel('PendingReport');

                foreach ($insert_data['SubLeave'] as $subleave) {
                    $pending_report_exists = $this->PendingReport->findByUserIdAndDate($insert_data['Leave']['user_id'], $subleave['date']);
                    if (!empty($pending_report_exists)) {
                        $this->PendingReport->delete($pending_report_exists['PendingReport']['id']);
                    }
                }
            }
//end

            if ($this->Leave->saveAll($insert_data)) {
//				$this->leave_request_mail($this->Leave->getLastInsertId());
                $this->Session->setFlash('Leave Form Submitted Sucessfully', 'flash_success');
                return $this->redirect('add');
            } else {
                $this->Session->setFlash('Failed to Send your leave form', 'flash_error');
            }
        } else {
            $this->set('users', $this->requestAction('users/get_all_users'));
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_all_leave_count_by_user_id_and_status($user_id = NULL, $status = NULL, $year = NULL) {
        $this->loadModel('SubLeave');
        $sub_leaves = $this->SubLeave->find('all', array('recursive' => 1, 'conditions' => array('YEAR(SubLeave.date)' => $year, 'SubLeave.status' => $status), 'order' => array('SubLeave.created DESC')));

        $all_sub_leaves = $this->SubLeave->find('all', array('recursive' => 1, 'conditions' => array('YEAR(SubLeave.date)' => $year, 'SubLeave.status' => 'P'), 'order' => array('SubLeave.created DESC')));

        $paid_causual_this_day = 0;

        foreach ($all_sub_leaves as $all_sub_leave) {
            if ($all_sub_leave['Leave']['user_id'] == $user_id) {
                $paid_causual_this_day += $all_sub_leave['SubLeave']['paid_casual_this_day'];
            }
        }

        $count = 0;

        if ($sub_leaves) {
            foreach ($sub_leaves as $sub_leave) {
                if ($sub_leave['Leave']['user_id'] == $user_id) {
                    $count += $sub_leave['SubLeave']['day'];
                }
            }
        }

        $status == 'C' ? $count = $count + $paid_causual_this_day : $count = $count - $paid_causual_this_day;

        if ($count < 0) {
            $count = 0;
        }
        return $count;
    }

}

?>