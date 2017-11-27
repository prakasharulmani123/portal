<?php

class CronController extends AppController {

    public $components = array('Email');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->layout = null;
    }

    /*    public function test() {
      // Check the action is being invoked by the cron dispatcher
      if (!defined('CRON_DISPATCHER')) {
      echo 'not defined';
      //			$this->redirect('/');
      exit();
      }
      else{
      echo 'defined';
      exit;
      }

      //no view
      $this->autoRender = false;

      //do stuff...

      return;
      }
     */

///////////////////////////////////////////////////////////////////////////////

    public function cron_test() {
        $this->Email->to = 'prakash.paramanandam@arkinfotec.com';
        $this->Email->subject = 'Portal : Cron test ';
        $this->Email->replyTo = 'admin@arkinfotec.com';
        $this->Email->from = 'admin@arkinfotec.com';
        $this->Email->sendAs = 'html';
        $this->Email->send('test');
        exit;
    }

    //runs daily 
    public function birthday_email() {

//        $this->Email->to = 'prakash.paramanandam@arkinfotec.com';
//        $this->Email->subject = 'Portal : Cron test ';
//        $this->Email->replyTo = 'admin@arkinfotec.com';
//        $this->Email->from = 'admin@arkinfotec.com';
//        $this->Email->sendAs = 'html';
//        $this->Email->send('test');

        $this->loadModel('User');
        $users = $this->User->find('all', array('conditions' => array('User.role' => 'user', 'User.active' => 1, 'User.employee_type' => 'P')));

        $email_users = array();
        $birthday_users = array();
        foreach ($users as $user) {
            if (date('m-d') == date('m-d', strtotime('-1 days', strtotime($user['User']['date_of_birth'])))) {
                $birthDate = date('d/m/Y', strtotime($user['User']['date_of_birth']));
                $birthDate = explode("/", $birthDate);

                $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));

                $birthday_users[$user['User']['id']] = $user['User']['employee_name'] . ',' . ($age);
            } else {
                $email_users[$user['User']['id']] = $user['User']['email'];
            }
        }

        //email notification before the birthday
        if ($birthday_users) {
            foreach ($birthday_users as $key => $birthday_user) {
                $user = $this->User->find('first', array('conditions' => array('User.id' => $key)));

//				$this->Email->to = array('prakash.paramanandam@arkinfotec.com');
                $this->Email->to = $email_users;
                $this->Email->subject = 'Birthday Reminder : ' . $user['User']['employee_name'];
                $this->Email->replyTo = 'admin@arkinfotec.com';
                $this->Email->from = 'admin@arkinfotec.com';
                $this->Email->template = 'birthday_notification';
                $this->Email->sendAs = 'html';
                $this->set('birthday_user', $birthday_user);
                $this->set('user', $user);
                $this->Email->send();

//				echo 'before Birthday - '.$birthday_user.'-'.date('Y-m-d H-:i:s').'<br>';
            }
        }

        $users = $this->User->find('all', array('conditions' => array('User.role' => 'user', 'User.active' => 1)));
        $birthday_users = array();
        foreach ($users as $user) {
            if (date('m-d') == date('m-d', strtotime($user['User']['date_of_birth']))) {
                $birthDate = date('d/m/Y', strtotime($user['User']['date_of_birth']));
                $birthDate = explode("/", $birthDate);

                $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));

                $birthday_users[$user['User']['id']] = $user['User']['employee_name'] . ',' . ($age + 1);
            }
        }

        //birthday mail on that day
        if ($birthday_users) {
            foreach ($birthday_users as $key => $birthday_user) {
                $user = $this->User->find('first', array('conditions' => array('User.id' => $key)));

                $quote = $this->birth_day_quote();
//				$this->Email->to = array('prakash.paramanandam@arkinfotec.com');
                $this->Email->to = $user['User']['email'];
                $this->Email->subject = 'Happy Birthday : ' . $user['User']['employee_name'];
                $this->Email->replyTo = 'admin@arkinfotec.com';
                $this->Email->from = 'admin@arkinfotec.com';
                $this->Email->template = 'birthday_mail';
                $this->Email->sendAs = 'html';
                $this->set('birthday_user', $birthday_user);
                $this->set('user', $user);
                $this->set('quote', $quote);
                $this->Email->send();

//				echo 'Happy Birthday - '.$birthday_user.'-'.date('Y-m-d H-:i:s').'<br>';
            }
        }

        //create pending reports
        $this->add_pending_report_automatically();

        //checks wrong ip address entry.
        $this->ip_checking();

        //update user leave on year starts.
        if (date('m') == '01' && date('d') == '01') {
            $users = $this->User->find('all', array('conditions' => array('User.active' => 1, 'User.role' => 'user')));

            foreach ($users as $user) {
                $this->User->id = $user['User']['id'];
                $this->User->saveField('casual_leave', 12);
            }
        }
    }

///////////////////////////////////////////////////////////////////////////////
//not using - to be confirm

    public function check_belated_pending_reports() {
        $this->loadModel('PendingReport');
        $this->loadModel('DailyStatus');
        $this->loadModel('Leave');
        $this->loadModel('SubLeave');

        $check_date = date('Y-m-d', strtotime('-6 day', strtotime(date('Y-m-d'))));

        $users = $this->requestAction('users/get_all_users');

        foreach ($users as $user):
            if(@$user['User']['employee_type'] == 'T'){
                continue;
            }
            $pending_reports = $this->PendingReport->find('first', array('conditions' => array('PendingReport.user_id' => $user['User']['id'], 'PendingReport.date' => $check_date, 'PendingReport.status' => 1)));

            if (!empty($pending_reports)) {

                $check_report = $this->DailyStatus->find('all', array('conditions' => array('DailyStatus.user_id' => $user['User']['id'], 'DailyStatus.date' => $check_date)));

                if (empty($check_report)) {

                    $insert_leave = array('Leave' => array('user_id' => $user['User']['id'],
                            'request' => 'past',
                            'date' => $check_date,
                            'days' => 1,
                            'reason' => 'Pending Report Delayed',
                            'approved' => 1,
                            'remarks' => 'Pending Report Delayed'));

                    $this->Leave->save($insert_leave);
                    $leave_id = $this->Leave->getLastInsertId();

                    $leave_count = $this->requestAction('leave/user_get_all_leave_count_per_year/' . $user['User']['id'] . '/' . date('Y'));

                    $insert_sub_leave = array();
                    if ($leave_count + 1 <= 12) {
                        $insert_sub_leave['SubLeave']['status'] = 'C';
                    } else {
                        $insert_sub_leave['SubLeave']['status'] = 'P';
                    }

                    $insert_sub_leave['SubLeave']['leave_id'] = $leave_id;
                    $insert_sub_leave['SubLeave']['date'] = $check_date;
                    $insert_sub_leave['SubLeave']['day'] = 1;

                    $this->SubLeave->save($insert_sub_leave);
                    $this->PendingReport->delete($pending_reports['PendingReport']['id']);
                }
            }
        endforeach;
    }

///////////////////////////////////////////////////////////////////////////////

    public function add_pending_report_automatically() {
        $check_day = date('Y-m-d', strtotime('-1 days'));

        if (date('D', strtotime($check_day)) != 'Sun') {
            $this->loadModel('User');
            $this->loadModel('DailyStatus');
            $this->loadModel('Permission');
            $this->loadModel('Leave');
            $this->loadModel('Holiday');
            $this->loadModel('PendingReport');
            $this->loadModel('Entry');
            $this->loadModel('SubLeave');

            $users = $this->User->findAllByRoleAndActiveAndSuperUser('user', 1, 0);

            foreach ($users as $user):
                if(@$user['User']['employee_type'] == 'T'){
                    continue;
                }
                $check_report_exists = $this->DailyStatus->findByUserIdAndDate($user['User']['id'], $check_day);

                if (empty($check_report_exists)):

                    $check_leave_exists = array();
                    $check_sub_leave_exists = $this->SubLeave->find('first', array('conditions' => array('SubLeave.date' => $check_day)));

                    if (!empty($check_sub_leave_exists)) {
                        if ($check_sub_leave_exists['Leave']['approved'] != 2 && $check_sub_leave_exists['Leave']['user_id'] == $user['User']['id']) {
                            $check_leave_exists = $check_sub_leave_exists['Leave'];
                        }
                    }

                    /* $check_permission_exists = $this->Permission->find('first', array('conditions'=>array('Permission.user_id'=>$user['User']['id'],'Permission.date'=>$check_day, 'Permission.approved !='=>2))); */
                    $check_holiday_exists = $this->Holiday->find('first', array('conditions' => array('Holiday.date' => $check_day)));
                    $check_pending_report_exists = $this->PendingReport->find('first', array('conditions' => array('PendingReport.user_id' => $user['User']['id'], 'PendingReport.date' => $check_day, 'PendingReport.status !=' => 2)));

                    if (empty($check_leave_exists) && /* empty($check_permission_exists) && */empty($check_holiday_exists) && empty($check_pending_report_exists)):
                        $add_pending_report = array();

                        $add_pending_report['PendingReport']['user_id'] = $user['User']['id'];
                        $add_pending_report['PendingReport']['date'] = $check_day;
                        $add_pending_report['PendingReport']['status'] = 0;


                        $entry_exists = $this->Entry->find('first', array('conditions' => array('Entry.user_id' => $user['User']['id'], 'Entry.date' => $check_day)));

                        if (!empty($entry_exists)) {

                            $org_time_in_hour = date('H', strtotime($entry_exists['Entry']['time_in']));
                            $time_in_hour = date('H', strtotime($entry_exists['Entry']['time_in']));
                            if ($time_in_hour > 12) {
                                $time_in_hour = ($time_in_hour - 12);
                            }
                            $time_in_minute = date('i', strtotime($entry_exists['Entry']['time_in']));
                            $minute_part_one = substr(date('i', strtotime($entry_exists['Entry']['time_in'])), 0, 1);
                            $minute_part_two = substr(date('i', strtotime($entry_exists['Entry']['time_in'])), 1, 2);

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

                            $start_time = date('Y-m-d H:i:s', strtotime($check_day . ' ' . $time_in_hour . ':' . $time_in_minute));
//							$start_time = date('Y-m-d H:i:s', strtotime($check_day.' '.$org_time_in_hour.':'.$time_in_minute));
                            $add_pending_report['PendingReport']['start_time'] = $start_time;
                            $add_pending_report['PendingReport']['reason'] = 'Entry created on ' . $start_time . '. But Daily Status Report not sent.';
                        }

                        $this->PendingReport->saveAll($add_pending_report);
                    endif;

                endif;
            endforeach;
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function birth_day_quote() {
        $quote = array();

        $quote[0] = "And in the end, it's not the years in your life that count. It's the life in your years.";
        $quote[1] = "We turn not older with years but newer every day.";
        $quote[2] = "Today you are You, that is truer than true. There is no one alive who is Youer than You.";
        $quote[3] = "God gave us the gift of life; it is up to us to give ourselves the gift of living well.";
        $quote[4] = "It is not true that people stop pursuing dreams because they grow old, they grow old because they stop pursuing dreams.";
        $quote[5] = "Youth is happy because it has the ability to see beauty. Anyone who keeps the ability to see beauty never grows old.";
        $quote[6] = "Age is strictly a case of mind over matter. If you don't mind, it doesn't matter.";
        $quote[7] = "As you grow up, make sure you have more dreams than memories, more opportunities than chances, more hard work than luck and more friends than acquaintances.";
        $quote[8] = "A birthday is a time to reflect on the year gone by, but to also set your goals for the upcoming year.";
        $quote[9] = "There is a fountain of youth: it is your mind, your talents, the creativity you bring to your life and the lives of the people you love. When you learn to tap this source, you will have truly defeated age.";
        $quote[10] = "Live not one's life as though one had a thousand years, but live each day as the last.";
        $quote[11] = "Anyone who stops learning is old, whether at twenty or eighty. Anyone who keeps learning stays young. The greatest thing in life is to keep your mind young.";
        $quote[12] = "Most of us can remember a time when a birthday – especially if it was one's own – brightened the world as if a second sun has risen.";
        $quote[13] = "You were born, and with you endless possibilities―very few ever to be realized. It's okay. Life was never about what you could do, but what you would do";
        $quote[14] = "Everyday is a birthday; every moment of it is new to us; we are born again, renewed for fresh work and endeavor.";
        $quote[15] = "God gave us the gift of life; it is up to us to give ourselves the gift of living well.";

        $rand_no = mt_rand(0, 15);

        return $quote[$rand_no];
    }

///////////////////////////////////////////////////////////////////////////////
    //runs every week
    public function db_backup() {
        $db = $this->requestAction('users/database_mysql_dump');

        $fileName = $db['database'] . '-backup-' . /* date('Y-m-d') . */'.sql';
        $fp = fopen(WWW_ROOT . "files/db_backup/" . $fileName, "wb");
        fwrite($fp, $db['content']);
        fclose($fp);

        $all_to = $all_cc = $all_bcc = array();

        $add_to = $this->requestAction('emails/get_email/to');
        $add_cc = $this->requestAction('emails/get_email/cc');
        $add_bcc = $this->requestAction('emails/get_email/bcc');

        foreach ($add_to as $to) {
            $array = explode(',', $to['Email']['options']);

            foreach ($array as $key => $value) {
                if ($value == 3) {
                    $all_to[$to['Email']['id']] = $to['Email']['email'];
                }
            }
        }

        foreach ($add_cc as $cc) {
            $array = explode(',', $cc['Email']['options']);

            foreach ($array as $key => $value) {
                if ($value == 3) {
                    $all_cc[$cc['Email']['id']] = $cc['Email']['email'];
                }
            }
        }

        foreach ($add_bcc as $bcc) {
            $array = explode(',', $bcc['Email']['options']);

            foreach ($array as $key => $value) {
                if ($value == 3) {
                    $all_bcc[$bcc['Email']['id']] = $bcc['Email']['email'];
                }
            }
        }

        if (!empty($all_to) || !empty($all_cc) || !empty($all_bcc)) {
//			$this->Email->to = array('prakash.paramanandam@arkinfotec.com');
            $this->Email->to = $all_to;
            $this->Email->cc = $all_cc;
            $this->Email->bcc = $all_bcc;
            $this->Email->subject = 'Portal : Weekly DB Backup';
            $this->Email->replyTo = 'admin@arkinfotec.com';
            $this->Email->from = 'admin@arkinfotec.com';
            $this->Email->sendAs = 'html';
            $this->Email->attachments = array(WWW_ROOT . 'files/db_backup/' . $fileName);

            $message = '<b>Portal</b><br><br>';
            $message .= '<b>DB Backup : </b>' . $db['database'] . '-backup-' . /* date('Y-m-d') . */ '.sql' . ' <br><br>';
            $message .= '<b>Created : </b>' . date('Y-m-d h:i:a') . ' <br>';

            $this->Email->send($message);
        }

        //pending reports summary to admin
        $this->pending_reports_summary();
    }

///////////////////////////////////////////////////////////////////////////////

    public function pending_reports_summary() {
        $this->loadModel('Leave');
        $this->loadModel('Permission');
        $this->loadModel('PendingReport');

        $pending_leaves = $this->Leave->find('all', array('conditions' => array('Leave.approved' => 0)));
        $pending_permissions = $this->Permission->find('all', array('conditions' => array('Permission.approved' => 0)));
        $pending_reports = $this->PendingReport->find('all', array('conditions' => array('PendingReport.status' => 0, 'PendingReport.start_time !=' => '0000-00-00 00:00:00')));
        $pending_report_users = $this->PendingReport->find('all', array('conditions' => array('PendingReport.status' => 0, 'PendingReport.start_time !=' => '0000-00-00 00:00:00'), 'group' => array('PendingReport.user_id')));

        if (!empty($pending_leaves) || !empty($pending_permissions) || !empty($pending_reports)) {
            $all_to = $all_cc = $all_bcc = array();

            $add_to = $this->requestAction('emails/get_email/to');
            $add_cc = $this->requestAction('emails/get_email/cc');
            $add_bcc = $this->requestAction('emails/get_email/bcc');

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

            if (!empty($all_to) || !empty($all_cc) || !empty($all_bcc)) {
                $this->Email->to = $all_to;
                $this->Email->cc = $all_cc;
                $this->Email->bcc = $all_bcc;
                $this->Email->subject = 'Portal : Pending Report Summary';
                $this->Email->replyTo = 'admin@arkinfotec.com';
                $this->Email->from = 'admin@arkinfotec.com';
                $this->Email->sendAs = 'html';
                $this->Email->template = 'pending_report_summary';
                $this->set(compact('pending_leaves', 'pending_permissions', 'pending_reports', 'pending_report_users'));
                $this->Email->send();
            }
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function ip_checking() {
        $this->loadModel('Entry');
        $entries = $this->Entry->find('all', array('conditions' => array('Entry.date' => date('Y-m-d', strtotime('-1 days')))));
        $wrong_ip_entries = array();

        foreach ($entries as $entry) {
            if (($entry['Entry']['time_in_ip'] != $entry['Entry']['time_out_ip']) && $entry['Entry']['time_in_ip'] != '' && $entry['Entry']['time_out_ip'] != '') {
                array_push($wrong_ip_entries, $entry);
            }
        }

        if (!empty($wrong_ip_entries)) {
            $all_to = $all_cc = $all_bcc = array();

            $add_to = $this->requestAction('emails/get_email/to');
            $add_cc = $this->requestAction('emails/get_email/cc');
            $add_bcc = $this->requestAction('emails/get_email/bcc');

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

            if (!empty($all_to) || !empty($all_cc) || !empty($all_bcc)) {
                $this->Email->to = $all_to;
                $this->Email->cc = $all_cc;
                $this->Email->bcc = $all_bcc;
                $this->Email->subject = 'Portal : Wrong IP Address - Time In & Out Entries';
                $this->Email->replyTo = 'admin@arkinfotec.com';
                $this->Email->from = 'admin@arkinfotec.com';
                $this->Email->sendAs = 'html';

                $message = 'Dear admin, <br><br>';
                $message .= 'The below employee list Timer On & OFF IP address were not same on ' . date('Y-m-d', strtotime('-1 days')) . '. <br>';

                $i = 1;
                foreach ($wrong_ip_entries as $wrong_ip_entry) {
                    $user = $this->requestAction('users/get_user/' . $wrong_ip_entry['Entry']['user_id']);
                    $message .= $i++ . ' . ' . $user['User']['employee_name'] . '<br>';
                }

                $message .= '<br>';

                $this->Email->send($message);
            }
        }
    }

}

?>