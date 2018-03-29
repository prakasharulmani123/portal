<script type="text/javascript">
    $(function () {
        $("#CompensationsFromDate").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
        $("#CompensationsToDate").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });

    $(document).ready(function () {
        $("#leave-index").dataTable({
            "iDisplayLength": 10,
            "sPaginationType": "full_numbers",
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bPaginate": true});
    });

    

    
</script>
 
<div style="margin:10px 50px 10px 23px;">
    <?php echo $this->Form->create('Compensations'); ?>
    <?php
    $all_user = array();

    foreach ($users as $user) {
        $all_user[$user['User']['id']] = $user['User']['employee_name'];
    }
    if (isset($all['to_date'], $all['from_date'])) {
        $from_date = $all['from_date'];
        $to_date = $all['to_date'];
    }
    if (empty($from_date)) {
        $from_date = "";
    } else {
        $from_date = date('d-m-Y', strtotime($from_date));
    }

    if (empty($to_date)) {
        $to_date = "";
    } else {
        $to_date = date('d-m-Y', strtotime($to_date));
    }
    ?>
    <?php $name = isset($all['user_id']) ? $all['user_id'] : null; ?>
    <b><?php echo " Employee : " ?></b>
    <?php echo $this->Form->input('user_id', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array($all_user), 'selected' => $name, 'style' => 'width:140px; margin-top:6px;')); ?>
    <b><?php echo " From : " ?></b>
    <?php echo $this->Form->input('from_date', array('label' => false, 'div' => false, 'class' => 'form-control', 'value' => $from_date, 'style' => 'width:100px; margin-top:6px;')); ?>
    <b><?php echo " To : " ?></b>
    <?php echo $this->Form->input('to_date', array('label' => false, 'type' => 'text', 'div' => false, 'class' => 'form-control', 'value' => $to_date, 'style' => 'width:100px; margin-top:6px;')); ?>
    <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?>
    <?php echo $this->Html->link('Reset', array('controller' => 'compensations', 'action' => 'reset', 'admin' => true), array('class' => 'btn btn-danger')); ?>
    <?php echo $this->Form->end(); ?>
    <div class="clear"></div>
</div>

<div class="workplace">
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-target"></div>
                <h1>Compensation Leave Requests</h1>
                <div class="clear"></div>
            </div>
            <div class="block-fluid table-sorting">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="leave-index">
                    <thead>
                        <tr>
                            <th width="6%">No</th>
                            <th width="15%">Name</th>
                            <th width="15%">Date</th>
                            <th width="10%">Days</th>
                            <th width="10%">Comments</th>
                            <th width="10%">Status</th>
                            <th width="10%">Type</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($compensations as $compensation):

                            $user = $this->requestAction('users/get_user', array('pass' => array('User.id' => $compensation['Compensation']['user_id'])));
                            ?>

                        <tr>
                            <td><?php echo h($i); ?></td>
                            <td><?php echo h($user['User']['employee_name']); ?></td>
                            <td><?php echo h($compensation['Compensation']['date']); ?></td>
                            <td><?php echo h($compensation['Compensation']['days']); ?></td>
                            <td><?php echo h($compensation['Compensation']['comments']); ?></td>
                            <td>
                                    <?php
                                    $status = $compensation['Compensation']['status'];
                                   
                                   
                                    if ($status == 0) {
                                      ?>  
                                <span class="label label-success"<?php echo $compensation['Compensation']['id'] ?>><?php echo 'Not Used' ?></span>
                              <?php
                                }
                                else{ ?>
                               <span class="label label-success" <?php echo $compensation['Compensation']['id'] ?>><?php echo 'Compensated' ?></span>
                               <?php 
                               }
                                ?>


                                
                            </td>	 

                            <td><?php
                                    $type = $compensation['Compensation']['type'];
                                   
                                   
                                    if ($type == 'L') {
                                       ?>
                                 <span class="label label-success" <?php echo $compensation['Compensation']['id'] ?>><?php echo 'Leave'; ?></span>
                                        
                                  <?php  
                                  
                                    }
                                    elseif ($type == 'P'){ ?>
                                        
                                    
                                 <span class="label label-success"  <?php echo $compensation['Compensation']['id'] ?>><?php echo 'Permission'; ?></span>
                                    <?php } ?>
                            </td>


                        </tr>
                            <?php
                            $i++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="dr"><span></span></div>
</div>