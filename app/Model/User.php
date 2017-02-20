<?php

class User extends AppModel {

    public $name = 'User';
    public $validate = array('email' => array('login' => array('rule' => 'isUnique', 'message' => 'Email Already Exists')));

    public function getActiveUserList($field1 = 'User.id', $field2 = 'User.employee_name') {
        return $this->find('list', array('conditions' => array('User.id !=' => $_SESSION['User']['id'], 'User.role' => 'user', 'User.active' => '1'), 'order' => array('User.employee_name ASC'), 'fields' => array($field1, $field2)));
    }

    public function getUserByEmpId($id) {
        $id = str_pad($id, 3, '0', STR_PAD_LEFT);
        return $this->find('first', array('conditions' => array('employee_id' => $id)));
    }

}
