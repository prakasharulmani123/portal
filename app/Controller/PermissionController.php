<?php

class PermissionController extends AppController {

    public $name = 'Permission';
    public $helpers = array('Html', 'Form', 'Js', 'Paginator');
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Paginator');

///////////////////////////////////////////////////////////////////////////////

    public function beforeFilter() {
        parent::beforefilter();
        $this->__validateLoginStatus();
    }

///////////////////////////////////////////////////////////////////////////////

    public function index() {
        $this->set('cpage', 'permission');
        $this->layout = "user-inner";
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('Permission.from_date', '');
            $this->Session->write('Permission.to_date', '');
            $this->Session->write('Permission.approved', '');
            $this->Session->write('Permission.permission_leave', '');

            if (!empty($this->request->data['Permission']['from_date']) && !empty($this->request->data['Permission']['to_date'])) {
                $this->Session->write('Permission.from_date', date('Y-m-d', strtotime($this->request->data['Permission']['from_date'])));
                $this->Session->write('Permission.to_date', date('Y-m-d', strtotime($this->request->data['Permission']['to_date'])));
            }

            if ($this->request->data['Permission']['approved'] != '') {
                $this->Session->write('Permission.approved', $this->request->data['Permission']['approved']);
            }
            /*
              if($this->request->data['Permission']['permission_leave'] != '')
              {
              $this->Session->write('Permission.permission_leave', $this->request->data['Permission']['permission_leave']);
              }
             */

            return $this->redirect('/permission');
        }

        if ($this->Session->check('Permission')) {
            $all = $this->Session->read('Permission');

            $permission_query = '';
            /*
              if($all['permission_leave'] != ''){
              $permission_query = 'Permission.permission_leave = '.$all['permission_leave'];
              }
             */

            $approve_query = '';
            if ($all['approved'] != '') {
                $approve_query = 'Permission.approved = ' . $all['approved'];
            }

            if ($all['from_date'] != '') {
                $leaves = $this->Permission->find('all', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id'), 'Permission.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), $permission_query, $approve_query), 'order' => array('Permission.created DESC')));
            } else {
                $leaves = $this->Permission->find('all', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id'), $permission_query, $approve_query), 'order' => array('Permission.created DESC')));
            }
        } else {
            $all = array('from_date' => '', 'to_date' => '', 'approved' => '', 'permission_leave' => '');
            $leaves = $this->Permission->find('all', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id')), 'order' => array('Permission.created DESC')));
        }

        $this->set(compact('all'));
        $this->set(compact('leaves'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_index() {
          $this->layout = "admin-inner";
        $this->set('cpage', 'permission');

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('Permission.user_id', '');
            $this->Session->write('Permission.from_date', '');
            $this->Session->write('Permission.to_date', '');
            $this->Session->write('Permission.approved', '');
            $this->Session->write('Permission.permission_leave', '');

            if ($this->request->data['Permission']['user_id'] != '') {
                $this->Session->write('Permission.user_id', $this->request->data['Permission']['user_id']);
            }

            if (!empty($this->request->data['Permission']['from_date']) && !empty($this->request->data['Permission']['to_date'])) {
                $this->Session->write('Permission.from_date', date('Y-m-d', strtotime($this->request->data['Permission']['from_date'])));
                $this->Session->write('Permission.to_date', date('Y-m-d', strtotime($this->request->data['Permission']['to_date'])));
            }

            if ($this->request->data['Permission']['approved'] != '') {
                $this->Session->write('Permission.approved', $this->request->data['Permission']['approved']);
            }
            /*
              if($this->request->data['Permission']['permission_leave'] != '')
              {
              $this->Session->write('Permission.permission_leave', $this->request->data['Permission']['permission_leave']);
              }
             */

            return $this->redirect(array('action' => 'admin_index'));
        }

        if ($this->Session->check('Permission')) {
            $all = $this->Session->read('Permission');

            $permission_query = '';
            /*
              if($all['permission_leave'] != ''){
              $permission_query = 'Permission.permission_leave = '.$all['permission_leave'];
              }
             */

            $approve_query = '';
            if ($all['approved'] != '') {
                $approve_query = 'Permission.approved = ' . $all['approved'];
            }

            if ($all['user_id'] == '' && $all['from_date'] == '') {
                $leaves = array('conditions' => array($permission_query, $approve_query), 'order' => array('Permission.created' => 'DESC'));
            } elseif ($all['user_id'] != '' && $all['from_date'] == '') {
                $leaves = array('conditions' => array('Permission.user_id' => $all['user_id'], $permission_query, $approve_query), 'order' => array('Permission.created' => 'DESC'));
            } elseif ($all['user_id'] == '' && $all['from_date'] != '') {
                $leaves = array('conditions' => array('Permission.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), $permission_query, $approve_query), 'order' => array('Permission.created' => 'DESC'));
            } elseif ($all['user_id'] != '' && $all['from_date'] != '') {
                $leaves = array('conditions' => array('Permission.user_id' => $all['user_id'], 'Permission.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), $permission_query, $approve_query), 'order' => array('Permission.created' => 'DESC'));
            }
        } else {
            $all = array('user_id' => '', 'from_date' => '', 'to_date' => '', 'approved' => '', 'permission_leave' => '');
            $leaves = array('order' => array('Permission.created' => 'DESC'));
        }
        $leaves = array_merge($leaves, array('limit' => 25));
        $this->Paginator->settings = $leaves;
        $leaves = $this->Paginator->paginate('Permission');

//        $this->set('leaves', $this->Permission->find('all', array('order' => array('Permission.created' => 'DESC'))));
        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('leaves'));
        if ($this->request->is('ajax')) {
            $this->render('admin_index', 'ajaxpagination'); // View, Layout
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function permission_add() {
        $this->set('cpage', 'permission');
        $this->layout = "user-inner";
        $u_id = $this->Session->read('User.id');

        if ($this->request->is('post') || $this->request->is('put')) {
            $check_permission = $this->Permission->findByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d', strtotime($this->data['Permission']['date'])));

            if (empty($check_permission)) {
                $this->request->data['Permission']['date'] = date('Y-m-d', strtotime($this->data['Permission']['date']));
                $this->request->data['Permission']['user_id'] = $this->Session->read('User.id');

//update from and to time
                if ($this->data['Permission']['from']['meridian'] == 'pm') {
                    if ($this->data['Permission']['from']['hours'] != '12') {
                        $this->request->data['Permission']['from_time'] = date('Y-m-d', strtotime($this->data['Permission']['date'])) . ' ' . ($this->data['Permission']['from']['hours'] + 12) . ':' . $this->data['Permission']['from']['minutes'] . ':' . '00';
                    } else {
                        $this->request->data['Permission']['from_time'] = date('Y-m-d', strtotime($this->data['Permission']['date'])) . ' ' . ($this->data['Permission']['from']['hours']) . ':' . $this->data['Permission']['from']['minutes'] . ':' . '00';
                    }
                } else {
                    $this->request->data['Permission']['from_time'] = date('Y-m-d', strtotime($this->data['Permission']['date'])) . ' ' . ($this->data['Permission']['from']['hours']) . ':' . $this->data['Permission']['from']['minutes'] . ':' . '00';
                }

                if ($this->data['Permission']['to']['meridian'] == 'pm') {
                    if ($this->data['Permission']['to']['hours'] != '12') {
                        $this->request->data['Permission']['to_time'] = date('Y-m-d', strtotime($this->data['Permission']['date'])) . ' ' . ($this->data['Permission']['to']['hours'] + 12) . ':' . $this->data['Permission']['to']['minutes'] . ':' . '00';
                    } else {
                        $this->request->data['Permission']['to_time'] = date('Y-m-d', strtotime($this->data['Permission']['date'])) . ' ' . ($this->data['Permission']['to']['hours']) . ':' . $this->data['Permission']['to']['minutes'] . ':' . '00';
                    }
                } else {
                    $this->request->data['Permission']['to_time'] = date('Y-m-d', strtotime($this->data['Permission']['date'])) . ' ' . ($this->data['Permission']['to']['hours']) . ':' . $this->data['Permission']['to']['minutes'] . ':' . '00';
                }

                $count = $this->Permission->find('count', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id'), 'MONTH(Permission.date)' => date('m'), /* 'Permission.permission_leave'=>1, */ 'Permission.approved' => 1), 'order' => array('Permission.created DESC')));

                /* 			if($count <= 3){
                  $datetime1 = new DateTime($this->data['Permission']['from_time']);
                  $datetime2 = new DateTime($this->data['Permission']['to_time']);

                  $interval = $datetime1->diff($datetime2);
                  $minutes = ($interval->format('%h')*60)+($interval->format('%i'));

                  if($minutes > 120){
                  $this->request->data['Permission']['permission_leave'] = 2;
                  }
                  }
                  else{
                  $this->request->data['Permission']['permission_leave'] = 2;
                  }
                 */
                $pr_count = $this->Permission->find('count', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id'), 'MONTH(Permission.date)' => date('m'), 'YEAR(Permission.date)' => date('Y'), 'Permission.approved !=' => 2)));
                $this->loadModel('Compensation');
                $cm_count = $this->Compensation->find('count', array('conditions' => array('AND' => array('Compensation.user_id=' . $this->Session->read('User.id')), array('Compensation.status' => 0), array('Compensation.type' => 'P'))));

                if ($pr_count >= 3 && $cm_count > 0) {
                    $cm_userid = $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('AND' => array('Compensation.user_id' => $this->Session->read('User.id')), array('Compensation.status' => 0), array('Compensation.type' => 'P'))));
                    $cm_id = $cm_userid['Compensation']['id'];
                    $data1_com = array('Compensation' => array('id' => $cm_id, 'status' => true));
                    $this->Compensation->save($data1_com, true, array('status'));
                    $this->request->data['Permission']['compensation_id'] = $cm_id;
                }
                if ($this->Permission->save($this->request->data)) {
                    $this->permission_request_mail($this->Permission->getLastInsertId());
                    $this->Session->setFlash('Permission Request Submitted Sucessfully', 'flash_success');
                    return $this->redirect('/permission');
                } else {
                    $this->Session->setFlash('Failed to Send your Permission', 'flash_error');
                    return $this->redirect('/permission/permission_add');
                }
            } else {
                $this->Session->setFlash('You already sent a permission on this day. One permission allowed per day', 'flash_error');
                $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->Session->read('User.id')))));
//                return $this->redirect('/permission/permission_add');
            }
        } else {
            $this->data = $this->Permission->find('all', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id'))));
            $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->Session->read('User.id')))));
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_reset() {
          $this->layout = "admin-inner";
        $this->Session->delete('Permission');
        return $this->redirect(array('action' => 'admin_index'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function permission_reset() {
        $this->Session->delete('Permission');
        return $this->redirect(array('action' => 'index'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function permission_request_mail($id = NULL) {
        $permission = $this->Permission->read(null, $id);

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
            return $this->redirect('/permission');
        }

        $this->Email->to = $all_to;
        $this->Email->cc = $all_cc;
        $this->Email->bcc = $all_bcc;
        $this->Email->subject = 'Permission Request : ' . ' ' . date('jS F Y', strtotime($permission['Permission']['date']));
        $this->Email->replyTo = $this->Session->read('User.email');
        $this->Email->from = $this->Session->read('User.email');
        $this->Email->template = 'permission';
        $this->Email->sendAs = 'html';
        $this->set('permission', $permission);
        $this->set('user', $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->Session->read('User.id')))));
        $this->Email->send();
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_view($id = NULL) {
          $this->layout = "admin-inner";
        $this->set('cpage', 'leave');

        $leave = $this->Permission->find('first', array('conditions' => array('Permission.id' => $id)));
        $user_id = $leave['Permission']['user_id'];

        $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $user_id))));
        $this->set('leave', $this->Permission->find('first', array('conditions' => array('Permission.id' => $id), 'order' => array('Permission.created DESC'))));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_get_permission_requests_count() {
        return $this->Permission->find('count', array('conditions' => array('Permission.approved' => 0)));
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_permission_requests_count() {
        return $this->Permission->find('count', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id'), 'Permission.approved' => 0)));
    }

///////////////////////////////////////////////////////////////////////////////

    public function view($id = NULL) {
        $this->set('cpage', 'leave');
        $this->layout = 'user-inner';

        $user_id = $this->Session->read('User.id');

        $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $user_id))));
        $this->set('leave', $this->Permission->find('first', array('conditions' => array('Permission.id' => $id), 'order' => array('Permission.created DESC'))));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_add_remarks() {
          $this->layout = "admin-inner";
        $update = array(
            'Permission' => array(
                'id' => $this->data['id'],
                'approved' => $this->data['status'],
                'remarks' => $this->data['remarks'],
            )
        );
        if ($this->Permission->save($update)) {

            $this->permission_remarks($this->data['id']);

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

    public function permission_remarks($id) {
        //Single Status Change Approved && Declined Function--
        $this->loadModel('Compensation');
        $app = $this->Permission->find('first', array('recursive' => -1, 'conditions' => array('AND' => array('Permission.id' => $id))));
        $apprv = $app['Permission']['approved'];
        $com_id = $app['Permission']['compensation_id'];
        if (!empty($com_id)) {
            if ($this->data['status'] == 1 && $apprv == 1) {
                $app_com = array('Compensation' => array('id' => $com_id, 'status' => 1));
                $this->Compensation->save($app_com, true, array('status'));
            }
            if ($this->data['status'] == 2 && $apprv == 2) {
                $data1_com = array('Compensation' => array('id' => $com_id, 'status' => 0));
                $this->Compensation->save($data1_com, false, array('status'));
            }
            //Bulk Status Change Approved && Declined Function--
            if ($this->data['status'] == 1) {
                $this->loadModel('Compensation');
                $appr_id = $this->Permission->find('first', array('recursive' => -1, 'conditions' => array('AND' => array('Permission.approved' => 1, 'Permission.id' => $id))));
                $com_userid = $appr_id['Permission']['user_id'];
                $comp_id = $appr_id['Permission']['compensation_id'];
                $aplist = $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('Compensation.id=' . $comp_id, 'Compensation.user_id' => $com_userid, 'Compensation.status' => 0, 'Compensation.type' => 'P')));
                if ($aplist) {
                    $comid = array('Compensation' => array('id' => $comp_id, 'status' => 1));
                    $this->Compensation->save($comid, true, array('status'));
                }
            }

            if ($this->data['status'] == 2) {
                $this->loadModel('Compensation');
                $dec_id = $this->Permission->find('first', array('recursive' => -1, 'conditions' => array('AND' => array('Permission.approved' => 2, 'Permission.id' => $id))));
                $com_userid = $dec_id['Permission']['user_id'];
                $compensation_id = $dec_id['Permission']['compensation_id'];
                $lists = $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('Compensation.id=' . $compensation_id, 'Compensation.user_id' => $com_userid, 'Compensation.status' => 1, 'Compensation.type' => 'P')));
                if ($lists) {
                    $data1_com = array('Compensation' => array('id' => $compensation_id, 'status' => 0));
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
                $subject = 'Permission Accepted';
            } elseif ($status == 2) {
                $subject = 'Permission Declined';
            }
            $user = $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->data['user_id'])));
            $this->Email->to = $user['User']['email'];
            $this->Email->cc = $all_to;
            $this->Email->subject = $subject;
            $this->Email->replyTo = $this->Session->read('User.email');
            $this->Email->from = $this->Session->read('User.email');
            $this->Email->template = 'permissionaccept';
            $this->Email->sendAs = 'html';
            $this->set('user', $user);
            $this->set('permission', $this->Permission->find('first', array('conditions' => array('Permission.id' => $this->data['id']))));
            $this->set('status', $status);
            $this->Email->send();
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_all_permission() {
        return $this->Permission->find('all', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id')), 'order' => array('Permission.created DESC')));
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_current_month_permission() {
        return $this->Permission->find('count', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id'), 'MONTH(Permission.date)' => date('m'), 'YEAR(Permission.date)' => date('Y')/* , 'Permission.permission_leave'=>1 */, 'Permission.approved' => 1), 'order' => array('Permission.created DESC')));
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_current_month_permission_new() {
        return $this->Permission->find('count', array('conditions' => array('Permission.user_id' => $this->Session->read('User.id'), 'MONTH(Permission.date)' => date('m'), 'YEAR(Permission.date)' => date('Y'), /* 'Permission.permission_leave'=>1, */ 'Permission.approved !=' => 2)));
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_get_current_month_all_permission() {
        return $this->Permission->find('all', array('conditions' => array(
                        'Permission.user_id' => $this->Session->read('User.id'),
                        'MONTH(Permission.date)' => date('m'),
                        'YEAR(Permission.date)' => date('Y')
                    ),
                    'order' => array('Permission.date DESC')));
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_permission_approved_per_month($user_id = NULL, $month = NULL, $year = NULL) {
        return $this->Permission->find('list', array('fields' => array('Permission.id', 'Permission.date'), 'conditions' => array('Permission.user_id' => $user_id, 'MONTH(Permission.date)' => $month, 'YEAR(Permission.date)' => $year, 'Permission.approved' => 1), 'order' => array('Permission.created DESC')));
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_permission_by_userid_date($user_id = NULL, $date = NULL) {
        return $this->Permission->find('first', array('conditions' => array('Permission.user_id' => $user_id, 'Permission.date' => date('Y-m-d', strtotime($date)))));
    }

///////////////////////////////////////////////////////////////////////////////

    public function late_hour_permission($get_in_time = NULL) {/*
      $get_in_time =  str_replace('-',':',$get_in_time);

      $time1 = new DateTime('10:35:00');
      $time2 = new DateTime($get_in_time);
      //		$time2 = new DateTime(date('H:i:s'));
      $interval = $time1->diff($time2);

      $permission_exists = $this->Permission->find('first', array('conditions'=>array('Permission.user_id'=>$this->Session->read('User.id'),'Permission.date'=>date('Y-m-d'), 'Permission.approved !='=>2)));

      if(empty($permission_exists)){
      $current_permission_count = count($this->get_permission_approved_per_month($this->Session->read('User.id'),date('m'),date('Y')));

      $late_time = floatval($interval->format('%h').'.'.$interval->format('%i'));

      if($current_permission_count >= 2 || $late_time > 1.30){
      $this->loadModel('Leave');

      $leave_exists = $this->Leave->find('first', array('conditions'=>array('Leave.user_id'=>$this->Session->read('User.id'),'Leave.date'=>date('Y-m-d'), 'Leave.approved !='=>2)));

      if(empty($leave_exists)){
      $this->loadModel('SubLeave');

      $insert_half_day_leave = array('Leave'=>array('user_id'=>$this->Session->read('User.id'),
      'request'=>'current',
      'date'=>date('Y-m-d'),
      'days'=>0.50,
      'reason'=>'Late Entry',
      'approved'=>1,
      'remarks'=>'Late Entry'));

      $this->Leave->save($insert_half_day_leave);
      $leave_id = $this->Leave->getLastInsertId();

      $leave_count = $this->requestAction('leave/user_get_all_leave_count_per_year/'.$this->Session->read('User.id').'/'.date('Y'));

      $insert_sub_leave = array();
      if($leave_count + 0.5 <= 12){
      $insert_sub_leave['SubLeave']['status'] = 'C';
      }
      else{
      $insert_sub_leave['SubLeave']['status'] = 'P';
      }

      $insert_sub_leave['SubLeave']['leave_id'] = $leave_id;
      $insert_sub_leave['SubLeave']['date'] = date('Y-m-d');
      $insert_sub_leave['SubLeave']['day'] = 0.5;

      $this->SubLeave->save($insert_sub_leave);

      $current_permission_count >= 2 ? $message = 'You already take '.$current_permission_count.' permissions in this month. <br>' : $message = '';

      $this->Session->write('LateEntry', 'You are <b>'.$interval->format('%h hours %i minutes %s second(s)').'</b> late. <br>'.$message.'So this is considered as <span style="color:red">Half a day Leave</span>');
      }
      }
      else{
      $from_time = date('Y-m-d').' 10:00:00';
      $to_time = date('Y-m-d').' 12:00:00';

      $insert_permission = array('Permission'=>array('user_id'=>$this->Session->read('User.id'),
      'request'=>'current',
      'date'=>date('Y-m-d'),
      'from_time'=>$from_time,
      'to_time'=>$to_time,
      'reason'=>'Late Entry',
      'approved'=>1,
      'permission_leave'=>1,
      'remarks'=>'Late Entry'));

      $this->Permission->save($insert_permission);

      $this->Session->write('LateEntry', 'You are <b>'.$interval->format('%h hours %i minutes %s second(s)').'</b> late. <br>So This is considered as <span style="color:red">2 Hours Permission</span>');
      }
      }
     */
    }

///////////////////////////////////////////////////////////////////////////////

    public function check_permission_exists($user_id, $date) {
        $permission_exists = $this->Permission->find('first', array('conditions' => array('Permission.date' => $date, 'Permission.user_id' => $user_id, 'Permission.approved !=' => 2)));

        $return = array();
        if (!empty($permission_exists)) {
            $return['exists'] = 1;

            $return['start_hours'] = date('g', strtotime($permission_exists['Permission']['from_time']));
            $return['start_minutes'] = date('i', strtotime($permission_exists['Permission']['from_time']));
            $return['start_meridian'] = date('a', strtotime($permission_exists['Permission']['from_time']));

            $return['end_hours'] = date('g', strtotime($permission_exists['Permission']['to_time']));
            $return['end_minutes'] = date('i', strtotime($permission_exists['Permission']['to_time']));
            $return['end_meridian'] = date('a', strtotime($permission_exists['Permission']['to_time']));

            $return['reason'] = $permission_exists['Permission']['reason'];
        } else {
            $return['exists'] = 0;
        }
        echo json_encode($return);
        exit;
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_add() {
          $this->layout = "admin-inner";
        $this->set('cpage', 'permission');
        $this->layout = "admin-inner";
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Permission']['date'] = date('Y-m-d', strtotime($this->data['Permission']['date']));
            $this->request->data['Permission']['user_id'] = $this->data['Permission']['user_id'];

//update from and to time
            if ($this->data['Permission']['from']['meridian'] == 'pm') {
                if ($this->data['Permission']['from']['hours'] != '12') {
                    $this->request->data['Permission']['from_time'] = date('Y-m-d') . ' ' . ($this->data['Permission']['from']['hours'] + 12) . ':' . $this->data['Permission']['from']['minutes'] . ':' . '00';
                } else {
                    $this->request->data['Permission']['from_time'] = date('Y-m-d') . ' ' . ($this->data['Permission']['from']['hours']) . ':' . $this->data['Permission']['from']['minutes'] . ':' . '00';
                }
            } else {
                $this->request->data['Permission']['from_time'] = date('Y-m-d') . ' ' . ($this->data['Permission']['from']['hours']) . ':' . $this->data['Permission']['from']['minutes'] . ':' . '00';
            }

            if ($this->data['Permission']['to']['meridian'] == 'pm') {
                if ($this->data['Permission']['to']['hours'] != '12') {
                    $this->request->data['Permission']['to_time'] = date('Y-m-d') . ' ' . ($this->data['Permission']['to']['hours'] + 12) . ':' . $this->data['Permission']['to']['minutes'] . ':' . '00';
                } else {
                    $this->request->data['Permission']['to_time'] = date('Y-m-d') . ' ' . ($this->data['Permission']['to']['hours']) . ':' . $this->data['Permission']['to']['minutes'] . ':' . '00';
                }
            } else {
                $this->request->data['Permission']['to_time'] = date('Y-m-d') . ' ' . ($this->data['Permission']['to']['hours']) . ':' . $this->data['Permission']['to']['minutes'] . ':' . '00';
            }

            $count = $this->Permission->find('count', array('conditions' => array('Permission.user_id' => $this->data['Permission']['user_id'], 'MONTH(Permission.date)' => date('m'), /* 'Permission.permission_leave'=>1, */ 'Permission.approved' => 1), 'order' => array('Permission.created DESC')));

            /*
              if($count <= 3){
              $datetime1 = new DateTime($this->data['Permission']['from_time']);
              $datetime2 = new DateTime($this->data['Permission']['to_time']);

              $interval = $datetime1->diff($datetime2);
              $minutes = ($interval->format('%h')*60)+($interval->format('%i'));

              if($minutes > 120){
              $this->request->data['Permission']['permission_leave'] = 2;
              }
              }
              else{
              $this->request->data['Permission']['permission_leave'] = 2;
              }
             */

            if ($this->Permission->save($this->request->data)) {
//				$this->permission_request_mail($this->Permission->getLastInsertId());
                $this->Session->setFlash('Permission Request Submitted Sucessfully', 'flash_success');
                return $this->redirect('/admin/permission/add');
            } else {
                $this->Session->setFlash('Failed to Send your Permission', 'flash_error');
            }
        } else {
            $this->set('users', $this->requestAction('users/get_all_users'));
        }
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_bulk_status_change() {
          $this->layout = "admin-inner";
        $ids = $this->data['permission_ids'];
        foreach ($ids as $id) {
            $update = array(
                'Permission' => array(
                    'id' => $id,
                    'approved' => $this->data['status'],
                    'remarks' => $this->data['remarks'],
                )
            );
            if ($this->Permission->saveAll($update)) {
                $this->permission_remarks($id);
            }
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
        exit;
    }

}

?>