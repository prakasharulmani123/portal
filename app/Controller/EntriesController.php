<?php

class EntriesController extends AppController {

    public $name = 'Entries';
    public $helpers = array('Html', 'Form', 'Paginator', 'Time', 'Js' => array('Jquery'), 'Paginator');
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Paginator');

//////////////////////////////////////////////////////////////////////////////////////////////////

    public function beforeFilter() {
        parent::beforefilter();
        $this->__validateLoginStatus();
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_index() {
        $this->layout = "admin-inner";
        $this->set('cpage', 'entry');
        $this->layout = "admin-inner";
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('Entry.user_id', '');
            $this->Session->write('Entry.from_date', '');
            $this->Session->write('Entry.to_date', '');

            if ($this->request->data['Entry']['user_id'] != '') {
                $this->Session->write('Entry.user_id', $this->request->data['Entry']['user_id']);
            }

            if (!empty($this->request->data['Entry']['from_date']) && !empty($this->request->data['Entry']['to_date'])) {
                $this->Session->write('Entry.from_date', date('Y-m-d', strtotime($this->request->data['Entry']['from_date'])));
                $this->Session->write('Entry.to_date', date('Y-m-d', strtotime($this->request->data['Entry']['to_date'])));
            }

            return $this->redirect(array('action' => 'admin_index'));
        }

        if ($this->Session->check('Entry')) {
            $all = $this->Session->read('Entry');

            if ($all['user_id'] == '' && $all['from_date'] == '') {
                $entries = array('order' => array('Entry.time_in DESC'));
            } elseif ($all['user_id'] != '' && $all['from_date'] == '') {
                $entries = array('conditions' => array('Entry.user_id' => $all['user_id']), 'order' => array('Entry.time_in DESC'));
            } elseif ($all['user_id'] == '' && $all['from_date'] != '') {
                $entries = array('conditions' => array('Entry.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Entry.time_in DESC'));
            } elseif ($all['user_id'] != '' && $all['from_date'] != '') {
                $entries = array('conditions' => array('Entry.user_id' => $all['user_id'], 'Entry.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Entry.time_in DESC'));
            }
        } else {
            $all = array('user_id' => '', 'from_date' => '', 'to_date' => '');
            $entries = array('order' => array('Entry.time_in DESC'));
        }

        $entries = array_merge($entries, array('limit' => 25));
        $this->Paginator->settings = $entries;
        $entries = $this->Paginator->paginate('Entry');
        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('entries'));
        if ($this->request->is('ajax')) {
            $this->render('admin_index', 'ajaxpagination'); // View, Layout
        }
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    public function entry($id = NULL) {
        $isMobile = $this->isMobile();
        if ($isMobile == 0) {
            $ip = $this->request->clientIp();
            date_default_timezone_set("Asia/Kolkata");
            if ($id == 0) {
                $check_time = $this->Entry->find('first', array('conditions' => array('Entry.user_id' => $this->Session->read('User.id'), 'Entry.date' => date('Y-m-d'))));
                ;

                if (!empty($check_time)) {
                    if ($check_time['Entry']['time_out'] != '00/00/0000 00:00:00') {
                        $this->Entry->id = $check_time['Entry']['id'];

                        $this->request->data['Entry']['on_off'] = 1;

                        //added on 12-04-2014
                        $this->request->data['Entry']['time_out'] = "0000-00-00 00:00:00";
                        $this->request->data['Entry']['time_out_ip'] = "";
                        //end

                        if ($this->Entry->save($this->request->data)) {
                            $this->Session->setFlash('Your Timer is Already Started at ' . date('d-m-Y g:i A', strtotime($check_time['Entry']['time_in'])), 'flash_success');
                            return $this->redirect('/users/dashboard');
                        } else {
                            $this->Session->setFlash('Failed to Start the Timer', 'flash_error');
                        }
                    }
                } else {
                    $this->request->data['Entry']['user_id'] = $this->Session->read('User.id');
                    $this->request->data['Entry']['date'] = date('Y-m-d');
                    $this->request->data['Entry']['time_in'] = date('Y-m-d H:i:s');
                    $this->request->data['Entry']['time_out'] = "0000-00-00 00:00:00";
                    $this->request->data['Entry']['time_in_ip'] = $ip;
                    $this->request->data['Entry']['on_off'] = 1;

                    //1397278800
                    $timings = $this->office_times();
                    $excuse_time = strtotime(date('H:i', strtotime($timings['excuse_time'])));
                    $get_in_time = strtotime(date('H:i'));

                    //late entry
                    if ($get_in_time > $excuse_time) {
                        $this->requestAction('late_entries/late_entry/' . date('H-i-s') . '/' . date('Y-m-d'));
                    }

                    if ($this->Entry->save($this->request->data)) {
                        $this->Session->setFlash('Your Timer is Started ' . date('d-m-Y g:i A'), 'flash_success');
                        return $this->redirect('/users/dashboard');
                    } else {
                        $this->Session->setFlash('Failed to Start the Timer', 'flash_error');
                    }
                }
            } elseif ($id != 0) {
                $this->Entry->id = $id;

                $this->request->data['Entry']['time_out'] = date("Y-m-d H:i:s");
                $this->request->data['Entry']['time_out_ip'] = $ip;
                $this->request->data['Entry']['on_off'] = 0;

                $entry = $this->Entry->read();
                $hours = 0;
                $datetime1 = new DateTime($entry['Entry']['time_in']);
                $datetime2 = new DateTime(date("Y-m-d H:i:s"));
                $interval = $datetime1->diff($datetime2);
                $elapsed = $interval->format('%h hour %i minute');
                $hours = ($interval->format('%h') * 60) + ($interval->format('%i'));

                if ($this->Entry->save($this->request->data)) {
                    $this->Session->setFlash('Your Worked hours are ' . gmdate("H:i", ($hours * 60)), 'flash_success');
//				$this->Session->delete('DailyReportSend');
                    return $this->redirect('/users/dashboard');
                } else {
                    $this->Session->setFlash('Failed to End the Timer', 'flash_error');
                }
            }
        }
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    public function check_time_in_out() {
        return $this->Entry->find('first', array('conditions' => array('Entry.user_id' => $this->Session->read('User.id'), 'Entry.date' => date('Y-m-d'))));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_get_today_user_time_in_out() {
        return $this->Entry->find('all', array('conditions' => array('Entry.Date' => date('Y-m-d')), 'order' => array('Entry.time_out DESC')));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    public function get_latest_user_time_in_out() {
        return $this->Entry->find('all', array('conditions' => array('Entry.user_id' => $this->Session->read('User.id'), 'Entry.date between ? and ?' => array(date('Y-m-d', strtotime("-7 days")), date('Y-m-d'))), 'order' => array('Entry.created DESC')));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_reset() {
        $this->Session->delete('Entry');
        return $this->redirect(array('action' => 'admin_index'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    public function user_reset() {
        $this->Session->delete('UserEntry');
        return $this->redirect(array('controller' => 'entries', 'action' => 'index'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    public function index() {
        $this->set('cpage', 'entry');
        $this->layout = "user-inner";
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('UserEntry.from_date', '');
            $this->Session->write('UserEntry.to_date', '');

            if (!empty($this->request->data['Entry']['from_date']) && !empty($this->request->data['Entry']['to_date'])) {
                $this->Session->write('UserEntry.from_date', date('Y-m-d', strtotime($this->request->data['Entry']['from_date'])));
                $this->Session->write('UserEntry.to_date', date('Y-m-d', strtotime($this->request->data['Entry']['to_date'])));
            }

            return $this->redirect(array('action' => 'index'));
        }

        if ($this->Session->check('UserEntry')) {
            $all = $this->Session->read('UserEntry');

            if ($all['from_date'] == '') {
                $entries = array('conditions' => array('Entry.user_id' => $this->Session->read('User.id')), 'order' => array('Entry.time_in DESC'));
            } elseif ($all['from_date'] != '') {
                $entries = array('conditions' => array('Entry.user_id' => $this->Session->read('User.id'), 'Entry.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Entry.time_in DESC'));
            }
        } else {
            $all = array('from_date' => '', 'to_date' => '');
            $entries = array('conditions' => array('Entry.user_id' => $this->Session->read('User.id')), 'order' => array('Entry.time_in DESC'));
        }
        $entries = array_merge($entries, array('limit' => 25));
        $this->Paginator->settings = $entries;
        $entries = $this->Paginator->paginate('Entry');
        $this->set(compact('all'));
        $this->set(compact('entries'));
        if ($this->request->is('ajax')) {
            $this->render('admin_index', 'ajaxpagination'); // View, Layout
        }
    }

//////////////////////////////////////////////////////////////////////////////////////////////////

    /* Office Timings - Carefull to handle */
    public function office_times() {
        $this->loadModel('Users');
        $user = $this->Users->findById($this->Session->read('User.id'));
        $user_time = $user['Users']['timings'];
        return json_decode($user_time, true);
    }

    public function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    public function check_permission_saturday()
    {
        $current_day = date('d');
        $current_month = date('F Y');
        $offical_permissions[] = date('d', strtotime("second sat of {$current_month}"));
        $offical_permissions[]= date('d', strtotime("fourth sat of {$current_month}"));

        // for 2nd and 4th sat
        return in_array($current_day, $offical_permissions);
    }
}

?>