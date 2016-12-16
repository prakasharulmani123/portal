<?php
class User extends AppModel {
	public $name = 'User';

    public $validate = array('email' => array('login' => array('rule' => 'isUnique','message' => 'Email Already Exists')));
}
