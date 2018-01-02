<style>/*
.ui-datepicker-calendar {
    display: none;
    }*/
</style>

<script type="text/javascript">

    $(document).ready(function () {
        $("#leave-index").dataTable({
            "iDisplayLength": 10,
            "sPaginationType": "full_numbers",
            "bLengthChange": true,
            "bFilter": false,
            "bInfo": true,
            "bPaginate": true
        });

<?php
if (empty($all['month'])) {
    $year = date('Y');
    $month = date('m') - 1;
} else {
    $year = $all['year'];
    $month = $all['month'] - 1;
}
?>

        $('#LeaveMonth').datepicker({
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'MM yy',
            defaultDate: new Date(<?php echo $year ?>, <?php echo $month ?>, 1),
            onClose: function (dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(year, month, 1));
                $(".ui-datepicker-calendar").show();
            }
        });

        $("#LeaveMonth").focus(function () {
            $(".ui-datepicker-calendar").hide();
        });
    });
</script>

<div style="margin:10px 50px 10px 50px;">
    <?php echo $this->Form->create('Leave'); ?>
    <?php
    $all_user = array();

    foreach ($users as $user) {
        $all_user[$user['User']['id']] = $user['User']['employee_name'];
    }

    if (empty($all['month'])) {
        $month = date('d-m-Y');
//			$value = date('F Y');
        $value = '';
    } else {
        $month = '01-' . $all['month'] . '-' . $all['year'];
        $value = date('F Y', strtotime($month));
    }
    ?>
    <b><?php echo " Employee : " ?></b>
    <?php echo $this->Form->input('user_id', array('label' => false, 'div' => false, 'class' => 'form-control', 'options' => array('all' => 'All', '' => $all_user), 'selected' => $all['user_id'], 'style' => 'width:200px; margin-top:6px;')); ?>
    <b><?php echo " Month : " ?></b>
    <?php echo $this->Form->input('month', array('label' => false, 'div' => false, 'class' => 'form-control', 'value' => $value, 'style' => 'width:200px; margin-top:6px;')); ?>
    <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?></td>
<?php echo $this->Html->link('Reset', array('controller' => 'leave', 'action' => 'reset_monthly_leave_report', 'admin' => true), array('class' => 'btn btn-danger')); ?>
<?php echo $this->Form->end(); ?>
<div class="clear"></div>
</div>

<?php
if ($all['user_id'] == 'all') {
    ?>
    <div class="workplace">

        <div class="block-fluid table-sorting">
            <table border="1" width="100%">
                <thead style=" width:70; color: white;font-size:16px;  background-color:#486B91;">
                <th width="20%">Employee Name</th>
                <th width="50%">Details</th>
                <th width="10%">Action</th>
                </thead>
                <tbody>
                    <?php foreach ($all_user as $key => $all_use) { ?>
                        <tr>
                            <td><span style="font-size:14px;line-height: 14px; padding:45px"><?php echo $all_use; ?></span></td>


                            <td>
                                <?php
                                $casual_leave_per_month = $this->requestAction('leave/get_all_leave_count_per_month_per_status/' . $key . '/' . date('m', strtotime($month)) . '/' . date('Y', strtotime($month)) . '/' . 'C');
                                $paid_leave_per_month = $this->requestAction('leave/get_all_leave_count_per_month_per_status/' . $key . '/' . date('m', strtotime($month)) . '/' . date('Y', strtotime($month)) . '/' . 'P');

                                $leave_count = $this->requestAction('leave/get_all_leave_count/' . $key);

                                $casual_leave_per_year = $this->requestAction('leave/user_get_all_leave_count_by_user_id_and_status/' . $key . '/C/' . $year);
                                $sel_user = $this->requestAction('users/get_user/' . $key);
                                $user_casual_leave = $sel_user['User']['casual_leave'];
                                ?>

                                <div class="workplace">
                                    <div class="row-fluid editcss">
                                        <div class="wBlock auto space">
                                            <div class="dSpace">
                                                <h3>Casual Leave Days <br /><?php echo date('F', strtotime($month)) ?></h3>
                                                <span class="number"><?php echo $casual_leave_per_month; ?></span>                                                
                                            </div>
                                        </div>

                                        <div class="wBlock red auto space">
                                            <div class="dSpace">
                                                <h3>Loss of Pay(LOP) Leave Days <br /><?php echo date('F', strtotime($month)) ?></h3>
                                                <span class="number"><?php echo $paid_leave_per_month ?></span>                                                  
                                            </div>
                                        </div>                    

                                        <div class="wBlock green auto space">
                                            <div class="dSpace">
                                                <h3>Current <br />Leave Days</h3>
                                                <span class="number"><?php echo $leave_count; ?></span>                                                  
                                            </div>
                                        </div>

                                        <div class="wBlock blue auto space">
                                            <div class="dSpace">
                                                <h3>Remaining <br />Casual Days</h3>
                                                <span class="number"><?php echo ($user_casual_leave - $casual_leave_per_year) <= 0 ? 0 : $user_casual_leave - $casual_leave_per_year; ?></span>                                                  
                                            </div>
                                        </div>
                                    </div>

                            </td>
                            <td align="center">
                                <?php echo $this->Form->create('Leave'); ?>
                                <?php echo $this->Form->hidden('user_id', array('value' => $key)); ?>
                                <?php echo $this->Form->hidden('month', array('value' => $value)); ?>
                                <?php echo $this->Form->button('View', array('class' => 'btn btn-default')); ?></td>
                            <?php echo $this->Form->end(); ?></td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>   
    </div>                                        

    <?php
} else {
    ?>
    <?php
    if (!empty($all['month'])) {
        $casual_leave_per_month = $this->requestAction('leave/get_all_leave_count_per_month_per_status/' . $all['user_id'] . '/' . date('m', strtotime($month)) . '/' . date('Y', strtotime($month)) . '/' . 'C');

        $paid_leave_per_month = $this->requestAction('leave/get_all_leave_count_per_month_per_status/' . $all['user_id'] . '/' . date('m', strtotime($month)) . '/' . date('Y', strtotime($month)) . '/' . 'P');

        $leave_count = $this->requestAction('leave/get_all_leave_count/' . $all['user_id']);

        $casual_leave_per_year = $this->requestAction('leave/user_get_all_leave_count_by_user_id_and_status/' . $all['user_id'] . '/C/' . $year);

        $sel_user = $this->requestAction('users/get_user/' . $all['user_id']);
        $user_casual_leave = $sel_user['User']['casual_leave'];
        ?>

        <div class="workplace">
            <div class="row-fluid">
                <div class="wBlock auto">
                    <div class="dSpace">
                        <h3>Casual Leave Days <br /><?php echo date('F', strtotime($month)) ?></h3>
                        <span class="number"><?php echo $casual_leave_per_month; ?></span>                                                
                    </div>
                </div>

                <div class="wBlock red auto">
                    <div class="dSpace">
                        <h3>Loss of Pay(LOP) Leave Days <br /><?php echo date('F', strtotime($month)) ?></h3>
                        <span class="number"><?php echo $paid_leave_per_month ?></span>                                                  
                    </div>
                </div>                    

                <div class="wBlock green auto">
                    <div class="dSpace">
                        <h3>Current <br />Leave Days</h3>
                        <span class="number"><?php echo $leave_count; ?></span>                                                  
                    </div>
                </div>

                <div class="wBlock blue auto">
                    <div class="dSpace">
                        <h3>Remaining <br />Casual Days</h3>
                        <span class="number"><?php echo ($user_casual_leave - $casual_leave_per_year) <= 0 ? 0 : $user_casual_leave - $casual_leave_per_year; ?></span>                                                  
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="span12">
                    <div class="head">
                        <div class="isw-calendar"></div>
                        <h1>Monthly Leave Report</h1>
                        <div class="clear"></div>
                    </div>
                    <div class="block-fluid table-sorting">
                        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="leave-index">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Requisition</th>
                                    <th width="10%">Date</th>
                                    <th width="10%">Days</th>
                                    <th width="15%">Reason</th>
                                    <th width="5%">Status</th>
                                    <th width="15%">Remarks</th>
                                    <th width="20%">Casual / Loss of Pay(LOP)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($leaves as $leave):
                                    ?>
                                    <tr>
                                        <td><?php echo h($i); ?></td>
                                        <td>
                                            <?php
                                            if ($leave['Leave']['request'] == 'past') {
                                                echo h('Past Requisition');
                                            } elseif ($leave['Leave']['request'] == 'current') {
                                                echo h('Current Requisition');
                                            }
                                            ?>
                                        </td>
                                        <?php
                                        $leave_date = '';
                                        foreach ($leave['SubLeave'] as $subleave) {
                                            $leave_date .= date('d-m-Y', strtotime($subleave['date'])) . ' & ';
                                        }
                                        $leave_date = rtrim($leave_date, ' & ');
                                        ?>
                                        <td><?php echo h($leave_date) ?></td>
                                        <td>
                                            <?php
                                            switch ($leave['Leave']['days']) {
                                                case 0.5:
                                                    echo 'Half a day';
                                                    break;
                                                case 1:
                                                    echo 'One day';
                                                    break;
                                                case 1.5:
                                                    echo 'One & Half a days';
                                                    break;
                                                case 2:
                                                    echo 'Two days';
                                                    break;
                                                case 2.5:
                                                    echo 'Two & Half a days';
                                                    break;
                                                case 3:
                                                    echo 'Three days';
                                                    break;
                                            }
                                            ?></td>
                                        <td><?php echo h($leave['Leave']['reason']) ?></td>
                                        <td><p>
                                                <?php if ($leave['Leave']['approved'] == 0) { ?>
                                                    <span class="label label-important">Pending</span>
                                                <?php } elseif ($leave['Leave']['approved'] == 1) {
                                                    ?>
                                                    <span class="label label-success">Approved</span>
                                                    <?php
                                                }
                                                if ($leave['Leave']['approved'] == 2) {
                                                    ?>
                                                    <span class="label label-inverse">Declined</span>
                                                <?php }
                                                ?></p>
                                        </td>
                                        <td><?php echo h($leave['Leave']['remarks']) ?></td>
                                        <td><?php
                                            if ($leave['Leave']['approved'] == 1) {
                                                foreach ($leave['SubLeave'] as $subleave) {
                                                    ?>
                                                    <span id="td_span_<?php echo $subleave['id'] ?>">
                                                        <?php echo date('d-m-Y', strtotime($subleave['date'])) ?> : <?php echo $subleave['status'] == 'C' ? '<b class="text-info">Casual</b>' : '<b class="text-error">Loss of Pay(LOP)</b>'; ?>
                                                    </span><br />
                                                    <?php
                                                }
                                            }
                                            ?></td>
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
        <?php
    }
}
?>