<?php

class CompensationsController extends AppController {

    public $name = 'Compensations';
    public $helpers = array('Html', 'Form', 'Js', 'Paginator');
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function admin_index($id = NULL) {
        $this->layout = "admin-inner";
        $this->set('cpage', 'compensation');

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Session->write('Compensations.user_id', '');
            $this->Session->write('Compensations.from_date', '');
            $this->Session->write('Compensations.to_date', '');
            $this->Session->write('Compensations.approved', '');

            if ($this->request->data['Compensations']['user_id'] != '') {
                $this->Session->write('Compensations.user_id', $this->request->data['Compensations']['user_id']);
            }

            if (!empty($this->request->data['Compensations']['from_date']) && !empty($this->request->data['Compensations']['to_date'])) {
                $this->Session->write('Compensations.from_date', date('Y-m-d', strtotime($this->request->data['Compensations']['from_date'])));
                $this->Session->write('Compensations.to_date', date('Y-m-d', strtotime($this->request->data['Compensations']['to_date'])));
            }

            if ($this->request->data['Compensations']['approved'] != '') {
                $this->Session->write('Compensations.approved', $this->request->data['Compensations']['approved']);
            }
            return $this->redirect(array('action' => 'admin_index'));
        }

        if (isset($_GET['approved'])) {
            $this->Session->write('Compensations.approved', $_GET['approved']);
            return $this->redirect(array('action' => 'admin_index'));
        }
        if ($this->Session->check('Compensations')) {
            $all = $this->Session->read('Compensations');

            if (!isset($all['user_id'], $all['from_date']) || $all['user_id'] == '' && $all['from_date'] == '') {
                if ($all['approved'] == '') {
                    $compensations = $this->Compensation->find('all', array('order' => array('Compensation.created DESC')));
                } else {
                    $compensations = $this->Compensation->find('all', array('conditions' => array('Compensation.approved' => $all['approved']), 'order' => array('Compensation.created DESC')));
                }
            } elseif (!isset($all['user_id'], $all['from_date']) || $all['user_id'] != '' && $all['from_date'] == '') {

                if ($all['approved'] == '') {
                    $compensations = $this->Compensation->find('all', array('conditions' => array('Compensation.user_id' => $all['user_id']), 'order' => array('Compensation.created DESC')));
                } else {
                    $compensations = $this->Compensation->find('all', array('conditions' => array('Compensation.user_id' => $all['user_id'], 'Compensations.approved' => $all['approved']), 'order' => array('Compensation.created DESC')));
//                print_r($leaves);exit;
                }
            } elseif (!isset($all['user_id'], $all['from_date']) || $all['user_id'] == '' && $all['from_date'] != '') {

                if ($all['approved'] == '') {
                    $compensations = $this->Compensation->find('all', array('conditions' => array('Compensation.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date'])))), 'order' => array('Compensation.created DESC')));
                } else {
                    $compensations = $this->Compensation->find('all', array('conditions' => array('Compensation.date between ? and ?' => array(date('Y-m-d', strtotime($all['from_date'])), date('Y-m-d', strtotime($all['to_date']))), 'Compensations.approved' => $all['approved']), 'order' => array('Compensation.created DESC')));
                }
            }
        } else {

            $all = array('user_id' => '', 'from_date' => '', 'to_date' => '', 'approved' => '');
            $compensations = $this->Compensation->find('all', array('order' => array('Compensation.created DESC')));
        }

        $this->set('compensations', $this->Compensation->find('all', array('order' => array('Compensation.created DESC'))));
        $this->set(compact('all'));
        $this->set('users', $this->requestAction('users/get_all_users'));
        $this->set(compact('compensations'));
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
     public function admin_reset() {
        $this->Session->delete('Compensations');
        return $this->redirect(array('action' => 'admin_index'));
    }

    public function get_id($compensation_id = NULL) {
        return $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('Compensation.id' => $compensation_id)));
    }

    public function add_current_month_permission_new() {
        return $this->Compensation->find('count', array('conditions' => array('AND' => array('Compensation.user_id=' . $this->Session->read('User.id')), array('Compensation.status' => 0), array('Compensation.type' => 'P'))));
//pr($return);exit;
    }

    public function get_permission_id($compensation_id = NULL) {
        return $this->Compensation->find('first', array('recursive' => -1, 'conditions' => array('Compensation.id' => $compensation_id)));
    }

}

?>