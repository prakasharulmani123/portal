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

    public function admin_index() {
        $this->layout = 'admin-inner';
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
