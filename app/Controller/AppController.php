<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

//App::uses('CakeTime', 'Utility');
//CakeTime::convert(time(), new DateTimeZone('Asia/Kolkata'));

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public function admin_switch($field = null, $id = null) {
        $this->autoRender = false;
        $model = $this->modelClass;
        if ($this->$model && $field && $id) {
            $field = $this->$model->escapeField($field);
            $this->$model->updateAll(array($field => '1 -' . $field), array($this->$model->escapeField() => $id));
            if (!$this->RequestHandler->isAjax()) {
                return $this->redirect($this->referer());
            } else {
                return 1;
            }
        }
    }

    public function __validateLoginStatus() {
        if ($this->action != 'database_mysql_dump') {
            if ($this->action != 'admin_login' && $this->action != 'login' && $this->action != 'admin_logout' && $this->action != 'logout') {
                if ($this->Session->check('User')) {
                    if ($this->params['prefix'] == 'admin') {
                        if (!$this->access_rights()) {
                            return $this->render('/Errors/error403');
                        }
                        if ($this->Session->read('User.super_user') == 0 && $this->Session->read('User.role') == 'user') {
                            return $this->redirect('/users');
                        }
                    }

                    if ($this->Session->read('User.role') == 'admin') {
                        $this->layout = "admin-inner";
                    }
                } else {
                    return $this->redirect('/');
                }
            } elseif ($this->action == 'admin_login') {
                if ($this->Session->check('User')) {
                    return $this->redirect('/admin/users');
                }
            } elseif ($this->action == 'login') {
                if ($this->Session->check('User')) {
                    return $this->redirect('/users/dashboard');
                }
            }
        }
    }

    private function access_rights() {
        if ($this->Session->read('User.role') == 'admin') {
            return true;
        } else {
            $this->loadModel('Module');
            if ($this->Session->read('User.super_user') == 1 && $this->Session->read('User.role') == 'user') {
                $demodule = json_decode($this->Session->read('User.access'));
                $request_path = $_SERVER['REQUEST_URI'];
                $exp = explode('/admin', $request_path);
                $path = explode('/', substr($exp[1], 1));
                if ($path == 'users') {
                    return true;
                } else {
//                    $exp = explode('/admin', $request_path);
//                    $path = explode('/', substr($exp[1], 1));
                    $module = $path[0];
                    if (isset($path[1])) {
                        $module .= '/' . $path[1];
                    }
                    $roles = $this->Module->find('first', array('conditions' => array('Module.url' => $module))); //we get the authors from the database       
                    if (is_array($roles) &&  $roles) {
                        return in_array($roles['Module']['id'], $demodule);
                    } else {
                        return false;
                    }
                }
            }
        }
    }

    public function beforeFilter() {
        date_default_timezone_set("Asia/Kolkata");
       
    }

///////////////////////////////////////////////////////////////////////////////

    /**
     * Dumps the MySQL database that this controller's model is attached to.
     * This action will serve the sql file as a download so that the user can save the backup to their local computer.
     *
     * @param string $tables Comma separated list of tables you want to download, or '*' if you want to download them all.
     */
    public function database_mysql_dump($tables = '*') {

        $return = '';

        $modelName = $this->modelClass;

        $dataSource = $this->{$modelName}->getDataSource();
        $databaseName = $dataSource->getSchemaName();


// Do a short header
        $return .= '-- Database: `' . $databaseName . '`' . "\n";
        $return .= '-- Generation time: ' . date('D jS M Y H:i:s') . "\n\n\n";


        if ($tables == '*') {
            $tables = array();
            $result = $this->{$modelName}->query('SHOW TABLES');
            foreach ($result as $resultKey => $resultValue) {
                $tables[] = current($resultValue['TABLE_NAMES']);
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

// Run through all the tables
        foreach ($tables as $table) {
            $tableData = $this->{$modelName}->query('SELECT * FROM ' . $table);

            $return .= 'DROP TABLE IF EXISTS ' . $table . ';';
            $createTableResult = $this->{$modelName}->query('SHOW CREATE TABLE ' . $table);
            $createTableEntry = current(current($createTableResult));
            $return .= "\n\n" . $createTableEntry['Create Table'] . ";\n\n";

// Output the table data
            foreach ($tableData as $tableDataIndex => $tableDataDetails) {

                $return .= 'INSERT INTO ' . $table . ' VALUES(';

                foreach ($tableDataDetails[$table] as $dataKey => $dataValue) {

                    if (is_null($dataValue)) {
                        $escapedDataValue = 'NULL';
                    } else {
// Convert the encoding
                        $escapedDataValue = mb_convert_encoding($dataValue, "UTF-8", "ISO-8859-1");

// Escape any apostrophes using the datasource of the model.
                        $escapedDataValue = $this->{$modelName}->getDataSource()->value($escapedDataValue);
                    }

                    $tableDataDetails[$table][$dataKey] = $escapedDataValue;
                }
                $return .= implode(',', $tableDataDetails[$table]);

                $return .= ");\n";
            }

            $return .= "\n\n\n";
        }

        $ret = array();

        $ret['content'] = $return;
        $ret['database'] = $databaseName;
        return $ret;

// Set the default file name
//		$fileName = $databaseName . '-backup-' . date('Y-m-d_H-i-s') . '.sql';
//		$content = "some text here";
//		$fp = fopen(WWW_ROOT . "files/db_backup/".$fileName,"wb");
//		fwrite($fp,$return);
//		fclose($fp);
//		$this->Email->to = array('prakash.paramanandam@arkinfotec.com');
//		$this->Email->subject = 'DB Backup checking';
//		$this->Email->replyTo = 'admin@arkinfotec.com';
//		$this->Email->from = 'admin@arkinfotec.com';
//		$this->Email->sendAs = 'html'; 
//		$attachments = array();
//		$this->Email->attachments = array(WWW_ROOT.'files/db_backup/'.$fileName);
//		$this->Email->send('Database backup');
// Serve the file as a download
//		$this->autoRender = false;
//		$this->response->type('Content-Type: text/x-sql');
//		$this->response->file('files/db_backup/'.$fileName, array('download' => true, 'name' => 'DB'));
//		$this->response->download($fileName);
//		$this->response->body($return);
    }

}
