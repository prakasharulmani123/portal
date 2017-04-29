<?php

class UsersController extends AppController {

    public $name = 'Users';
    public $helpers = array('Html', 'Form', 'Js', 'Paginator', 'Time');
    public $components = array('Session', 'Cookie', 'RequestHandler', 'Email');

///////////////////////////////////////////////////////////////////////////////

    public function beforeFilter() {
        parent::beforefilter();
        if ($this->action != 'forgot_password' && $this->action != 'get_user') {
            $this->__validateLoginStatus();
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function login() {
        $this->layout = "login";
        if ($this->request->is('post')) {
            $user = $this->User->find('first', array('conditions' => array('User.email' => $this->data['User']['email'], 'User.active' => 1)));
            if (!empty($user)) {
                if ($user['User']['role'] == 'user') {
                    if ($user['User']['password'] == md5($this->data['User']['password'])) {
                        $this->Session->write('User.id', $user['User']['id']);
                        $this->Session->write('User.email', $user['User']['email']);
                        $this->Session->write('User.role', $user['User']['role']);
                        $this->Session->write('User.name', $user['User']['employee_name']);
                        $this->Session->write('User.photo', $user['User']['photo']);
                        $this->Session->write('User.casual_leave', $user['User']['casual_leave']);
                        $this->Session->write('User.super_user', $user['User']['super_user']);
                        $this->Session->write('User.timings', $user['User']['timings']);
//			$this->requestAction('pendingreports/check_belated_pending_reports');
                        $this->redirect('/users/dashboard');
                    } else {
                        $this->Session->setFlash("Password Doesn't Match", 'flash_error');
                    }
                } else {
                    $this->Session->setFlash("Dear Admin , Please Log in Here", "flash_success");
                    return $this->redirect('/admin');
                }
            } else {
                $this->Session->setFlash("This User doesn't exists", "flash_error");
            }
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_login() {
        $this->layout = "login";
        if ($this->request->is('post')) {
            $user = $this->User->find('first', array('conditions' => array('User.email' => $this->data['User']['email'], 'User.active' => 1)));
            if (!empty($user)) {
                if ($user['User']['role'] == 'admin' || ($user['User']['role'] == 'user' && $user['User']['super_user'] == 1)) {
                    if ($user['User']['password'] == md5($this->data['User']['password'])) {
                        $this->Session->write('User.id', $user['User']['id']);
                        $this->Session->write('User.email', $user['User']['email']);
                        $this->Session->write('User.role', $user['User']['role']);
                        $this->Session->write('User.name', $user['User']['employee_name']);
                        $this->Session->write('User.photo', $user['User']['photo']);
                        $this->Session->write('User.super_user', $user['User']['super_user']);
                        $this->Session->write('User.access', $user['User']['access']);
                        $this->redirect(array('controller' => 'users', 'action' => 'index', 'admin' => true));
                    } else {
                        $this->Session->setFlash("Password Doesn't Match", 'flash_error');
                    }
                } else {
                    $this->Session->setFlash("Dear User , Please Log in Here", "flash_success");
                    return $this->redirect('/');
                }
            } else {
                $this->Session->setFlash("This User doesn't exists", "flash_error");
            }
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_index() {
        $this->layout = "admin-inner";
        $this->set('cpage', 'dashboard');
        $this->set('entries', $this->requestAction('admin/entries/get_today_user_time_in_out'));
        $this->set('reports', $this->requestAction('admin/dailystatus/get_recent_reports'));
        $this->set('emails', $this->requestAction('emails/all_email'));

        $this->set('birthdays', $this->User->find('all', array('conditions' => array('User.role' => 'user', 'User.active' => 1),
                    'order' => array('MONTH(User.date_of_birth) ASC', 'DATE(User.date_of_birth) ASC')
        )));
    }

///////////////////////////////////////////////////////////////////////////////

    public function dashboard() {
        $this->layout = "user-inner";
        $this->set('cpage', 'dashboard');

        $this->set('entries', $this->requestAction('entries/get_latest_user_time_in_out'));
        $this->set('reports', $this->requestAction('dailystatus/user_get_recent_reports'));
        $this->set('leaves', $this->requestAction('leave/user_get_current_month_leave'));
        $this->set('permissions', $this->requestAction('permission/user_get_current_month_all_permission'));
        $this->set('holidays', $this->requestAction('holidays/get_holiday_per_month'));

        if ($this->Session->check('LateEntry')) {
            $this->set('late_entry', $this->Session->read('LateEntry'));
            $this->Session->delete('LateEntry');
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function index() {
        $this->layout = "user-inner";
        $this->set('cpage', '');
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_employee($status = NULL) {
        $this->layout = "admin-inner";
        $this->set('users', $this->User->find('all', array('conditions' => array('User.role' => 'user', 'User.active' => $status))));
        $admins = $this->User->find('first', array('conditions' => array('User.role' => 'admin', 'User.active' => $status)));
        $super_users = $this->User->find('list', array('conditions' => array('User.super_user' => 1, 'User.active' => $status)));
        $this->set('super_users', $super_users);
        $this->set('admins', $admins);
        $this->set('status', $status);
        $this->set('cpage', 'employee');
    }

///////////////////////////////////////////////////////////////////////////////
    public function admin_access($id = null) {
        $this->layout = "admin-inner";
        $this->set('cpage', 'employee');
        $this->set('id', $id);
        $findaccess = $this->User->find('first', array('conditions' => array('User.super_user' => 1, 'User.role' => 'user', 'User.id' => $id)));
        $findaccesses = $findaccess['User']['access'];
        $this->set('findaccesses', $findaccesses);
        $this->loadModel('Module');
        $roles = $this->Module->find('all', array('conditions' => array('Module.parent_id' => 0)));
        $this->set('roles', $roles);
        $childs = $this->Module->find('all', array('conditions' => array('Module.parent_id !=' => 0)));
        $this->set('childs', $childs);
        if ($this->request->is('post')) {
            $values = $this->request->data;
            $modules = array_filter($values['modules'], function($values) {
                return ($values != 0 );
            });
            $module = json_encode(array_values($modules), false);
            $this->request->data['User']['id'] = $id;
            $this->request->data['User']['access'] = $module;
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash('Added Sucessfully', 'flash_success');
                return $this->redirect('/admin/users/employee/1');
            } else {
                $this->Session->setFlash('Failed to add ', 'flash_error');
                return $this->redirect('/admin/users/dashboard');
            }
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_add() {
        $this->layout = "admin-inner";
        $this->set('cpage', 'employee');
        if ($this->request->is('post') || $this->request->is('put')) {
            $user = $this->get_all_users();
            if (empty($user)) {
                $this->request->data['User']['employee_id'] = '001';
            } else {
                $user = $this->get_last_added_user();
                $emp_id = $this->get_next_employee_id($user['User']['employee_id']);
                $this->request->data['User']['employee_id'] = $emp_id;
            }

            //casual leave set
            date('d', strtotime($this->data['User']['joined_on'])) > 15 ? $casual_leave = 12 - date('m', strtotime($this->data['User']['joined_on'])) : $casual_leave = 12 - (date('m', strtotime($this->data['User']['joined_on'])) - 1);
            $this->request->data['User']['casual_leave'] = $casual_leave;

            //check and upload images
            $this->Img = $this->Components->load('Img');
            if ($this->request->data['User']['upload']['name']) {
                $image_information = pathinfo(str_replace(' ', '_', $this->request->data['User']['upload']['name']));
                $newName = $image_information['filename'] . '_' . time();
                $ext = $this->Img->ext($this->request->data['User']['upload']['name']);
                $origFile = $newName . '.' . $ext;
                $targetdir = WWW_ROOT . 'img/users/original';
                $upload = $this->Img->upload($this->request->data['User']['upload']['tmp_name'], $targetdir, $origFile);
                if ($upload == 'Success') {
                    $this->Img->resampleGD($targetdir . DS . $origFile, WWW_ROOT . 'img/users/large/', $origFile, 800, 800, 1, 0);
                    $this->Img->resampleGD($targetdir . DS . $origFile, WWW_ROOT . 'img/users/medium/', $origFile, 288, 155, 1, 0);
                    $this->Img->resampleGD($targetdir . DS . $origFile, WWW_ROOT . 'img/users/small/', $origFile, 180, 180, 1, 0);
                    $this->request->data['User']['photo'] = $origFile;
                } else {
                    $this->request->data['User']['photo'] = '';
                }
            }
            //end

            $password = $this->randomPassword();
            $this->request->data['User']['password'] = md5($password);
            $this->request->data['User']['date_of_birth'] = date('Y-m-d', strtotime($this->data['User']['date_of_birth']));
            $this->request->data['User']['joined_on'] = date('Y-m-d', strtotime($this->data['User']['joined_on']));
            $this->request->data['User']['timings'] = '{"office_start_time":"09:30:00","office_end_time":"19:00:00","late_entry_end_time":"19:30:00","excuse_time":"10:05:00","permission_start_time":"10:30:00","permission_max_time":"11:30:00","half_day_excuse_time":"11:45:00","half_day_grace_time":"00:15:00","permission_hours":"02:00:00","office_hours":"08:00:00","total_hours_in_office":"09:30:00","report_send_grace_time":"00:10:00","permission_back_time":"06:00:00"}';
            if ($this->User->save($this->request->data)) {
                $this->user_account_activation_mail($this->User->getLastInsertId(), $password);
                $this->Session->setFlash('Employee Added Sucessfully', 'flash_success');
                return $this->redirect('/admin/users/add');
            } else {
                $this->Session->setFlash('Failed to add Employee', 'flash_error');
                return $this->redirect('/admin/users/add');
            }
        } else {
            $countries = $this->requestAction('countries/get_all_countries');
            $this->set(compact('countries'));

            $states = $this->requestAction('states/get_states/1');
            $this->set(compact('states'));
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_edit($id = null) {
        $this->layout = "admin-inner";
        $this->User->id = $id;
        $this->set('cpage', 'employee');
        if ($this->request->is('put') || $this->request->is('post')) {

            //check and upload images
            $this->Img = $this->Components->load('Img');
            if ($this->request->data['User']['upload']['name']) {
                $image_information = pathinfo(str_replace(' ', '_', $this->request->data['User']['upload']['name']));
                $newName = $image_information['filename'] . '_' . time();
                $ext = $this->Img->ext($this->request->data['User']['upload']['name']);
                $origFile = $newName . '.' . $ext;
                $targetdir = WWW_ROOT . 'img/users/original';
                $upload = $this->Img->upload($this->request->data['User']['upload']['tmp_name'], $targetdir, $origFile);
                if ($upload == 'Success') {
                    $this->Img->resampleGD($targetdir . DS . $origFile, WWW_ROOT . 'img/users/large/', $origFile, 800, 800, 1, 0);
                    $this->Img->resampleGD($targetdir . DS . $origFile, WWW_ROOT . 'img/users/medium/', $origFile, 288, 155, 1, 0);
                    $this->Img->resampleGD($targetdir . DS . $origFile, WWW_ROOT . 'img/users/small/', $origFile, 180, 180, 1, 0);
                    $this->request->data['User']['photo'] = $origFile;
                } else {
                    $this->request->data['User']['photo'] = '';
                }
            }
            //end

            $this->request->data['User']['joined_on'] = date('Y-m-d', strtotime($this->data['User']['joined_on']));
            $this->request->data['User']['date_of_birth'] = date('Y-m-d', strtotime($this->data['User']['date_of_birth']));

            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash('Employee Updated Successfully', 'flash_success');
                return $this->redirect('/admin/users/edit/' . $id);
            } else {
                $this->Session->setFlash('Failed to Update Employee', 'flash_error');
            }
        } else {
            $this->data = $this->User->find('first', array('conditions' => array('User.id' => $id)));

            $countries = $this->requestAction('countries/get_all_countries');
            $this->set(compact('countries'));

            $states = $this->requestAction('states/get_states/1');
            $this->set(compact('states'));
        }
    }

///////////////////////////////////////////////////////////////////////////////
//Not Required
    /* 	
      public function admin_employee_delete($id){
      $this->set('cpage','employee');
      if ($this->User->delete($id)){
      $this->Session->setFlash('User deleted', 'flash_success');
      return $this->redirect('/admin/users/employee');
      }
      }
     */
///////////////////////////////////////////////////////////////////////////////

    public function logout() {
        $role = $this->Session->read('User.role');
        $this->Session->destroy();
        $this->Session->setFlash('You were Logged out SuccessFully', 'flash_success');
        if ($role == 'admin') {
            return $this->redirect('/admin');
        } else {
            return $this->redirect('/');
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function profile($id = null) {
        $this->layout = "user-inner";
        $this->set('cpage', '');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->User->id = $this->Session->read('User.id');

            //check and upload images
            $this->Img = $this->Components->load('Img');
            if ($this->request->data['User']['upload']['name']) {
                $image_information = pathinfo(str_replace(' ', '_', $this->request->data['User']['upload']['name']));
                $newName = $image_information['filename'] . '_' . time();
                $ext = $this->Img->ext($this->request->data['User']['upload']['name']);
                $origFile = $newName . '.' . $ext;
                $targetdir = WWW_ROOT . 'img/users/original';
                $upload = $this->Img->upload($this->request->data['User']['upload']['tmp_name'], $targetdir, $origFile);
                if ($upload == 'Success') {
                    $this->Img->resampleGD($targetdir . DS . $origFile, WWW_ROOT . 'img/users/large/', $origFile, 800, 800, 1, 0);
                    $this->Img->resampleGD($targetdir . DS . $origFile, WWW_ROOT . 'img/users/medium/', $origFile, 288, 155, 1, 0);
                    $this->Img->resampleGD($targetdir . DS . $origFile, WWW_ROOT . 'img/users/small/', $origFile, 180, 180, 1, 0);
                    $this->request->data['User']['photo'] = $origFile;
                } else {
                    $this->request->data['User']['photo'] = '';
                }
            }
            //end

            $this->request->data['User']['date_of_birth'] = date('Y-m-d', strtotime($this->data['User']['date_of_birth']));
            if ($this->User->save($this->data)) {
                $this->Session->setFlash('Your Profile Updated Successfully', 'flash_success');
                $this->redirect('/users/profile');
            } else {
                $this->Session->setFlash('Failed to Update your profile', 'flash_error');
            }
        } else {
            $this->data = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));

            $countries = $this->requestAction('countries/get_all_countries');
            $this->set(compact('countries'));

            $states = $this->requestAction('states/get_states/1');
            $this->set(compact('states'));
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_profile($id = null) {
        $this->layout = "admin-inner";
        $this->set('cpage', '');
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->User->id = $this->Session->read('User.id');

            if ($this->data['User']['password'] == null || $this->data['User']['password'] == "") {
                unset($this->request->data['User']['password']);
            } else {
                $this->request->data['User']['password'] = md5($this->data['User']['password']);
            }

            if ($this->User->save($this->data)) {
                $this->Session->setFlash('Your Profile Updated Successfully', 'flash_success');
                return $this->redirect('/admin/users/profile');
            } else {
                $this->Session->setFlash('Failed to Update your profile', 'flash_error');
            }
        } else {
            $this->data = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function image_name($string) {
        $img_name = "";
        $pos = 0;

        $pos = strpos($string, ".");
        $sub = substr($string, $pos);
        $img_name = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace('', '', date('Ymd H:i:s')));
        $image = $img_name . $sub;
        return $image;
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_user($id = null) {
        return $this->User->find('first', array('conditions' => array('User.id' => $id)));
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_all_users() {
        return $this->User->find('all', array('conditions' => array('User.role' => 'user', 'User.active' => 1), 'order' => array('User.employee_name' => 'ASC')));
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_last_added_user() {
        return $this->User->find('first', array('conditions' => array('User.role' => 'user'), 'order' => array('User.id' => 'DESC')));
    }

///////////////////////////////////////////////////////////////////////////////

    public function get_next_employee_id($emp_id = NULL) {
        $employee_id = intval($emp_id) + 1;
        if (strlen(strval($employee_id)) == 1) {
            $employ_id = "00" . strval($employee_id);
        } elseif (strlen(strval($employee_id)) == 2) {
            $employ_id = "0" . strval($employee_id);
        } else {
            $employ_id = strval($employee_id);
        }

        return $employ_id;
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_view($id = NULL) {
        $this->layout = "admin-inner";
        $this->set('cpage', 'employee');
        $this->data = $this->User->find('first', array('conditions' => array('User.id' => $id)));

        $countries = $this->requestAction('countries/get_all_countries');
        $this->set(compact('countries'));

        $states = $this->requestAction('states/get_states/1');
        $this->set(compact('states'));

        $cities = $this->requestAction('cities/get_cities_list/' . $this->data['User']['state_id']);
        $this->set(compact('cities'));
    }

///////////////////////////////////////////////////////////////////////////////

    public function password() {
        $this->set('cpage', '');
        $this->layout = 'user-inner';
        if ($this->request->is('post') || $this->request->is('put')) {
            $user = $this->User->find('first', array('conditions' => array('User.id' => $this->Session->read('User.id'))));
            $org_pass = $user['User']['password'];
            if ($org_pass == md5($this->data['User']['old_password'])) {
                if ($this->data['User']['new_password'] == $this->data['User']['con_password']) {
                    $this->User->id = $this->Session->read('User.id');
                    $this->request->data['User']['password'] = md5($this->data['User']['new_password']);
                    if ($this->User->save($this->request->data)) {
                        echo $this->Session->setFlash("Password Updated Successfully", 'flash_success');
                        return $this->redirect('/users/password');
                    } else {
                        $this->Session->setFlash("Failed to save password", 'flash_error');
                    }
                } else {
                    $this->Session->setFlash("New Password and Confirm Password doesn't match", 'flash_error');
                }
            } else {
                $this->Session->setFlash('Wrong Old Password', 'flash_error');
            }
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function user_account_activation_mail($id = NULL, $password = NULL) {
        $user = $this->User->read(null, $id);

        $this->Email->to = $user['User']['email'];
        $this->Email->subject = 'Welcome to Ark Portal';
        $this->Email->replyTo = $this->Session->read('User.email');
        $this->Email->from = $this->Session->read('User.email');
        $this->Email->template = 'accountactivation';
        $this->Email->sendAs = 'html';
        $this->set('password', $password);
        $this->set('user', $this->requestAction('users/get_user', array('pass' => array('User.id' => $id))));
        $this->Email->send();
    }

////////////////////////////////////////////////////////////////	

    public function randomPassword() {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789!@#%^&*";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

////////////////////////////////////////////////////////////////	

    public function forgot_password() {
        $this->layout = 'login';

        if ($this->request->is('post')) {
            $user = $this->User->find('first', array('conditions' => array('User.email' => $this->data['User']['email'])));

            if ($user) {
                $this->User->id = $user['User']['id'];
                $new_password = $this->randomPassword();

                $this->request->data['User']['password'] = md5($new_password);

                if ($this->User->save($this->data)) {
                    $this->forgot_password_mail($user['User']['id'], $new_password);
                    $this->Session->setFlash("Your new password has been sent , please check your email", 'flash_success');
                    return $this->redirect('/');
                } else {
                    $this->Session->setFlash('Failed to to send your password , please try again', 'flash_error');
                }
            } else {
                $this->Session->setFlash("Email Id doesn't exists", 'flash_error');
            }
        }
    }

///////////////////////////////////////////////////////////////////////////////

    public function forgot_password_mail($id = NULL, $password = NULL) {
        $user = $this->User->read(null, $id);

        $this->Email->to = $user['User']['email'];
        $this->Email->subject = ' ark infotec : forgot password ';
        $this->Email->replyTo = 'admin@arkinfotec.com';
        $this->Email->from = 'admin@arkinfotec.com';
        $this->Email->template = 'forgotpassword';
        $this->Email->sendAs = 'html';
        $this->set('password', $password);
        $this->set('user', $user);
        $this->Email->send();
    }

///////////////////////////////////////////////////////////////////////////////

    public function test_mail() {
        $this->Email->to = 'prakash.paramanandam@arkinfotec.com';
        $this->Email->subject = ' test mail';
        $this->Email->replyTo = 'prakash.paramanandam@arkinfotec.com';
        $this->Email->from = 'prakash.paramanandam@arkinfotec.com';
        $this->Email->template = 'test_template';
        $this->Email->sendAs = 'html';
        $this->Email->send();
        exit;
    }

///////////////////////////////////////////////////////////////////////////////

    public function test_doc() {
        require_once 'PhpWord/PhpWord/Autoloader.php';
        require_once 'PhpWord/PhpWord/PhpWord.php';
        require_once 'PhpWord/PhpWord/Settings.php';
        require_once 'PhpWord/PhpWord/Template.php';
        require_once 'PhpWord/PhpWord/DocumentProperties.php';
        require_once 'PhpWord/PhpWord/Collection/Titles.php';

        Autoloader::register();
        $phpWord = new PhpWord();

        $document = $phpWord->loadTemplate('PhpWord/resources/MOM.docx');

        // Variables on different parts of document
        $document->setValue('weekday', date('F d, Y')); // On header
        $document->setValue('time', date('H:i:s')); // On footer
        // On content
        $document->setValue('m_loc', 'test');
        $document->setValue('user', 'prakash');
        $document->setValue('building', 'building');
        $document->setValue('web_address', 'web_address');
        $document->setValue('sche_start', 'sche_start');
        $document->setValue('sche_actu_start', 'sche_actu_start');
        $document->setValue('m_scribe', 'm_scribe');
        $document->setValue('agenda', 'agenda');
        $document->setValue('m_sche_end', '03:05 pm');
        $document->setValue('m_actu_end', '04:05 pm');
        $document->setValue('action', 'action');
        $document->setValue('next_meet', 'next_meet');

        $name = 'Sample_07_TemplateCloneRow.doc';
        $document->saveAs($name);
        rename($name, "PhpWord/results/{$name}");

        exit;
    }

///////////////////////////////////////////////////////////////////////////////

    public function admin_timings() {
        $this->layout = "admin-inner";
        $this->set('cpage', 'timings');
        $this->set('users', $this->User->find('all', array('conditions' => array('User.role' => 'user', 'User.active' => '1'))));
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function admin_change_timings() {
        $timings = array();
        foreach ($this->request->data('Users') as $key => $user) {
            if (is_array($user)) {
                if (isset($user['meridian'])) {
                    $timings[$key] = date('H:i:s', strtotime($user['hours'] . ':' . $user['minutes'] . ':00 ' . $user['meridian']));
                } else {
                    $timings[$key] = date('H:i:s', strtotime($user['hours'] . ':' . $user['minutes'] . ':00'));
                }
            }
        }
        $update_users = explode(',', $this->request->data('Users.ids'));

        $save = array();
        foreach ($update_users as $update_user_id) {
            $save['User']['id'] = $update_user_id;
            $save['User']['timings'] = json_encode($timings);
            $this->User->save($save);
        }
        $this->Session->setFlash("Timings changes successfully", 'flash_success');
        return $this->redirect('timings');
    }

///////////////////////////////////////////////////////////////////////////////

    public function app_test() {
        
    }

}

?>