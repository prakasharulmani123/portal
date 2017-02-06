<?php

class UserAttendancesController extends AppController {

    public $name = 'UserAttendances';
    public $helpers = array('Html', 'Form', 'Paginator', 'Time', 'Js' => array('Jquery'), 'Paginator');
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler', 'Paginator');

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function beforeFilter() {
        parent::beforefilter();
        $this->__validateLoginStatus();
    }

///////////////////////////////////////////////////////////////////////////////

    public function index() {
        $this->layout = "user-inner";

        $this->set('cpage', 'my_attendance');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('UserAttendance.from_date', '');
            $this->Session->write('UserAttendance.to_date', '');

            if (!empty($this->request->data['UserAttendance']['from_date']) && !empty($this->request->data['UserAttendance']['to_date'])) {
                $this->Session->write('UserAttendance.from_date', date('Y-m-d', strtotime($this->request->data['UserAttendance']['from_date'])));
                $this->Session->write('UserAttendance.to_date', date('Y-m-d', strtotime($this->request->data['UserAttendance']['to_date'])));
            }

            return $this->redirect(array('action' => 'index'));
        }

        if ($this->Session->check('UserAttendance')) {
            $all = $this->Session->read('UserAttendance');

            if ($all['from_date'] != '') {
                $attendances = array('conditions' => array('UserAttendance.user_id' => $this->Session->read('User.id'), 'UserAttendance.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('UserAttendance.date' => 'DESC'), 'group' => array('UserAttendance.date'));
            } else {
                $attendances = array('conditions' => array('UserAttendance.user_id' => $this->Session->read('User.id')), 'order' => array('UserAttendance.date' => 'DESC'), 'group' => array('UserAttendance.date'));
            }
        } else {
            $all = array('from_date' => '', 'to_date' => '');
            $attendances = array('conditions' => array('UserAttendance.user_id' => $this->Session->read('User.id')), 'order' => array('UserAttendance.date' => 'DESC'), 'group' => array('UserAttendance.date'));
        }
        $attendances = array_merge($attendances, array('limit' => 10));
        $this->Paginator->settings = $attendances;
        $attendances = $this->Paginator->paginate('UserAttendance');
        $this->set(compact('attendances', 'all'));
        if ($this->request->is('ajax')) {
            $this->render('index', 'ajaxpagination'); // View, Layout
        }
//    
//        $this->layout = "user-inner";
//        $this->set('cpage', 'my_attendance');
//        $this->set('attendances', $this->UserAttendance->find('all', array('conditions' => array('UserAttendance.user_id' => $this->Session->read('User.id')), 'order' => array('UserAttendance.date'  => 'DESC'))));
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_index() {
        $this->set('cpage', 'my_attendance');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('UserAttendance.user_id', '');
            $this->Session->write('UserAttendance.from_date', '');
            $this->Session->write('UserAttendance.to_date', '');

            if ($this->request->data['UserAttendance']['user_id'] != '') {
                $this->Session->write('UserAttendance.user_id', $this->request->data['UserAttendance']['user_id']);
            }

            if (!empty($this->request->data['UserAttendance']['from_date']) && !empty($this->request->data['UserAttendance']['to_date'])) {
                $this->Session->write('UserAttendance.from_date', date('Y-m-d', strtotime($this->request->data['UserAttendance']['from_date'])));
                $this->Session->write('UserAttendance.to_date', date('Y-m-d', strtotime($this->request->data['UserAttendance']['to_date'])));
            }

            return $this->redirect(array('action' => 'admin_index'));
        }

        if ($this->Session->check('UserAttendance')) {
            $all = $this->Session->read('UserAttendance');

            if ($all['user_id'] == '' && $all['from_date'] == '') {
                $attendances = array('order' => array('UserAttendance.date' => 'DESC'), 'group' => array('UserAttendance.date,UserAttendance.user_id'));
            } elseif ($all['user_id'] != '' && $all['from_date'] == '') {
                $attendances = array('conditions' => array('UserAttendance.user_id' => $all['user_id']), 'order' => array('UserAttendance.date' => 'DESC'), 'group' => array('UserAttendance.date,UserAttendance.user_id'));
            } elseif ($all['user_id'] == '' && $all['from_date'] != '') {
                $attendances = array('conditions' => array('UserAttendance.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('UserAttendance.date' => 'DESC'), 'group' => array('UserAttendance.date,UserAttendance.user_id'));
            } elseif ($all['user_id'] != '' && $all['from_date'] != '') {
                $attendances = array('conditions' => array('UserAttendance.user_id' => $all['user_id'], 'UserAttendance.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('UserAttendance.date' => 'DESC'), 'group' => array('UserAttendance.date,UserAttendance.user_id'));
            }
        } else {
            $all = array('user_id' => '', 'from_date' => '', 'to_date' => '');
            $attendances = array('order' => array('UserAttendance.date' => 'DESC'), 'group' => array('UserAttendance.date', 'UserAttendance.user_id'));
        }
        $attendances = array_merge($attendances, array('limit' => 10));
        $this->Paginator->settings = $attendances;
        $attendances = $this->Paginator->paginate('UserAttendance');

        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('attendances'));
        if ($this->request->is('ajax')) {
            $this->render('admin_index', 'ajaxpagination'); // View, Layout
        }
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_reset() {
        $this->Session->delete('UserAttendance');
        return $this->redirect(array('action' => 'admin_index'));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function reset() {
        $this->Session->delete('UserAttendance');
        return $this->redirect(array('action' => 'index'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function import_attendance() {
        if ($this->Session->read('User.super_user') == 1 || in_array($this->Session->read('User.id'), array(26))) {
            if ($this->request->is('post')) {

                $allowed = array('csv');
                $filename = $this->request->data['User']['upload']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array($ext, $allowed)) {
                    $this->Session->setFlash('Not a valid File', 'flash_error');
                    return $this->redirect('/user_attendances/import_attendance');
                }

                $fileName = 'attendance_import_csv';
                $uploadPath = 'uploads/';
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $uploadFile = $uploadPath . $fileName;
                if (move_uploaded_file($this->request->data['User']['upload']['tmp_name'], $uploadFile)) {
                    $file = Router::url("/$uploadFile", true);
                    $messages = $this->import($file, $this->request->data['User']['import_date']);
                    $this->Session->setFlash("Attendance imported successfully", 'flash_success');
                    return $this->redirect('/user_attendances/import_attendance');
                } else {
                    $this->Session->setFlash('Failed to upload', 'flash_error');
                }
            }
            $this->layout = "user-inner";
            $this->set('cpage', 'import_attendance');
        } else {
            return $this->redirect('/users/dashboard');
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_import_attendance() {
        if ($this->request->is('post')) {

            $allowed = array('csv');
            $filename = $this->request->data['User']['upload']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array($ext, $allowed)) {
                $this->Session->setFlash('Not a valid File', 'flash_error');
                return $this->redirect('/admin/user_attendances/import_attendance');
            }

            $fileName = 'attendance_import_csv';
            $uploadPath = 'uploads/';
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $uploadFile = $uploadPath . $fileName;
            if (move_uploaded_file($this->request->data['User']['upload']['tmp_name'], $uploadFile)) {
                $file = Router::url("/$uploadFile", true);
                $messages = $this->import($file, $this->request->data['User']['import_date']);
                $this->Session->setFlash("Attendance imported successfully", 'flash_success');
                return $this->redirect('/admin/user_attendances/import_attendance');
            } else {
                $this->Session->setFlash('Failed to upload', 'flash_error');
            }
        }
        $this->set('cpage', 'import_attendance');
    }

///////////////////////////////////////////////////////////////////////////////

    public function import($filename, $date) {
        // open the file
        $handle = fopen($filename, "r");
        // read the 1st row as headings
        $header = fgetcsv($handle);

        if (trim($header[0]) != 'Employee Code' || trim($header[1]) != 'Employee Name' || trim($header[2]) != 'InTime' || trim($header[3]) != 'OutTime' || trim($header[4]) != 'PunchRecords') {
            $this->Session->setFlash('File Headers Mismatch !!!', 'flash_error');
            if ($this->Session->read('User.role') == 'user')
                return $this->redirect('/user_attendances/import_attendance');
            else if ($this->Session->read('User.role') == 'admin')
                return $this->redirect('/admin/user_attendances/import_attendance');
        }
        // read each data row in the file
        while (($row = fgetcsv($handle)) !== FALSE) {
            $user = ClassRegistry::init('User')->getUserByEmpId($row[0]);
            if ($user) {
                $data['user_id'] = $user['User']['id'];
                $data['date'] = date('Y-m-d', strtotime($date));
                $data['start_time'] = $row[2];
                $data['end_time'] = $row[3];
                $data['total_elapsed'] = $this->_timeDiff($data['start_time'], $data['end_time']);
                $records = str_replace(['(', ')'], ['#', '#'], $row[4]);

                $in_mins = 0;
                $out_mins = 0;
                preg_match_all('/(.*?)#TD#/s', $records, $matches);
                $data['log'] = [];

                if ($matches && isset($matches[1])) {
                    $in_outs = array_filter($matches[1], function($v) {
                        return strpos($v, ':in') !== false || strpos($v, ':out') !== false;
                    });
                    $in_outs = array_values($in_outs);

                    foreach ($in_outs as $k => $in_out) {
                        if (strpos($in_out, ':in') !== false) {
                            $t1 = str_replace(':in', '', $in_out);
                            $t2 = isset($in_outs[$k + 1]) ? str_replace(':out', '', $in_outs[$k + 1]) : '';
                            $e = '';
                            $d = 'i';

                            if ($t2) {
                                $e = $this->_timeDiff($t1, $t2);
                                $in_mins += $this->_getMins($e);
                            }
                        } else if (strpos($in_out, ':out') !== false) {
                            $t1 = str_replace(':out', '', $in_out);
                            $t2 = isset($in_outs[$k + 1]) ? str_replace(':in', '', $in_outs[$k + 1]) : '';
                            $e = '';
                            $d = 'o';

                            if ($t2) {
                                $e = $this->_timeDiff($t1, $t2);
                                $out_mins += $this->_getMins($e);
                            }
                        }

                        $data['log'][] = [
                            'd' => $d,
                            't1' => $t1,
                            't2' => $t2,
                            'e' => $e,
                        ];
                    }
                }

                $data['in_elapsed'] = gmdate("H:i", ($in_mins * 60));
                $data['out_elapsed'] = gmdate("H:i", ($out_mins * 60));

                if ($data['log']) {
                    $data['log'] = json_encode($data['log']);
                    ClassRegistry::init('UserAttendance')->deleteAll(['user_id' => $data['user_id'], 'date' => $data['date']]);
                    ClassRegistry::init('UserAttendance')->saveMany(['UserAttendance' => $data]);
                }
            }
        }
        // close the file
        fclose($handle);
        // return the messages
        @unlink($filename);
    }

    private function _timeDiff($time1, $time2) {
        $datetime1 = new DateTime($time1);
        $datetime2 = new DateTime($time2);
        $interval = $datetime1->diff($datetime2);
        $hours = ($interval->format('%h') * 60) + ($interval->format('%i'));
        return gmdate("H:i", ($hours * 60));
    }

    private function _getMins($time) {
        return (date('H', strtotime($time)) * 60) + date('i', strtotime($time));
    }

}

?>