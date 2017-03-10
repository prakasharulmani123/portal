<?php

class DailystatusController extends AppController {

    public $name = 'DailyStatus';
    public $helpers = array('Html', 'Form', 'Paginator', 'Time', 'Js' => array('Jquery'), 'Paginator');
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Paginator');

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function beforeFilter() {
        parent::beforefilter();
        $this->__validateLoginStatus();
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function index() {

// public function getWeek($date){
//$date1 = date('Y-m-d H:i:a',strtotime($report['TempReport']['date']));
        $this->layout = "user-inner";
        $this->set('cpage', 'dailystatus');

        $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->Session->read('User.id')))));

        $this->loadModel('TempReport');
        $reports = $this->TempReport->find('all', array('conditions' => array('TempReport.user_id' => $this->Session->read('User.id'), 'TempReport.date' => date('Y-m-d')), 'order' => array('TempReport.start_time ASC')));

        $check_time = $this->requestAction('entries/check_time_in_out');
        if (!empty($check_time)) {
            if ($check_time['Entry']['on_off'] == 0) {
                $check_on_off = $check_time['Entry']['on_off'];
                $entry_id = 0;
            } else {
                $check_on_off = $check_time['Entry']['on_off'];
                $entry_id = $check_time['Entry']['id'];
            }
        } else {
            $check = 0;
            $entry_id = 0;
        }

        $this->loadModel('Meeting');
        $meetings = $this->Meeting->find('all', array('conditions' => array('Meeting.user_id' => $this->Session->read('User.id'), 'Meeting.meeting_date' => date('Y-m-d'), 'Meeting.status' => 0), 'order' => array('Meeting.meeting_date' => 'ASC'), 'group' => array('Meeting.meeting_date')));

        $this->loadModel('LateEntry');
        $late_entry = $this->LateEntry->find('first', array(
            'conditions' => array(
                'DATE(LateEntry.date)' => date('Y-m-d'),
                'LateEntry.user_id' => $this->Session->read('User.id'),
            )
        ));
        $office_times = $this->requestAction('entries/office_times');

        $this->loadModel('Permission');
        $permission_exists = $this->Permission->find('first', array('conditions' => array('Permission.date' => date('Y-m-d'), 'Permission.user_id' => $this->Session->read('User.id'), 'Permission.approved !=' => 2)));

        $report_send = '';
        if ($this->Session->check('report_send')) {
            $report_send = $this->Session->read('report_send');
            $this->Session->delete('report_send');
        }

        $this->loadModel('Leave');
        $leave = $this->Leave->find('all', array(
            'conditions' => array(
                'Leave.user_id' => $this->Session->read('User.id'),
                'Leave.days' => '0.50',
                'Leave.date' => date('Y-m-d')
        )));

        $this->set(compact('reports', 'entry_id', 'meetings', 'report_send', 'late_entry', 'office_times', 'permission_exists', 'leave'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_index() {
        $this->set('cpage', 'dailyreports');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('DailyStatus.user_id', '');
            $this->Session->write('DailyStatus.from_date', '');
            $this->Session->write('DailyStatus.to_date', '');

            if ($this->request->data['DailyStatus']['user_id'] != '') {
                $this->Session->write('DailyStatus.user_id', $this->request->data['DailyStatus']['user_id']);
            }

            if (!empty($this->request->data['DailyStatus']['from_date']) && !empty($this->request->data['DailyStatus']['to_date'])) {
                $this->Session->write('DailyStatus.from_date', date('Y-m-d', strtotime($this->request->data['DailyStatus']['from_date'])));
                $this->Session->write('DailyStatus.to_date', date('Y-m-d', strtotime($this->request->data['DailyStatus']['to_date'])));
            }

            return $this->redirect(array('action' => 'admin_index'));
        }

        if ($this->Session->check('DailyStatus')) {
            $all = $this->Session->read('DailyStatus');

            if ($all['user_id'] == '' && $all['from_date'] == '') {
                $dailyreports = array('order' => array('DailyStatus.date' => 'DESC'), 'group' => array('DailyStatus.date,DailyStatus.user_id'));
            } elseif ($all['user_id'] != '' && $all['from_date'] == '') {
                $dailyreports = array('conditions' => array('DailyStatus.user_id' => $all['user_id']), 'order' => array('DailyStatus.date' => 'DESC'), 'group' => array('DailyStatus.date,DailyStatus.user_id'));
            } elseif ($all['user_id'] == '' && $all['from_date'] != '') {
                $dailyreports = array('conditions' => array('DailyStatus.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('DailyStatus.date' => 'DESC'), 'group' => array('DailyStatus.date,DailyStatus.user_id'));
            } elseif ($all['user_id'] != '' && $all['from_date'] != '') {
                $dailyreports = array('conditions' => array('DailyStatus.user_id' => $all['user_id'], 'DailyStatus.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('DailyStatus.date' => 'DESC'), 'group' => array('DailyStatus.date,DailyStatus.user_id'));
            }
        } else {
            $all = array('user_id' => '', 'from_date' => '', 'to_date' => '');
            $dailyreports = array('order' => array('DailyStatus.date' => 'DESC'), 'group' => array('DailyStatus.date', 'DailyStatus.user_id'));
        }
        $dailyreports = array_merge($dailyreports, array('limit' => 25));
        $this->Paginator->settings = $dailyreports;
        $dailyreports = $this->Paginator->paginate('DailyStatus');

        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('dailyreports'));
        if ($this->request->is('ajax')) {
            $this->render('admin_index', 'ajaxpagination'); // View, Layout
        }
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function daily_status_mail() {
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
            return $this->redirect('/dailystatus');
        }

        $reports = $this->DailyStatus->findAllByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d'), '', array('start_time ASC'));

        $this->loadModel('LateEntry');
        $late_entry = $this->LateEntry->find('first', array(
            'conditions' => array(
                'DATE(LateEntry.date)' => date('Y-m-d'),
                'LateEntry.user_id' => $this->Session->read('User.id'),
            )
        ));

        $this->Email->to = $all_to;
        $this->Email->cc = $all_cc;
        $this->Email->bcc = $all_bcc;
        $this->Email->subject = 'Daily Status Report : ' . ' ' . date('jS F Y');
        $this->Email->replyTo = $this->Session->read('User.email');
        $this->Email->from = $this->Session->read('User.email');
        $this->Email->template = 'dailystatusreport';
        $this->Email->sendAs = 'html';
        $this->set('reports', $reports);
        $this->set('late_entry', $late_entry);
        $this->Email->send();
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function hoursRange($lower = 0, $upper = 24, $step = .1, $format = NULL) {
        if ($format === NULL) {
            $format = 'g:ia'; // 9:30pm
        }
        $times = array();
        foreach (range($lower, $upper, $step) as $increment) {
            $increment = number_format($increment, 2);
            $n = $increment;
            $whole = floor($n);      // 1
            $fraction = $n - $whole; // .25

            if ($fraction <= .50 && $whole != 24) {
                $times[(string) $increment] = date('g:i A', strtotime(strval(str_replace('.', ':', $increment))));
            }
        }
        return $times;
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_reset() {
        $this->Session->delete('DailyStatus');
        return $this->redirect(array('action' => 'admin_index'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function report_reset() {
        $this->Session->delete('Report');
        return $this->redirect('/dailystatus/reports');
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_view($id = NULL) {
        $this->set('cpage', 'dailystatus');

        $dailyreport = $this->DailyStatus->find('first', array('conditions' => array('DailyStatus.id' => $id)));
        $user_id = $dailyreport['DailyStatus']['user_id'];
        $date = $dailyreport['DailyStatus']['date'];

        $this->set('user_id', $user_id);
        $this->set('date', $date);
        $this->set('times', $this->hoursRange());
        $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->Session->read('User.id')))));
        $this->set('reports', $this->DailyStatus->find('all', array('conditions' => array('DailyStatus.user_id' => $user_id, 'DailyStatus.date' => $date), 'order' => array('DailyStatus.start_time ASC'))));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_get_all_reports() {
        return $this->DailyStatus->find('all', array('order' => array('DailyStatus.created' => 'DESC')));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_get_recent_reports() {
        return $this->DailyStatus->find('all', array('conditions' => array('DailyStatus.date between ? and ?' => array(date('Y-m-d', strtotime("-1 days")), date('Y-m-d'))), 'group' => array('DailyStatus.date,DailyStatus.user_id'), 'order' => array('DailyStatus.created' => 'DESC')));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function user_get_recent_reports() {
        return $this->DailyStatus->find('all', array('conditions' => array('DailyStatus.user_id' => $this->Session->read('User.id'), 'DailyStatus.date between ? and ?' => array(date('Y-m-d', strtotime("-7 days")), date('Y-m-d'))), 'group' => 'DailyStatus.date', 'order' => array('DailyStatus.created' => 'DESC')));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function delete_row($id = NULL) {
        $this->loadModel('TempReport');

        $this->TempReport->delete($id);
        return $this->redirect('/dailystatus');
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function add_daily_report($id = NULL) {
        $this->loadModel('Project');
        $this->loadModel('Compensation');
        $this->loadModel('TempReport');
        $this->loadModel('PendingReport');
        $temp_reports = $this->TempReport->findAllByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d'));
        $pending_report = $this->PendingReport->findByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d'));
        $reports = array();
        foreach ($temp_reports as $key => $temp_report):
            $reports[$key]['DailyStatus'] = $temp_report['TempReport'];
            unset($reports[$key]['DailyStatus']['id']);
        endforeach;
        if ($reports) {

            ///////Adding Projects Name:
            $records = array();
            foreach ($reports as $key=> $report) {
                $name = $report['DailyStatus']['projectname'];
                if ($name != NULL) {
                   $project_name = $report['DailyStatus']['projectname'];
                    $results = $this->Project->find('count', array('conditions' => array('Project.projectname' => $project_name)));
                    if ($results == 0) {
                        $data = array(
                            'Project' => array(
                                'projectname' => $project_name,
                            )
                        );
                        $this->Project->saveAll($data);
                    }
                    $get_pro = $this->Project->find('first', array('fields' => array('Project.id'), 'conditions' => array('Project.projectname' => $project_name)));
                    $pr_id = $get_pro['Project']['id'];
                    $reports[$key]['DailyStatus']['project_id'] = $pr_id;
                }
                // pr($report['DailyStatus']['project_id']); pr($get_pro);exit;
            }
//       $uname = $report['DailyStatus']['projectname'];
//    $get_pro = $this->Project->find('first', array('fields' => array('Project.id'), 'conditions' => array('Project.projectname' => $uname)));
//         $pr_id = $get_pro['Project']['id'];
//          $report['DailyStatus']['project_id'] = $pr_id;
            // pr($uname);pr($pr_id);exit;
            if ($this->DailyStatus->saveAll($reports)) {
                $worked_hours = 0;
                foreach ($reports as $key => $report) {
                    $datetime1 = new DateTime($report['DailyStatus']['start_time']);
                    $datetime2 = new DateTime($report['DailyStatus']['end_time']);
                    $interval = $datetime1->diff($datetime2);
                    if ($report['DailyStatus']['category_id'] != 23 && $report['DailyStatus']['category_id'] != 22 && $report['DailyStatus']['category_id'] != 24) {
                        $worked_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
                    }
                }
                $workhours = gmdate("H:i", ($worked_hours * 60)) . '<br>';
                $permission_time = '04:00';
                $min_time = '02:00';
                if ($workhours >= $min_time) {
                    $date_time = $reports[0]['DailyStatus']['date'];
                    $day = date('D', strtotime($date_time));
                    $this->loadModel('Holiday');
                    $officialleave = $this->Holiday->find('count', array('conditions' => array('Holiday.date=' . $date_time)));
                    if ($day == 'Wed' || $officialleave) {
                        $this->Compensation->create();
                        $user_id = $this->Session->read('User.id');
                        $max_time = '06:00';
                        $greeting = ($day == 'Sun') ? 'working on sunday' : 'working on ' . $date_time;
                        $calculateday = ($workhours >= $max_time || $workhours <= $permission_time ) ? '1' : '0.5';
                        $type = ($workhours <= $permission_time) ? 'P' : 'L';
                        $data = array(
                            'Compensation' => array(
                                'user_id' => $user_id,
                                'date' => $date_time,
                                'days' => $calculateday,
                                'comments' => $greeting,
                                'status' => '0',
                                'type' => $type,
                            )
                        );

                        $this->Compensation->save($data);
                        $this->loadModel('User');
                        $this->User->id = $user_id;
                        $this->User->updateAll(array(
                            'User.compensation_leave' => 'User.compensation_leave + 1'), array('User.id' => $user_id));

                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash('');
                        $this->redirect(array('action' => 'index'));
                    }
                } else {
                    $this->Session->setFlash('');
                    $this->redirect(array('action' => 'index'));
                }
                foreach ($temp_reports as $temp_report):
                    $this->TempReport->delete($temp_report['TempReport']['id']);
                endforeach;
                if (!empty($pending_report)) {
                    $this->PendingReport->delete($pending_report['PendingReport']['id']);
                }
            };
            $this->daily_status_mail();
            $this->send_mom();
            $this->Session->write('report_send', 1);
            $this->Session->setFlash('Your report has been sent suceesfully', 'flash_success');
            return $this->redirect('/dailystatus');
        } else {
            $this->Session->setFlash('No records to send - Please add reports', 'flash_error');
            return $this->redirect('/dailystatus');
        }
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function send_mom() {
        $this->loadModel('Meeting');
        $meetings = $this->Meeting->findAllByUserIdAndMeetingDate($this->Session->read('User.id'), date('Y-m-d'));

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
                $name = date('Y-m-d') . '-' . $meeting['Project']['projectname'] . '-' . $this->Session->read('User.id') . '.doc';

// Variables on different parts of document
                $document->setValue('weekday', date('F d, Y')); // On header
                $document->setValue('time', date('H:i:s')); // On footer
// On content
                $document->setValue('m_date', date('M d, Y', strtotime($meeting['Meeting']['meeting_date'])));
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

//				$name = 'Sample_07_TemplateCloneRow.doc';
                $document->saveAs($name);
                rename($name, "PhpWord/results/{$name}");

                $user = $this->requestAction('users/get_user/' . $this->Session->read('User.id'));

                $this->Email->to = $all_to;
                $this->Email->cc = $all_cc;
                $this->Email->bcc = $all_bcc;
                $this->Email->subject = 'MOM : ' . ucfirst($meeting['Project']['projectname']) . ' ' . date('jS F Y');
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

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function cancel() {
        $this->loadModel('TempReport');

        $temp_reports = $this->TempReport->findAllByUserIdAndDate($this->Session->read('User.id'), date('Y-m-d'));

        if (!empty($temp_reports)) {
            foreach ($temp_reports as $temp_report):
                $this->TempReport->delete($temp_report['TempReport']['id']);
            endforeach;
        }

        return $this->redirect('/dailystatus');
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function reports() {
        $this->layout = "user-inner";

        $this->set('cpage', 'myreport');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('Report.from_date', '');
            $this->Session->write('Report.to_date', '');

            if (!empty($this->request->data['DailyStatus']['from_date']) && !empty($this->request->data['DailyStatus']['to_date'])) {
                $this->Session->write('Report.from_date', date('Y-m-d', strtotime($this->request->data['DailyStatus']['from_date'])));
                $this->Session->write('Report.to_date', date('Y-m-d', strtotime($this->request->data['DailyStatus']['to_date'])));
            }

            return $this->redirect(array('action' => 'reports'));
        }

        if ($this->Session->check('Report')) {
            $all = $this->Session->read('Report');

            if ($all['from_date'] != '') {
                $dailyreports = array('conditions' => array('DailyStatus.user_id' => $this->Session->read('User.id'), 'DailyStatus.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('DailyStatus.date' => 'DESC'), 'group' => array('DailyStatus.date'));
            } else {
                $dailyreports = array('conditions' => array('DailyStatus.user_id' => $this->Session->read('User.id')), 'order' => array('DailyStatus.date' => 'DESC'), 'group' => array('DailyStatus.date'));
            }
        } else {
            $all = array('from_date' => '', 'to_date' => '');
            $dailyreports = array('conditions' => array('DailyStatus.user_id' => $this->Session->read('User.id')), 'order' => array('DailyStatus.date' => 'DESC'), 'group' => array('DailyStatus.date'));
        }
        $dailyreports = array_merge($dailyreports, array('limit' => 25));
        $this->Paginator->settings = $dailyreports;
        $dailyreports = $this->Paginator->paginate('DailyStatus');
        $this->set(compact('dailyreports', 'all'));
        if ($this->request->is('ajax')) {
            $this->render('reports', 'ajaxpagination'); // View, Layout
        }
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function reports_view($id = NULL) {
        $this->set('cpage', 'myreport');
        $this->layout = "user-inner";

        $dailyreport = $this->DailyStatus->find('first', array('conditions' => array('DailyStatus.id' => $id)));
        $date = $dailyreport['DailyStatus']['date'];

        $this->set('user_id', $this->Session->read('User.id'));
        $this->set('date', $date);
        $this->set('times', $this->hoursRange());
        $this->set('users', $this->requestAction('users/get_user', array('pass' => array('User.id' => $this->Session->read('User.id')))));
        $this->set('reports', $this->DailyStatus->find('all', array('conditions' => array('DailyStatus.user_id' => $this->Session->read('User.id'), 'DailyStatus.date' => $date), 'order' => array('DailyStatus.start_time ASC'))));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_reports_by_id_and_date($user_id = NULL, $date = NULL) {
        return $this->DailyStatus->find('all', array('conditions' => array('DailyStatus.user_id' => $user_id, 'DailyStatus.date' => $date)));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function edit_row($id = NULL) {
        $this->Session->write('DailyReportEdit', $id);
        return $this->redirect('/dailystatus');
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_monthly_report() {
        $this->set('cpage', 'month_report');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('DailyStatusMonth.user_id', '');
            $this->Session->write('DailyStatusMonth.month', '');
            $this->Session->write('DailyStatusMonth.year', '');

            if ($this->request->data['DailyStatus']['user_id'] != '') {
                $this->Session->write('DailyStatusMonth.user_id', $this->request->data['DailyStatus']['user_id']);
            }

            if (!empty($this->request->data['DailyStatus']['month'])) {
                $this->Session->write('DailyStatusMonth.month', date('m', strtotime($this->request->data['DailyStatus']['month'])));
                $this->Session->write('DailyStatusMonth.year', date('Y', strtotime($this->request->data['DailyStatus']['month'])));
            }

            return $this->redirect(array('action' => 'admin_monthly_report'));
        }

        if ($this->Session->check('DailyStatusMonth')) {
            $all = $this->Session->read('DailyStatusMonth');

            if ($all['user_id'] != '' && $all['month'] != '' && $all['year'] != '') {
                $dailyreports = $this->dates_month($all['month'], $all['year']);
            }
//			$dailyreports = $this->DailyStatus->find('all', array('conditions'=>array($user_query != '' ? $user_query : '', $month_query != '' ? $month_query : '', $year_query != '' ? $year_query : ''), 'order'=>array('DailyStatus.date ASC'), 'group'=>array('DailyStatus.date,DailyStatus.user_id')));
        } else {
            $all = array('user_id' => '', 'month' => '', 'year' => '');
//			$dailyreports = $this->DailyStatus->find('all', array('order'=>array('DailyStatus.date ASC'), 'group'=>array('DailyStatus.date','DailyStatus.user_id')));
        }

        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('dailyreports'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_reset_month() {
        $this->Session->delete('DailyStatusMonth');
        return $this->redirect(array('action' => 'admin_monthly_report'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function dates_month($month, $year) {
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $dates_month = array();
        for ($i = 1; $i <= $num; $i++) {
            $mktime = mktime(0, 0, 0, $month, $i, $year);
            $date = date("d-M-Y", $mktime);
            $date = date('Y-m-d', strtotime($date));
            $dates_month[$i] = $date;
        }
        return $dates_month;
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_export_to_csv() {
        $all = $this->Session->read('DailyStatusMonth');
        $dailyreports = $this->dates_month($all['month'], $all['year']);
        $employee = $this->requestAction('users/get_user', array('pass' => array($all['user_id'])));

        $filename = $employee['User']['employee_name'] . '-' . date('F-Y', strtotime('01-' . $all['month'] . '-' . $all['year'])) . ".csv";

        $f = fopen('php://memory', 'w');
        fputcsv($f, array('S.No', 'Name', 'Date', 'Day', 'Projects', 'Worked Hours'));

        $leaves_month = $this->requestAction('leave/get_current_month_leave_approved/' . $all['user_id'] . '/' . $all['month'] . '/' . $all['year']);
        $holidays_month = $this->requestAction('holidays/get_holidays_per_month/' . $all['month'] . '/' . $all['year']);

        $leaves = $leaves_month;

        $check_sun_day = false;
        /*
          foreach($leaves_month as $key=>$value){
          if($key > 1){
          for($x = 1; $x <=(round($key)-1); $x++){
          date('D',strtotime('+'.$x.' day', strtotime($value))) == 'Sun' ? $check_sun_day = true : '';

          $check_sun_day == true ? array_push($leaves, date('Y-m-d',strtotime('+'.($x+1).' day', strtotime($value)))) : array_push($leaves, date('Y-m-d',strtotime('+'.$x.' day', strtotime($value))));
          }
          }
          }
         */
        $holidays = array();
        foreach ($holidays_month as $holiday_month) {
            $holidays[$holiday_month['Holiday']['name'] . ',' . $holiday_month['Holiday']['date']] = $holiday_month['Holiday']['date'];
        }

        $permissions = $this->requestAction('permission/get_permission_approved_per_month/' . $all['user_id'] . '/' . $all['month'] . '/' . $all['year']);

        $row = 1;

        $half_day = '';
        foreach ($dailyreports as $dailyreport):
            $color = '';
            $worked_hours = 0;
            $projects = "";
            $hours = 0;
            $break_hours = 0;

            $check_sunday = false;
            $check_leave = false;
            $check_holiday = false;
            $check_working_day = false;
            $check_permission = false;

            date('D', strtotime($dailyreport)) == 'Sun' ? $check_sunday = true : $check_sunday = false;

            if (!empty($leaves)) {
                if (array_key_exists(date('Y-m-d', strtotime($dailyreport)), $leaves)) {
                    $days = array_search(date('Y-m-d', strtotime($dailyreport)), $leaves);
                    $check_leave = true;
                }
            }

            if (!empty($holidays)) {
                if (in_array(date('Y-m-d', strtotime($dailyreport)), $holidays)) {
                    $holiday_name = str_replace(',' . $dailyreport, ' ', array_search(date('Y-m-d', strtotime($dailyreport)), $holidays));
                    $check_holiday = true;
                }
            }

            if (!empty($permissions)) {
                if (in_array(date('Y-m-d', strtotime($dailyreport)), $permissions)) {
                    $check_permission = true;
                }
            }

            $reports = $this->requestAction('daily_status/get_reports_by_id_and_date/' . $all['user_id'] . '/' . $dailyreport);
            !empty($reports) ? $check_working_day = true : $check_working_day = false;

            foreach ($reports as $report):

                $start_time = strtotime($report['DailyStatus']['start_time']);
                $end_time = strtotime($report['DailyStatus']['end_time']);

                $datetime1 = new DateTime($report['DailyStatus']['start_time']);
                $datetime2 = new DateTime($report['DailyStatus']['end_time']);
                $interval = $datetime1->diff($datetime2);
                $elapsed = $interval->format('%h hour %i minute');
                $hours += ($interval->format('%h') * 60) + ($interval->format('%i'));

                if ($report['DailyStatus']['projectname']) {
                    $projects .= $report['DailyStatus']['projectname'] . ',';
                }

                if ($report['DailyStatus']['category_id'] != 23 && $report['DailyStatus']['category_id'] != 22) {
                    $worked_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
                } else {
                    $break_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
                }

                if ($report['DailyStatus']['projectname']) {
                    $projects .= $report['DailyStatus']['projectname'] . ' , ';
                }
            endforeach;

            if ($check_working_day == true) {
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), $projects, gmdate("H:i", ($worked_hours * 60))));
                $check_leave == true ? $check_leave = false : '';
            }

            if ($check_sunday == true) {
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), 'Sunday', 'Sunday'));
            }

            if ($check_holiday == true) {
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), $holiday_name, $holiday_name));
            }

            if ($check_leave == true) {
                $leave_row = $this->requestAction('leave/get_leave_by_userid_date/' . $all['user_id'] . '/' . $dailyreport);
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), $leave_row['Leave']['reason'], 'Leave'));
            }

            if ($check_working_day == false && $check_sunday == false && $check_holiday == false && $check_leave == false) {
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), '--', '--'));
            }
        endforeach;

        $late_entries = $this->requestAction('late_entries/get_late_entry_by_user_id/' . $employee['User']['id']);
        $late_fee = 0;

        if (!empty($late_entries)) {
            fputcsv($f, array(''));
            fputcsv($f, array('', '', 'Late Entry'));
            fputcsv($f, array('S.No', 'date', 'Late Details', 'Amount'));

            $i = 1;
            foreach ($late_entries as $late_entry) {
                $start_time = date('d-m-Y', strtotime($late_entry['LateEntry']['date'])) . ' 10:35:00';
                $datetime1 = new DateTime($start_time);
                $datetime2 = new DateTime($late_entry['LateEntry']['created']);
                $interval = $datetime1->diff($datetime2);

                $late_entry['LateEntry']['amount'] == 0 ? $late_entry_amount = 'warning' : $late_entry_amount = $late_entry['LateEntry']['amount'];

                fputcsv($f, array($i++, date('Y-m-d', strtotime($late_entry['LateEntry']['date'])), $interval->format('%h') . ' hours ' . $interval->format('%i') . ' minutes ' . $interval->format('%s') . ' seconds', date('l', strtotime($dailyreport)), $late_entry_amount));
                $late_fee += $late_entry['LateEntry']['amount'];
                $i++;
            }
            fputcsv($f, array('Total Late Fee: ' . floatval($late_entry['LateEntry']['amount'])));
        }

        fseek($f, 0);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement;filename="' . $filename . '";');
        fpassthru($f);
        exit;
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function monthly_report() {
        $this->set('cpage', 'month_report');
        $this->layout = 'user-inner';
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('DailyStatusMonth.month', '');
            $this->Session->write('DailyStatusMonth.year', '');

            if (!empty($this->request->data['DailyStatus']['month'])) {
                $this->Session->write('DailyStatusMonth.month', date('m', strtotime($this->request->data['DailyStatus']['month'])));
                $this->Session->write('DailyStatusMonth.year', date('Y', strtotime($this->request->data['DailyStatus']['month'])));
            }

            return $this->redirect(array('action' => 'monthly_report'));
        }

        if ($this->Session->check('DailyStatusMonth')) {
            $all = $this->Session->read('DailyStatusMonth');

            if ($all['month'] != '' && $all['year'] != '') {
                $dailyreports = $this->dates_month($all['month'], $all['year']);
            }
        } else {
            $all = array('month' => '', 'year' => '');
        }

        $this->set(compact('all'));
        $this->set(compact('dailyreports'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function reset_month() {
        $this->Session->delete('DailyStatusMonth');
        return $this->redirect(array('action' => 'monthly_report'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function export_to_csv() {
        $all = $this->Session->read('DailyStatusMonth');

        $dailyreports = $this->dates_month($all['month'], $all['year']);
        $employee = $this->requestAction('users/get_user', array('pass' => array($this->Session->read('User.id'))));

        $filename = $employee['User']['employee_name'] . '-' . date('F-Y', strtotime('01-' . $all['month'] . '-' . $all['year'])) . ".csv";

        $f = fopen('php://memory', 'w');
        fputcsv($f, array('S.No', 'Name', 'Date', 'Day', 'Projects', 'Worked Hours'));

        $leaves_month = $this->requestAction('leave/get_current_month_leave_approved/' . $this->Session->read('User.id') . '/' . $all['month'] . '/' . $all['year']);
        $holidays_month = $this->requestAction('holidays/get_holidays_per_month/' . $all['month'] . '/' . $all['year']);

        $leaves = $leaves_month;

        $check_sun_day = false;
        /*
          foreach($leaves_month as $key=>$value){
          if($key > 1){
          for($x = 1; $x <=(round($key)-1); $x++){
          date('D',strtotime('+'.$x.' day', strtotime($value))) == 'Sun' ? $check_sun_day = true : '';

          $check_sun_day == true ? array_push($leaves, date('Y-m-d',strtotime('+'.($x+1).' day', strtotime($value)))) : array_push($leaves, date('Y-m-d',strtotime('+'.$x.' day', strtotime($value))));
          }
          }
          }
         */
        $holidays = array();
        foreach ($holidays_month as $holiday_month) {
            $holidays[$holiday_month['Holiday']['name'] . ',' . $holiday_month['Holiday']['date']] = $holiday_month['Holiday']['date'];
        }

        $permissions = $this->requestAction('permission/get_permission_approved_per_month/' . $this->Session->read('User.id') . '/' . $all['month'] . '/' . $all['year']);

        $row = 1;

        $half_day = '';
        foreach ($dailyreports as $dailyreport):
            $color = '';
            $worked_hours = 0;
            $projects = "";
            $hours = 0;
            $break_hours = 0;

            $check_sunday = false;
            $check_leave = false;
            $check_holiday = false;
            $check_working_day = false;
            $check_permission = false;

            date('D', strtotime($dailyreport)) == 'Sun' ? $check_sunday = true : $check_sunday = false;

            if (!empty($leaves)) {
                if (array_key_exists(date('Y-m-d', strtotime($dailyreport)), $leaves)) {
                    $days = array_search(date('Y-m-d', strtotime($dailyreport)), $leaves);
                    $check_leave = true;
                }
            }

            if (!empty($holidays)) {
                if (in_array(date('Y-m-d', strtotime($dailyreport)), $holidays)) {
                    $holiday_name = str_replace(',' . $dailyreport, ' ', array_search(date('Y-m-d', strtotime($dailyreport)), $holidays));
                    $check_holiday = true;
                }
            }

            if (!empty($permissions)) {
                if (in_array(date('Y-m-d', strtotime($dailyreport)), $permissions)) {
                    $check_permission = true;
                }
            }

            $reports = $this->requestAction('daily_status/get_reports_by_id_and_date/' . $this->Session->read('User.id') . '/' . $dailyreport);
            !empty($reports) ? $check_working_day = true : $check_working_day = false;

            foreach ($reports as $report):

                $start_time = strtotime($report['DailyStatus']['start_time']);
                $end_time = strtotime($report['DailyStatus']['end_time']);

                $datetime1 = new DateTime($report['DailyStatus']['start_time']);
                $datetime2 = new DateTime($report['DailyStatus']['end_time']);
                $interval = $datetime1->diff($datetime2);
                $elapsed = $interval->format('%h hour %i minute');
                $hours += ($interval->format('%h') * 60) + ($interval->format('%i'));

                if ($report['DailyStatus']['projectname']) {
                    $projects .= $report['DailyStatus']['projectname'] . ',';
                }

                if ($report['DailyStatus']['category_id'] != 23 && $report['DailyStatus']['category_id'] != 22) {
                    $worked_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
                } else {
                    $break_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
                }

                if ($report['DailyStatus']['projectname']) {
                    $projects .= $report['DailyStatus']['projectname'] . ' , ';
                }
            endforeach;

            if ($check_working_day == true) {
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), $projects, gmdate("H:i", ($worked_hours * 60))));
                $check_leave == true ? $check_leave = false : '';
            }

            if ($check_sunday == true) {
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), 'Sunday', 'Sunday'));
            }

            if ($check_holiday == true) {
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), $holiday_name, $holiday_name));
            }

            if ($check_leave == true) {
                $leave_row = $this->requestAction('leave/get_leave_by_userid_date/' . $this->Session->read('User.id') . '/' . $dailyreport);
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), $leave_row['Leave']['reason'], 'Leave'));
            }

            if ($check_working_day == false && $check_sunday == false && $check_holiday == false && $check_leave == false) {
                fputcsv($f, array($row++, $employee['User']['employee_name'], date('d/m/Y', strtotime($dailyreport)), date('l', strtotime($dailyreport)), '--', '--'));
            }
        endforeach;

        $late_entries = $this->requestAction('late_entries/get_late_entry_by_user_id/' . $employee['User']['id']);
        $late_fee = 0;

        if (!empty($late_entries)) {
            fputcsv($f, array(''));
            fputcsv($f, array('', '', 'Late Entry'));
            fputcsv($f, array('S.No', 'date', 'Late Details', 'Amount'));

            $i = 1;
            foreach ($late_entries as $late_entry) {
                $start_time = date('d-m-Y', strtotime($late_entry['LateEntry']['date'])) . ' 10:35:00';
                $datetime1 = new DateTime($start_time);
                $datetime2 = new DateTime($late_entry['LateEntry']['created']);
                $interval = $datetime1->diff($datetime2);

                $late_entry['LateEntry']['amount'] == 0 ? $late_entry_amount = 'warning' : $late_entry_amount = $late_entry['LateEntry']['amount'];

                fputcsv($f, array($i++, date('Y-m-d', strtotime($late_entry['LateEntry']['date'])), $interval->format('%h') . ' hours ' . $interval->format('%i') . ' minutes ' . $interval->format('%s') . ' seconds', date('l', strtotime($dailyreport)), $late_entry_amount));
                $late_fee += $late_entry['LateEntry']['amount'];
                $i++;
            }
            fputcsv($f, array('Total Late Fee: ' . floatval($late_entry['LateEntry']['amount'])));
        }


        fseek($f, 0);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachement;filename="' . $filename . '";');
        fpassthru($f);
        exit;
    }

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

//		}

        $reports = $this->TempReport->find('all', array('conditions' => array('TempReport.user_id' => $this->Session->read('User.id'), 'TempReport.date' => date('Y-m-d')), 'order' => array('TempReport.start_time ASC')));

        $this->loadModel('LateEntry');
        $late_entry = $this->LateEntry->find('first', array(
            'conditions' => array(
                'LateEntry.date' => date('Y-m-d'),
                'LateEntry.user_id' => $this->Session->read('User.id'),
            )
        ));

        $office_times = $this->requestAction('entries/office_times');

        $this->loadModel('Permission');
        $permission_exists = $this->Permission->find('first', array('conditions' => array('Permission.date' => date('Y-m-d'), 'Permission.user_id' => $this->Session->read('User.id'), 'Permission.approved !=' => 2)));

        $this->loadModel('Leave');
        $leave = $this->Leave->find('all', array(
            'conditions' => array(
                'Leave.user_id' => $this->Session->read('User.id'),
                'Leave.days' => '0.50',
                'Leave.date' => date('Y-m-d')
        )));
        $this->set(compact('reports', 'late_entry', 'office_times', 'permission_exists', 'leave'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function delete_reports() {
        $this->layout = '';

        $this->loadModel('TempReport');
        $this->TempReport->delete($this->data['id']);

        $reports = $this->TempReport->find('all', array('conditions' => array('TempReport.user_id' => $this->Session->read('User.id'), 'TempReport.date' => date('Y-m-d')), 'order' => array('TempReport.start_time ASC')));

        $this->set(compact('reports'));
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

    public function test_mail() {
        $this->Email->to = 'prakash.paramanandam@arkinfotec.com';
//		$this->Email->cc = 'prakash.paramanandam@arkinfotec.com';
        $this->Email->subject = 'Subject goes here...';
        $this->Email->from = 'My Name <me@example.com>';
        $this->Email->template = 'test_template';
        $this->Email->sendAs = 'html';
//		$this->Email->delivery = 'smtp';
        if ($this->Email->send()) {
            echo 'sent';
        } else {
            echo 'not sent';
        }

        exit;
    }

}

?>