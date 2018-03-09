<?php

class CompensationsController extends AppController
{
    public $name = 'Compensations';
    public $components = array('RequestHandler');

    public function get_id($compensation_id = NULL)
    {
        return $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('Compensation.id' => $compensation_id)));
    }

    public function add_current_month_permission_new()
    {
        return $this->Compensation->find('count', array('conditions' => array('AND' => array('Compensation.user_id=' . $this->Session->read('User.id')), array('Compensation.status' => 0), array('Compensation.type' => 'P'))));
    }

    public function get_permission_id($compensation_id = NULL)
    {
        return $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('Compensation.id' => $compensation_id)));
    }
}