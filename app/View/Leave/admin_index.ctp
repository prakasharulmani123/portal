<script type="text/javascript">
    $(function () {
        $("#LeaveFromDate").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true   
        });
        $("#LeaveToDate").datepicker({
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

    jQuery(document).ready(function () {
        $(".b_popup_4").dialog({
            autoOpen: false,
            modal: true,
            width: 400,
            buttons: {
                "Ok": function () {
                    var id = $("#leave-id").val();
                    var status = $("#leave-status").val();
                    var remarks = $("#leave-remarks").val();
                    var user_id = $("#user-id").val();

                    $.ajax({
                        url: BaseURL + "/admin/leave/add_remarks",
                        type: "POST",
                        dataType: "JSON",
                        data: {'id': id, 'status': status, 'remarks': remarks, 'user_id': user_id},
                        success: function (msg) {
                            $("#button_" + id).attr("class", msg.class);
                            $("#span_" + id).html(msg.status);
                            $("#remarks_" + id).html(msg.remarks);
                        }
                    });
                    $("#leave-id").val("");
                    $("#leave-status").val("");
                    $("#leave-remarks").val("");

                    $(this).dialog("close");
                },
                Cancel: function () {
                    $(this).dialog("close");
                    $("#leave-id").val("");
                    $("#leave-status").val("");
                    $("#leave-remarks").val("");
                }
            }
        });

        /*	
         $(".dropdown-menu a").click(function(){
         var id = $(this).attr('leave-id');
         var status = $(this).attr('status');
         var user_id = $(this).attr('user-id');
         
         if(status == 2 || status == 1){
         $(".b_popup_4").dialog('open');
         $('#leave-id').val($(this).attr("leave-id"));
         $('#leave-status').val($(this).attr("status"));
         $('#user-id').val($(this).attr("user-id"));
         
         return false
         }
         });
         */
    });

    function leave_sent(id, sts, user_id, leave_id) {
        var status = $('#a-leave-' + sts + '-' + id).attr('status');
        if (status == 2 || status == 1) {
            $(".b_popup_4").dialog('open');
            $('#leave-id').val(leave_id);
            $('#leave-status').val(status);
            $('#user-id').val(user_id);
        }
    }
</script>

<div class="dialog b_popup_4" style="display: none;" title="Remarks">                                
    <div class="block"> 
        <input type="hidden" value="" id="leave-id" name="data[Leave][id]"/>
        <input type="hidden" value="" id="leave-status" name="data[Leave][approved]"/>
        <input type="hidden" value="" id="user-id" name="data[Leave][user_id]"/>
        <span>Remarks:</span>
        <p>
            <textarea placeholder="Remarks..." name="data[Leave][remarks]" id="leave-remarks"></textarea>
        </p>
        <div class="dr"><span></span></div>
    </div>
</div>                                        

<div style="margin:10px 50px 10px 23px;">
    <?php echo $this->Form->create('Leave'); ?>
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
    <?php echo $this->Form->input('from_date', array('label' => false, 'autocomplete' => 'off','div' => false, 'class' => 'form-control', 'value' => $from_date, 'style' => 'width:100px; margin-top:6px;')); ?>
    <b><?php echo " To : " ?></b>
    <?php echo $this->Form->input('to_date', array('label' => false, 'autocomplete' => 'off','type' => 'text', 'div' => false, 'class' => 'form-control', 'value' => $to_date, 'style' => 'width:100px; margin-top:6px;')); ?>
    <b><?php echo " Status : " ?></b>
    <?php echo $this->Form->input('approved', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array('0' => 'Pending', '1' => 'Approved', '2' => 'Declined'), 'selected' => $all['approved'], 'style' => 'width:100px; margin-top:6px;')); ?>
    <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?>
    <?php echo $this->Html->link('Reset', array('controller' => 'leave', 'action' => 'reset', 'admin' => true), array('class' => 'btn btn-danger')); ?>
    <?php echo $this->Form->end(); ?>
    <div class="clear"></div>
</div>

<div class="workplace">
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-target"></div>
                <h1>Leave Requests</h1>
                <div class="clear"></div>
            </div>
            <div class="block-fluid table-sorting">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="leave-index">
                    <thead>
                        <tr>
                            <th width="6%">No</th>
                            <th width="15%">Name</th>
                            <th width="15%">Requisition</th>
                            <th width="10%">Date</th>
                            <th width="10%">Days</th>
                            <th width="10%">Compensation Days</th>
                            <th width="10%">Reason</th>
                            <th width="10%">Status</th>
                            <th width="20%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($leaves as $leave):

                            $user = $this->requestAction('users/get_user', array('pass' => array('User.id' => $leave['Leave']['user_id'])));
                            ?>

                            <tr>
                                <td><?php echo h($i); ?></td>
                                <td><?php echo h($user['User']['employee_name']); ?></td>
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
                                    $comp_leave = $leave['Leave']['compensation_id'];
                                    $string = unserialize($comp_leave);
                                    $tdays = "";
                                    $days = $leave['Leave']['days'];
                                    if (is_array($string) || is_object($string)) {
                                        foreach ($string as $key => $value) {
                                            $tdays = 0;
                                            $blogs = $this->requestAction('Compensations/get_id', array('pass' => array('Compensation.id' => $value)));
                                            $cdays = $blogs['Compensation']['days'];
                                            $tdays = $days + $cdays;
                                            $days = $tdays;
                                        }
                                    }
                                    switch ($days) {
                                        case 0:
                                            echo '_';
                                            break;
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
                                <?php
                                $records = array();
                                $days = 0;
                                if (is_array($string) || is_object($string)) {
                                    foreach ($string as $key => $value) {
                                        $tdays = 0;
                                        $blogs = $this->requestAction('Compensations/get_id', array('pass' => array('Compensation.id' => $value)));
                                        $cdays = $blogs['Compensation']['days'];
                                        $records[] = date('d-m-Y', strtotime($blogs['Compensation']['date']));
                                        $tdays = $days + $cdays;
                                        $days = $tdays;
                                    }
                                }
                                $imp_rec = implode(" & ", (array) $records);
                                ?>
                                <td><?php echo h($imp_rec) ?> <?php echo h(($tdays > 0) ? '(' . $tdays . ' days)' : "-") ?></td>
                                <td><?php echo h($leave['Leave']['reason']) ?></td>
                                <td>
                                    <?php
                                    $status = $leave['Leave']['approved'];
                                    $button = 'btn-danger';
                                    $value = 'Pending';
                                    if ($status == 1) {
                                        $button = 'btn-success';
                                        $value = 'Approved';
                                    }
                                    if ($status == 2) {
                                        $button = 'btn-inverse';
                                        $value = 'Declined';
                                    }
                                    ?>
                                    <div class="btn-group li_check_access"> 
                                        <!--<div class="li_check_access">-->
                                        <button data-toggle="dropdown" class="btn btn-mini <?php echo $button; ?> dropdown-toggle" id="button_<?php echo $leave['Leave']['id'] ?>">
                                            <span id="span_<?php echo $leave['Leave']['id'] ?>"><?php echo $value; ?></span> <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                        <!--                    <li><a href="#" user-id="<?php echo $leave['Leave']['user_id'] ?>" leave-id="<?php echo $leave['Leave']['id'] ?>" status="0">Pending</a></li>
                                            -->                    <li><a class="check-access" id="a-leave-approve-<?php echo $leave['Leave']['id'] ?>" data-href="/admin/leave/add_remarks"  href="javascript:leave_sent(<?php echo $leave['Leave']['id'] ?>, 'approve', <?php echo $leave['Leave']['user_id'] ?>,<?php echo $leave['Leave']['id'] ?>)" status="1">Approved</a></li>
                                            <li><a class="check-access" id="a-leave-decline-<?php echo $leave['Leave']['id'] ?>" data-href="/admin/leave/add_remarks"  href="javascript:leave_sent(<?php echo $leave['Leave']['id'] ?>, 'decline', <?php echo $leave['Leave']['user_id'] ?>,<?php echo $leave['Leave']['id'] ?>)" status="2">Declined</a></li>
                                        </ul>
                                        <!--</div>-->
                                    </div>
                                </td>	  
                                <td><span id="remarks_<?php echo $leave['Leave']['id'] ?>"><?php echo h($leave['Leave']['remarks']) ?></span></td>

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