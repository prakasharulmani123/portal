<?php

class PendingReportsController extends AppController {

    public $name = 'PendingReports';
    public $helpers = array('Html', 'Form', 'Js', 'Paginator', 'Time');
    public $components = array('Session', 'Cookie', 'Email');

/////////////////////////////////////////////////////////////////////////	

    public function beforeFilter() {
        $this->set('cpage', 'pendingreport');
        parent::beforefilter();
        $this->__validateLoginStatus();
    }

/////////////////////////////////////////////////////////////////////////	

    public function admin_index() {
        $this->layout = "admin-inner";
        $this->set('open2', 'active');
        if ($this->request->is('put') || $this->request->is('post')) {
            if ($this->StaticPage->save($this->request->data)) {
                echo $this->Session->setFlash('Saved Successfully', 'flash_success');
            } else {
                echo $this->Session->setFlash('Failed to Save', 'flash_error');
            }
            return $this->redirect('');
        } else {
            $pending_reports = $this->PendingReport->find('all', array('order' => array('PendingReport.date DESC')));
            $this->set(compact('pending_reports'));
        }
    }

/////////////////////////////////////////////////////////////////////////	

    public function admin_add() {
        $this->layout = "admin-inner";
        $this->set('open2', 'active');
        if ($this->request->is('put') || $this->request->is('post')) {

            if ($this->data['PendingReport']['start']['meridian'] == 'pm') {
                if ($this->data['PendingReport']['start']['hours'] != '12') {
                    $this->request->data['PendingReport']['start_time'] = date('Y-m-d', strtotime($this->data['PendingReport']['date'])) . ' ' . ($this->data['PendingReport']['start']['hours'] + 12) . ':' . $this->data['PendingReport']['start']['minutes'] . ':' . '00';
                } else {
                    $this->request->data['PendingReport']['start_time'] = date('Y-m-d', strtotime($this->data['PendingReport']['date'])) . ' ' . ($this->data['PendingReport']['start']['hours']) . ':' . $this->data['PendingReport']['start']['minutes'] . ':' . '00';
                }
            } else {
                $this->request->data['PendingReport']['start_time'] = date('Y-m-d', strtotime($this->data['PendingReport']['date'])) . ' ' . ($this->data['PendingReport']['start']['hours']) . ':' . $this->data['PendingReport']['start']['minutes'] . ':' . '00';
            }

            foreach ($this->data['PendingReport']['user'] as $user_id) {
                $insert_pending_report = array('PendingReport' => array('user_id' => $user_id,
                        'date' => date('Y-m-d', strtotime($this->data['PendingReport']['date'])),
                        'start_time' => $this->data['PendingReport']['start_time'],
                        'reason' => $this->data['PendingReport']['reason'],
                        'remarks' => $this->data['PendingReport']['remarks'],
                        'status' => 1));

                $this->PendingReport->saveAll($insert_pending_report);
            }
            return $this->redirect('/admin/pending_reports');
        }
    }

/////////////////////////////////////////////////////////////////////////	

    public function index() {
        $this->layout = 'user-inner';
        $pending_reports = $this->PendingReport->find('all', array('conditions' => array('PendingReport.user_id' => $this->Session->read('User.id')), 'order' => array('PendingReport.date DESC')));
        $this->set(compact('pending_reports'));
    }

/////////////////////////////////////////////////////////////////////////	

    public function send_request_on_timer() {
        if ($this->data['PendingReport']['start']['meridian'] == 'pm') {
            if ($this->data['PendingReport']['start']['hours'] != '12') {
                $this->request->data['PendingReport']['start_time'] = date('Y-m-d', strtotime($this->data['PendingReport']['date'])) . ' ' . ($this->data['PendingReport']['start']['hours'] + 12) . ':' . $this->data['PendingReport']['start']['minutes'] . ':' . '00';
            } else {
                $this->request->data['PendingReport']['start_time'] = date('Y-m-d', strtotime($this->data['PendingReport']['date'])) . ' ' . ($this->data['PendingReport']['start']['hours']) . ':' . $this->data['PendingReport']['start']['minutes'] . ':' . '00';
            }
        } else {
            $this->request->data['PendingReport']['start_time'] = date('Y-m-d', strtotime($this->data['PendingReport']['date'])) . ' ' . ($this->data['PendingReport']['start']['hours']) . ':' . $this->data['PendingReport']['start']['minutes'] . ':' . '00';
        }


        $excuse_time = strtotime(date('H:i', strtotime('10:30')));
        $get_in_time = strtotime(date('H:i', strtotime($this->data['PendingReport']['start_time'])));

        if ($get_in_time > $excuse_time) {
            $this->requestAction('late_entries/late_entry/' . date('H-i-s', strtotime($this->data['PendingReport']['start_time'])) . '/' . date('Y-m-d', strtotime($this->data['PendingReport']['start_time'])));
        }

        if ($this->PendingReport->save($this->data)) {

            $this->Session->setFlash('Your Request Sent to admin - please wait for approval', 'flash_success');
            return $this->redirect('/users/dashboard');
        }
    }

/////////////////////////////////////////////////////////////////////////

    public function check_user_pending_reports() {
        return $this->PendingReport->find('all', array('conditions' => array('PendingReport.user_id' => $this->Session->read('User.id'))));
    }

/////////////////////////////////////////////////////////////////////////

    public function check_user_pending_reports_active($user_id = NULL) {
        return $this->PendingReport->find('all', array('conditions' => array('PendingReport.user_id' => $user_id, 'PendingReport.status !=' => 2)));
    }

/////////////////////////////////////////////////////////////////////////

    public function check_user_pending_reports_approved($user_id = NULL) {
        return $this->PendingReport->find('all', array('conditions' => array('PendingReport.user_id' => $user_id, 'PendingReport.status' => 1)));
    }

/////////////////////////////////////////////////////////////////////////

    public function check_all_user_pending_reports_active() {
        return $this->PendingReport->find('all', array('conditions' => array('PendingReport.status !=' => 2)));
    }

/////////////////////////////////////////////////////////////////////////

    public function check_all_user_pending_reports_by_status($status = NULL) {
        return $this->PendingReport->find('all', array('conditions' => array('PendingReport.status' => $status)));
    }

/////////////////////////////////////////////////////////////////////////

    public function check_status_by_date($date = NULL) {
        return $this->PendingReport->find('first', array('conditions' => array('PendingReport.user_id' => $this->Session->read('User.id'), 'PendingReport.date' => $date)));
    }

/////////////////////////////////////////////////////////////////////////

    public function admin_add_remarks() {
        $this->layout = "admin-inner";
        $update = array(
            'PendingReport' => array(
                'id' => $this->data['id'],
                'status' => $this->data['status'],
                'remarks' => $this->data['remarks'],
            )
        );

        if ($this->PendingReport->saveAll($update)) {
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

/////////////////////////////////////////////////////////////////////////

    public function dailystatus($id = NULL) {

        $this->layout = "user-inner";

        $entry = $this->PendingReport->read(null, $id);

        if (empty($entry)) {
            $this->Session->setFlash('Entry not Exists', 'flash_error');
            return $this->redirect('/pending_reports');
        } else {
            if ($entry['PendingReport']['user_id'] != $this->Session->read('User.id')) {
                $this->Session->setFlash('This is not your pending report', 'flash_error');
                return $this->redirect('/pending_reports');
            } elseif ($entry['PendingReport']['start_time'] == '0000-00-00 00:00:00') {
                $this->Session->setFlash('Please send request to admin', 'flash_error');
                return $this->redirect('/pending_reports');
            }
        }
        $this->set('entry', $entry);

        $this->loadModel('TempReport');
        $reports = $this->TempReport->find('all', array(
            'conditions' => array(
                'TempReport.user_id' => $this->Session->read('User.id'),
                'TempReport.date' => date('Y-m-d', strtotime($entry['PendingReport']['date']))),
            'order' => array('DATE_FORMAT(TempReport.start_time,"%H:%i:%s") ASC')));

        $this->set('reports', $reports);
//			$this->set('times',$this->requestAction('dailystatus/hoursRange'));
        $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->Session->read('User.id')))));

        $this->loadModel('Meeting');
        $meetings = $this->Meeting->find('all', array('conditions' => array('Meeting.user_id' => $this->Session->read('User.id'), 'Meeting.meeting_date' => date('Y-m-d', strtotime($entry['PendingReport']['date'])), 'Meeting.status' => 0), 'order' => array('Meeting.meeting_date' => 'ASC'), 'group' => array('Meeting.meeting_date')));

        $this->set('meetings', $meetings);
        $this->set('id', $id);
    }

///////////////////////////////////////////////////////////////////////////////

    public function delete_row($id = NULL, $pending_id = NULL) {
        $this->loadModel('TempReport');

        $this->TempReport->delete($id);
        return $this->redirect('/pending_reports/dailystatus/' . $pending_id);
    }

///////////////////////////////////////////////////////////////////////////////

    public function edit_row($id = NULL, $pending_id = NULL) {
        $this->Session->write('PendingReportEdit', $id);
        return $this->redirect('/pending_reports/dailystatus/' . $pending_id);
    }

///////////////////////////////////////////////////////////////////////////////

    public function cancel($id = NULL) {
        $entry = $this->PendingReport->findById($id);

        $this->loadModel('TempReport');

        $temp_reports = $this->TempReport->findAllByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d', strtotime($entry['PendingReport']['date'])));

        if (!empty($temp_reports)) {
            foreach ($temp_reports as $temp_report):
                $this->TempReport->delete($temp_report['TempReport']['id']);
            endforeach;
        }
        return $this->redirect('/pending_reports/dailystatus/' . $id);
    }

///////////////////////////////////////////////////////////////////////////////

    public function add_daily_report($id = NULL) {
        $entry = $this->PendingReport->findById($id);

        if (!empty($entry)) {
            $this->loadModel('TempReport');
            $this->loadModel('DailyStatus');

            $temp_reports = $this->TempReport->findAllByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d', strtotime($entry['PendingReport']['date'])));

            $reports = array();

            foreach ($temp_reports as $key => $temp_report):
                $reports[$key]['DailyStatus'] = $temp_report['TempReport'];
                $reports[$key]['DailyStatus']['date'] = date('Y-m-d', strtotime($entry['PendingReport']['date']));
                unset($reports[$key]['DailyStatus']['id']);
            endforeach;

            if ($reports) {
                if ($this->DailyStatus->saveAll($reports)) {

                    foreach ($temp_reports as $temp_report):
                        $this->TempReport->delete($temp_report['TempReport']['id']);
                    endforeach;
                };

                $this->pending_report_mail($id);
                $this->update_pending_report($id);
            }
            else {
                $this->Session->setFlash('No records to send - Please add reports', 'flash_error');
                return $this->redirect('/pendingreport/' . $id);
            }
        } else {
            $this->Session->setFlash('No records to send', 'flash_error');
            return $this->redirect('../users/dashboard');
        }
        /*
          if($this->Session->check('PendingReport')){
          $pending_report = $this->PendingReport->read(null, $id);

          $this->loadModel('DailyStatus');
          foreach($this->Session->read('PendingReport') as $report){
          $this->request->data['DailyStatus']['date'] = $pending_report['PendingReport']['date'];
          $this->request->data['DailyStatus']['user_id'] = $this->Session->read('User.id');
          $this->request->data['DailyStatus']['projectname'] = $report['projectname'];
          $this->request->data['DailyStatus']['category_id'] = $report['category_id'];
          $this->request->data['DailyStatus']['work_id'] = $report['work_id'];
          $this->request->data['DailyStatus']['start_time'] = $report['start_time'];
          $this->request->data['DailyStatus']['end_time'] = $report['end_time'];
          $this->request->data['DailyStatus']['comments'] = $report['comments'];
          $this->request->data['DailyStatus']['status'] = $report['status'];
          $this->DailyStatus->saveAll($this->request->data);
          }

          $this->pending_report_mail($id);
          }
          else{
          $this->Session->setFlash('Session out','flash_error');
          return $this->redirect('/pendingreport/'.$id);
          }
         */
    }

///////////////////////////////////////////////////////////////////////////////

    public function pending_report_mail($id = NULL) {
        $all_to = $all_cc = $all_bcc = array();

        $add_to = $this->requestAction('emails/all_to_email');
        $add_cc = $this->requestAction('emails/all_cc_email');
        $add_bcc = $this->requestAction('emails/all_bcc_email');

        foreach ($add_to as $to) {
            $array = explode(',', $to['Email']['options']);

            foreach ($array as $key => $value) {
                if ($value == 1) {
                    $all_to[$to['Email']['id']] = $to['Email']['email'];
                }
            }
        }

        foreach ($add_cc as $cc) {
            $array = explode(',', $cc['Email']['options']);

            foreach ($array as $key => $value) {
                if ($value == 1) {
                    $all_cc[$cc['Email']['id']] = $cc['Email']['email'];
                }
            }
        }

        foreach ($add_bcc as $bcc) {
            $array = explode(',', $bcc['Email']['options']);

            foreach ($array as $key => $value) {
                if ($value == 1) {
                    $all_bcc[$bcc['Email']['id']] = $bcc['Email']['email'];
                }
            }
        }

        array_push($all_cc, $this->Session->read('User.email'));

        if (empty($all_to) && empty($all_cc) && empty($all_bcc)) {
            $this->Session->setFlash('Admin need to specify at least one destination for to, cc or bcc.<br>Contact Admin', 'flash_error');
        } else {
            $pending_report = $this->PendingReport->read(null, $id);

            $this->loadModel('DailyStatus');
            $reports = $this->DailyStatus->findAllByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d', strtotime($pending_report['PendingReport']['date'])), '', array('start_time ASC'));

            $this->Email->to = $all_to;
            $this->Email->cc = $all_cc;
            $this->Email->bcc = $all_bcc;
            $this->Email->subject = 'Pending Report : ' . ' ' . date('jS F Y', strtotime($pending_report['PendingReport']['date']));
            $this->Email->replyTo = $this->Session->read('User.email');
            $this->Email->from = $this->Session->read('User.email');
            $this->Email->template = 'pendingreport';
            $this->Email->sendAs = 'html';
            $this->set('pending_report', $pending_report);
            $this->set('reports', $reports);
            $this->Email->send();
        }
    }

    public function update_pending_report($id = NULL) {
        $this->loadModel('Entry');
        $this->loadModel('DailyStatus');

        $pending_report = $this->PendingReport->read(null, $id);

        $pending_reports = $this->DailyStatus->findAllByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d', strtotime($pending_report['PendingReport']['date'])), '', array('start_time ASC'));
        $end_array = end($pending_reports);
        $end_time = $end_array['DailyStatus']['end_time'];


        $add_entry = array('Entry' => array('user_id' => $this->Session->read('User.id'),
                'date' => $pending_report['PendingReport']['date'],
                'time_in' => $pending_report['PendingReport']['start_time'],
                'time_out' => $end_time,
                'on_off' => 0,
                'time_in_ip' => $this->request->clientIp(),
                'time_out_ip' => $this->request->clientIp()));

        $check_entry = $this->Entry->findByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d', strtotime($pending_report['PendingReport']['date'])));
        !empty($check_entry) ? $add_entry['Entry']['id'] = $check_entry['Entry']['id'] : '';

        $this->Entry->save($add_entry);
        $this->PendingReport->delete($id);
        $this->Session->delete('PendingReport');
        $this->send_mom($pending_report['PendingReport']['date']);
        $this->Session->setFlash('Your report has been sent suceesfully', 'flash_success');
        return $this->redirect('/');
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function send_mom($date) {
        $this->loadModel('Meeting');
        $meetings = $this->Meeting->findAllByUserIdAndMeetingDate($this->Session->read('User.id'), $date);

        if (!empty($meetings)) {
            require_once 'PhpWord/PhpWord/Autoloader.php';
            require_once 'PhpWord/PhpWord/PhpWord.php';
            require_once 'PhpWord/PhpWord/Settings.php';
            require_once 'PhpWord/PhpWord/Template.php';
            require_once 'PhpWord/PhpWord/DocumentProperties.php';
            require_once 'PhpWord/PhpWord/Collection/Titles.php';

            $all_to = $all_cc = $all_bcc = array();

            $add_to = $this->requestAction('emails/all_to_email');
            $add_cc = $this->requestAction('emails/all_cc_email');
            $add_bcc = $this->requestAction('emails/all_bcc_email');

            foreach ($add_to as $to) {
                $array = explode(',', $to['Email']['options']);

                foreach ($array as $key => $value) {
                    if ($value == 1) {
                        $all_to[$to['Email']['id']] = $to['Email']['email'];
                    }
                }
            }

            foreach ($add_cc as $cc) {
                $array = explode(',', $cc['Email']['options']);

                foreach ($array as $key => $value) {
                    if ($value == 1) {
                        $all_cc[$cc['Email']['id']] = $cc['Email']['email'];
                    }
                }
            }

            foreach ($add_bcc as $bcc) {
                $array = explode(',', $bcc['Email']['options']);

                foreach ($array as $key => $value) {
                    if ($value == 1) {
                        $all_bcc[$bcc['Email']['id']] = $bcc['Email']['email'];
                    }
                }
            }

            array_push($all_cc, $this->Session->read('User.email'));

            foreach ($meetings as $meeting) {
                Autoloader::register();
                $phpWord = new PhpWord();
                $document = $phpWord->loadTemplate('PhpWord/resources/MOM.docx');
                $name = date('Y-m-d', strtotime($date)) . '-' . $meeting['Project']['projectname'] . '-' . $this->Session->read('User.id') . '.doc';

                // Variables on different parts of document
                $document->setValue('weekday', date('F d, Y', strtotime($date))); // On header
                $document->setValue('time', date('H:i:s', strtotime($date))); // On footer
                // On content
                $document->setValue('m_date', date('M d, Y', strtotime($meeting['Meeting']['meeting_location'])));
                $document->setValue('m_loc', $meeting['Meeting']['meeting_location']);
                $document->setValue('user', $this->Session->read('User.name'));
                $document->setValue('building', $meeting['Meeting']['building']);
                $document->setValue('web_address', $meeting['Meeting']['web_address']);
                $document->setValue('sche_start', date('h:i a', strtotime($meeting['Meeting']['meeting_schedule_start'])));
                $document->setValue('sche_actu_start', date('h:i a', strtotime($meeting['Meeting']['meeting_actual_start'])));
                $document->setValue('m_scribe', $meeting['Meeting']['meeting_scribe']);
                $document->setValue('agenda', 'agenda');
                $document->setValue('m_sche_end', date('h:i a', strtotime($meeting['Meeting']['meeting_schedule_end'])));
                $document->setValue('m_actu_end', date('h:i a', strtotime($meeting['Meeting']['meeting_actual_end'])));
                $document->setValue('action', $meeting['Meeting']['action']);
                $document->setValue('next_meet', $meeting['Meeting']['next_meeting']);

                $document->saveAs($name);
                rename($name, "PhpWord/results/{$name}");

                $user = $this->requestAction('users/get_user/' . $this->Session->read('User.id'));

                $this->Email->to = $all_to;
                $this->Email->cc = $all_cc;
                $this->Email->bcc = $all_bcc;
                $this->Email->subject = 'MOM : ' . ucfirst($meeting['Project']['projectname']) . ' ' . date('jS F Y', strtotime($date));
                $this->Email->replyTo = $this->Session->read('User.email');
                $this->Email->from = $this->Session->read('User.email');
                $this->Email->template = 'mom';
                $this->Email->sendAs = 'html';
                $this->Email->attachments = array("PhpWord/results/" . $name);
                $this->set('user', $user);
                $this->set('meeting', $meeting);
                $this->Email->send();

                $this->Meeting->id = $meeting['Meeting']['id'];
                $this->Meeting->saveField('status', 1);

                unlink("PhpWord/results/" . $name);
            };
        }
    }

    /*
      public function check_belated_pending_reports(){
      $check_date = date('Y-m-d', strtotime ( '-6 day' , strtotime ( date('Y-m-d')) )) ;

      $pending_reports = $this->PendingReport->find('first', array('conditions'=>array('PendingReport.user_id'=>$this->Session->read('User.id'),'PendingReport.date'=>$check_date, 'PendingReport.status'=>1)));

      if(!empty($pending_reports)){
      $this->loadModel('DailyStatus');

      $check_report = $this->DailyStatus->find('all', array('conditions'=>array('DailyStatus.user_id'=>$this->Session->read('User.id'),'DailyStatus.date'=>$check_date)));

      if(empty($check_report)){

      $this->loadModel('Leave');
      $this->loadModel('SubLeave');

      $insert_leave = array('Leave'=>array('user_id'=>$this->Session->read('User.id'),
      'request'=>'past',
      'date'=>$check_date,
      'days'=>1,
      'reason'=>'Pending Report Delayed',
      'approved'=>1,
      'remarks'=>'Pending Report Delayed'));

      $this->Leave->save($insert_leave);
      $leave_id = $this->Leave->getLastInsertId();

      $leave_count = $this->requestAction('leave/user_get_all_leave_count_per_year/'.$this->Session->read('User.id').'/'.date('Y'));

      $insert_sub_leave = array();
      if($leave_count + 1 <= 12){
      $insert_sub_leave['SubLeave']['status'] = 'C';
      }
      else{
      $insert_sub_leave['SubLeave']['status'] = 'P';
      }

      $insert_sub_leave['SubLeave']['leave_id'] = $leave_id;
      $insert_sub_leave['SubLeave']['date'] = $check_date;
      $insert_sub_leave['SubLeave']['day'] = 1;

      $this->SubLeave->save($insert_sub_leave);
      $this->PendingReport->delete($pending_reports['PendingReport']['id']);
      }
      }
      }
     */

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add_reports() {
        $this->layout = '';

//		if($this->request->is('post') || $this->request->is('put')){
        $this->loadModel('TempReport');
        $insert = array();

        $start_time = $this->get_date($this->data['start_hours'], $this->data['start_minutes'], $this->data['start_meridian']);
        $end_time = $this->get_date($this->data['end_hours'], $this->data['end_minutes'], $this->data['end_meridian']);

        $insert['TempReport']['id'] = $this->data['id'];
        $insert['TempReport']['user_id'] = $this->data['user_id'];
        $insert['TempReport']['date'] = $this->data['date'];
        $insert['TempReport']['category_id'] = $this->data['category_id'];
        $insert['TempReport']['projectname'] = $this->data['projectname'];
        $insert['TempReport']['work_id'] = $this->data['work_id'];
        $insert['TempReport']['status'] = $this->data['status'];
        $insert['TempReport']['comments'] = $this->data['comments'];

        $insert['TempReport']['start_time'] = $start_time;
        $insert['TempReport']['end_time'] = $end_time;

        $this->TempReport->save($insert);

        if (isset($this->data['id']) || $this->data['id'] != '') {
            $this->Session->delete('PendingReportEdit');
        }
//		}

        $entry = $this->PendingReport->findById($this->data['pending_id']);

        $reports = $this->TempReport->find('all', array(
            'conditions' => array(
                'TempReport.user_id' => $this->Session->read('User.id'),
                'TempReport.date' => date('Y-m-d', strtotime($entry['PendingReport']['date']))),
            'order' => array('DATE_FORMAT(TempReport.start_time,"%H:%i:%s") ASC')));

        $this->set(compact('reports', 'entry'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function delete_reports() {
        $this->layout = '';

        $this->loadModel('TempReport');
        $this->TempReport->delete($this->data['id']);

        $entry = $this->PendingReport->findById($this->data['pending_id']);

        $reports = $this->TempReport->find('all', array('conditions' => array('TempReport.user_id' => $this->Session->read('User.id'), 'TempReport.date' => date('Y-m-d', strtotime($entry['PendingReport']['date']))), 'order' => array('TempReport.start_time ASC')));

        $this->set(compact('reports', 'entry'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_date($hours, $minutes, $meridian) {
        $date_time = '';

        if ($meridian == 'pm') {
            if ($hours != '12') {
                $date_time = date('Y-m-d') . ' ' . ($hours + 12) . ':' . $minutes . ':' . '00';
            } else {
                $date_time = date('Y-m-d') . ' ' . ($hours) . ':' . $minutes . ':' . '00';
            }
        } else {
            $date_time = date('Y-m-d') . ' ' . ($hours) . ':' . $minutes . ':' . '00';
        }

        return $date_time;
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function delete($pending_id = NULL) {
        $this->PendingReport->id = $pending_id;

        if ($this->PendingReport->exists()) {
            if ($this->PendingReport->delete()) {
                $this->Session->setFlash('Report Deleted', 'flash_success');
            } else {
                $this->Session->setFlash('Report Deleted', 'flash_error');
            }
        } else {
            $this->Session->setFlash('Report not exists', 'flash_error');
        }

        return $this->redirect('index');
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_delete($pending_id = NULL) {
        $this->layout = "admin-inner";
        $this->PendingReport->id = $pending_id;

        if ($this->PendingReport->exists()) {
            if ($this->PendingReport->delete()) {
                $this->Session->setFlash('Report Deleted', 'flash_success');
            } else {
                $this->Session->setFlash('Report Deleted', 'flash_error');
            }
        } else {
            $this->Session->setFlash('Report not exists', 'flash_error');
        }

        return $this->redirect('index');
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_multi_delete($pending_ids = NULL) {
        $this->layout = "admin-inner";
        if ($pending_ids != '') {
            $delete_ids = explode(',', $pending_ids);

            foreach ($delete_ids as $delete_id) {
                $this->PendingReport->id = $delete_id;
                if ($this->PendingReport->exists()) {
                    $this->PendingReport->delete();
                }
            }
        }

        return $this->redirect('index');
    }

}

?>