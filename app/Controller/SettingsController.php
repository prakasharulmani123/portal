<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class SettingsController extends AppController {

    public $name = 'Settings';

    public function beforeFilter() {
        $this->set('cpage', 'settings');
        parent::beforefilter();
        $this->__validateLoginStatus();
    }

    public function admin_index($id = null) {
        $this->layout = 'admin-inner';

        if ($this->request->is('ajax')) {
            $insert_birthaday_mail = array(
                'Setting' => array(
                    'id' => $this->data['id'],
                    'key_value' => $this->data['key_value'],
                    'value' => $this->data['description'],
                    'description' => $this->data['description'],
                )
            );
            if ($this->Setting->saveAll($insert_birthaday_mail)) {
                $return['description'] = $this->data['description'];
                echo json_encode($return);
            }
            exit;
        }

        $settings = $this->Setting->find('all');
        $this->set(compact('settings'));
    }

    public function admin_edit($val) {
        $this->Setting->key_value = $val;

        if ($this->request->is('put') || $this->request->is('post')) {
            $this->Setting->saveAll($this->request->data);
            return $this->redirect('/admin/settings');
        }
        $setting = $this->Setting->find('first', ['conditions' => ['Setting.key_value' => $val]]);
        $this->set(compact('setting','user'));
    }

}
