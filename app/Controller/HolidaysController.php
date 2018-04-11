<?php

class HolidaysController extends AppController {

    public $name = 'Holidays';

/////////////////////////////////////////////////////////////////////////	

    public function beforeFilter() {
        $this->set('cpage', 'holiday');
        parent::beforefilter();
        $this->__validateLoginStatus();
    }

/////////////////////////////////////////////////////////////////////////	

    public function admin_index() {
        $this->layout = 'admin-inner';
        $holidays = $this->Holiday->find('all', array('order' => array('Holiday.date ASC')));
        $this->set(compact('holidays'));
    }

/////////////////////////////////////////////////////////////////////////	

    public function admin_add() {
        $this->layout = "admin-inner";
        if ($this->request->is('put') || $this->request->is('post')) {
            $this->request->data['Holiday']['date'] = date('Y-m-d', strtotime($this->data['Holiday']['date']));
            if ($this->Holiday->save($this->request->data)) {
                $this->loadModel('PendingReport');
                $pending_reports = $this->PendingReport->find('all', array('conditions' => array('PendingReport.date' => '2018-04-07')));
                foreach ($pending_reports as $pending_report) {
                  $this->PendingReport->delete($pending_report['PendingReport']['id']);
                }
                echo $this->Session->setFlash('Holiday Added', 'flash_success');
            } else {
                echo $this->Session->setFlash('Failed to Add Holiday', 'flash_error');
            }
        }
    }

/////////////////////////////////////////////////////////////////////////	

    public function admin_edit($id = NULL) {
        $this->layout = "admin-inner";
        if ($this->request->is('put') || $this->request->is('post')) {
            $this->Holiday->id = $id;
            $this->request->data['Holiday']['date'] = date('Y-m-d', strtotime($this->data['Holiday']['date']));
            if ($this->Holiday->save($this->request->data)) {
                echo $this->Session->setFlash('Holiday Updated', 'flash_success');
            } else {
                echo $this->Session->setFlash('Failed to Holiday', 'flash_error');
            }
        } else {
            $this->data = $this->Holiday->find('first', array('conditions' => array('Holiday.id' => $id)));
        }
    }

/////////////////////////////////////////////////////////////////////////	

    public function admin_holiday_delete($id = NULL) {
        $this->layout = "admin-inner";
        if ($this->Holiday->delete($id)) {
            $this->Session->setFlash('Holiday Deleted Successfully', 'flash_success');
            return $this->redirect('/admin/holidays');
        } else {
            $this->Session->setFlash('Failed to Delete Holiday', 'flash_error');
        }
    }

/////////////////////////////////////////////////////////////////////////	

    public function get_holidays_per_month($month = NULL, $year = NULL) {
        return $this->Holiday->find('all', array('fields' => array('Holiday.name', 'Holiday.date'), 'conditions' => array('MONTH(Holiday.date)' => $month, 'YEAR(Holiday.date)' => $year)));
    }

/////////////////////////////////////////////////////////////////////////	

    public function index() {
        $this->layout = 'user-inner';
        $year = $this->request->query('y') ?: date('Y');
        $holidays = $this->Holiday->find('all', array('conditions' => array('YEAR(Holiday.date)' => $year), 'order' => array('Holiday.date ASC')));

        $this->set(compact('holidays', 'year'));
    }

/////////////////////////////////////////////////////////////////////////	

    public function get_holiday_per_month() {
        return $this->Holiday->find('all', array('conditions' => array('MONTH(Holiday.date)' => date('m'), 'YEAR(Holiday.date)' => date('Y'))));
    }

}

?>