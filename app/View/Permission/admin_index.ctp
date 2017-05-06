<script type="text/javascript">
    <?php if (!$this->request->is('ajax')) { ?>
    $(function () {
        $("#PermissionFromDate").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
        $("#PermissionToDate").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    });
    <?php } ?>

    $(document).ready(function () {
//        $("#leave-index").dataTable({
//            "iDisplayLength": 10,
//            "sPaginationType": "full_numbers",
//            "bLengthChange": true,
//            "bFilter": true,
//            "bInfo": true,
//            "bPaginate": true});
    });

    jQuery(document).ready(function () {
        $('input[type="checkbox"].checkrow').change(function() {
            if($(this).attr('checked')){
                $(this).closest('tr').find('td').css('background-color', '#DCDCDA');
            }else{
                $(this).closest('tr').find('td').css('background-color', '');
            }
        });

        $('input[type="checkbox"].checkall').change(function() {
            if($(this).attr('checked')){
                $('input[type="checkbox"].checkrow').closest('tr').find('td').css('background-color', '#DCDCDA');
            }else{
                $('input[type="checkbox"].checkrow').closest('tr').find('td').css('background-color', '');
            }
        });

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
                        url: BaseURL + "/admin/permission/add_remarks",
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

        $(".b_popup_5").dialog({
            autoOpen: false,
            modal: true,
            width: 400,
            buttons: {
                "Ok": function () {
                    _that = $(this);
                    var status = $("#leave-all-status").val();
                    var permission_ids = [];
                    $('input:checked').each(function () {
                        if ($(this).closest('tr[id]').attr('id') != 'header') {
                            permission_ids.push($(this).closest('tr[id]').attr('id')); // insert rowid's to array
                        }
                    });
                    var remarks = $("#leave-all-remarks").val();

                    $.ajax({
                        url: BaseURL + "/admin/permission/bulk_status_change",
                        type: "POST",
                        dataType: "JSON",
                        data: {'status': status, 'remarks': remarks, 'permission_ids': permission_ids},
                        success: function (msg) {
                            $.each(permission_ids, function (index, id) {
                                $("#button_" + id).attr("class", msg.class);
                                $("#span_" + id).html(msg.status);
                                $("#remarks_" + id).html(msg.remarks);
                            });
                            $("#leave-all-status").val("");
                            $("#leave-all-remarks").val("");
                            _that.dialog("close");
                        }
                    });
                },
                Cancel: function () {
                    $(this).dialog("close");
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

    function permission_sent(id, sts, user_id, leave_id) {
        var status = $('#a-permission-' + sts + '-' + id).attr('status');
        if (status == 2 || status == 1) {
            $(".b_popup_4").dialog('open');
            $('#leave-id').val(leave_id);
            $('#leave-status').val(status);
            $('#user-id').val(user_id);
        }
    }

    function change_status(sts)
    {
        var count = $(":checkbox").filter(':checked').length;

        if (count > 0) {
            if (sts == 2 || sts == 1) {
                $(".b_popup_5").dialog('open');
//            remarks = $("#leave-all-remarks").val();
                $("#leave-all-status").val(sts);
            }
        }
        else {
            alert('Please check atleast one checkbox');
        }
    }


</script>

<div class="dialog b_popup_4" style="display: none;" title="Remarks">                                
    <div class="block"> 
        <input type="hidden" value="" id="leave-id" name="data[Permission][id]"/>
        <input type="hidden" value="" id="leave-status" name="data[Permission][approved]"/>
        <input type="hidden" value="" id="user-id" name="data[Permission][user_id]"/>
        <span>Remarks:</span>
        <p>
            <textarea placeholder="Remarks..." name="data[Permission][remarks]" id="leave-remarks"></textarea>
        </p>
        <div class="dr"><span></span></div>
    </div>
</div>                                        

<div class="dialog b_popup_5" style="display: none;" title="Remarks">                                
    <div class="block"> 
        <input type="hidden" value="" id="leave-all-status"/>
        <span>Remarks:</span>
        <p>
            <textarea placeholder="Remarks..." name="data[Permission][remarks]" id="leave-all-remarks"></textarea>
        </p>
        <div class="dr"><span></span></div>
    </div>
</div>                                        

<?php if (!$this->request->is('ajax')) { ?>
<div style="margin:10px 50px 10px 23px;">
    <?php echo $this->Form->create('Permission'); ?>
    <?php
    $all_user = array();

    foreach ($users as $user) {
        $all_user[$user['User']['id']] = $user['User']['employee_name'];
    }
 if (isset($all['to_date'] , $all['from_date'])){
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
     <?php $name = isset($all['user_id']) ? $all['user_id'] : null;?>
    <b><?php echo " Employee : " ?></b>
    <?php echo $this->Form->input('user_id', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array($all_user),'selected' => $name, 'style' => 'width:140px; margin-top:6px;')); ?>

    <b><?php echo " From : " ?></b>
    <?php echo $this->Form->input('from_date', array('label' => false, 'div' => false, 'class' => 'form-control', 'value' => $from_date, 'style' => 'width:100px; margin-top:6px;')); ?>

    <b><?php echo " To : " ?></b>
    <?php echo $this->Form->input('to_date', array('label' => false, 'type' => 'text', 'div' => false, 'class' => 'form-control', 'value' => $to_date, 'style' => 'width:100px; margin-top:6px;')); ?>

    <div style="display:none">
        <b><?php echo " Type : " ?></b>
        <?php echo $this->Form->input('permission_leave', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array('1' => 'Permission', '2' => 'Half Day Leave'), 'selected' => $all['permission_leave'], 'style' => 'width:150px; margin-top:6px;')); ?>
    </div>

    <b><?php echo " Status : " ?></b>
    <?php echo $this->Form->input('approved', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array('0' => 'Pending', '1' => 'Approved', '2' => 'Declined'), 'selected' => $all['approved'], 'style' => 'width:100px; margin-top:6px;')); ?>

    <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?>
    <?php echo $this->Html->link('Reset', array('controller' => 'permission', 'action' => 'reset', 'admin' => true), array('class' => 'btn btn-danger')); ?>
    <?php echo $this->Form->end(); ?>
    <div class="clear"></div>
</div>
<?php } ?>

<?php if (!$this->request->is('ajax')) { ?>
    <div class="workplace">
        <div class="row-fluid">
            <div class="btn-group span2 li_check_access" style="float:right;">
                <!--<div class="li_check_access">-->
                <button data-toggle="dropdown" class="btn btn-info dropdown-toggle" id="com_button">
                    <span id="span_com">Bulk Change Status</span> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li class=""><a class="check-access" data-href="/admin/permission/add_remarks"  href="javascript:change_status(1)">Approved</a></li>
                    <li  class=""><a class="check-access" data-href="/admin/permission/add_remarks"  href="javascript:change_status(2)">Declined</a></li>
                </ul>
                <!--</div>-->
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <div class="head">
                    <div class="isw-list"></div>
                    <h1>Permission Requests</h1>
                    <div class="clear"></div>
                </div>
            <?php } ?>
            <div class="block-fluid table-sorting" id="content">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="leave-index-xxx">
                    <thead>
                        <tr>
                            <th width="5%"><input type="checkbox" name="checkall" class="checkall"/></th>
                            <th width="5%">No</th>
                            <th width="15%">Name</th>
                            <th width="10%">Requisition</th>
                            <th width="8%">Date</th>
                             <th width="8%">Compensation Date</th>
                            <th width="8%">From Time</th>
                            <th width="8%">To Time</th>
                            <th width="8%">Request Hours</th>
                            <th width="21%">Reason</th>
                            <th width="5%">Status</th>
                            <th width="12%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $page = $this->params['paging']['Permission']['page'];
                        $limit = $this->params['paging']['Permission']['limit'];
                        $counter = ($page * $limit) - $limit + 1;
                        foreach ($leaves as $leave):
                            $user = $this->requestAction('users/get_user', array('pass' => array('User.id' => $leave['Permission']['user_id'])));

                            $datetime1 = new DateTime($leave['Permission']['from_time']);
                            $datetime2 = new DateTime($leave['Permission']['to_time']);
                            $interval = $datetime1->diff($datetime2);
                            $hours = ($interval->format('%h') * 60) + ($interval->format('%i'));
                            ?>
                            <tr id="<?php echo $leave['Permission']['id'] ?>">
                                <td><input id="checkbox<?php echo $leave['Permission']['id'] ?>" name="checkbox<?php echo $leave['Permission']['id'] ?>" type="checkbox" value="<?php echo $leave['Permission']['id'] ?>" class="checkrow" /></td>
                                <td><?php echo h($counter); ?></td>
                                <td><?php echo h($user['User']['employee_name']); ?></td>
                                <td>
                                    <?php
                                    if ($leave['Permission']['request'] == 'past') {
                                        echo h('Past Requisition');
                                    } elseif ($leave['Permission']['request'] == 'current') {
                                        echo h('Current Requisition');
                                    }
                                    ?>
                                </td>
                                <td><?php echo h(date('d-m-Y', strtotime($leave['Permission']['date']))) ?></td>
                             
   <?php
 $com_id= $leave['Permission']['compensation_id']; 
$date="";
if ($com_id!=0) {
  $blogs = $this->requestAction('Compensations/get_permission_id', array('pass' => array('Compensation.id' =>$com_id)));
$date=date('d-m-Y', strtotime($blogs['Compensation']['date']));
}
?>
<td><?php echo h($date)?> </td>

                                <td><?php echo h(date('h:i A', strtotime($leave['Permission']['from_time']))) ?></td>
                                <td><?php echo h(date('h:i A', strtotime($leave['Permission']['to_time']))) ?></td>
                                <td><?php echo h(gmdate("H:i", ($hours * 60))) ?></td>
                                <td><?php echo h($leave['Permission']['reason']) ?></td>
                                <td>
                                    <?php
                                    $status = $leave['Permission']['approved'];
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
                                        <button data-toggle="dropdown" class="btn btn-mini <?php echo $button; ?> dropdown-toggle" id="button_<?php echo $leave['Permission']['id'] ?>">
                                            <span id="span_<?php echo $leave['Permission']['id'] ?>"><?php echo $value; ?></span> <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                        <!--                    <li><a href="#" user-id="<?php echo $leave['Permission']['user_id'] ?>" leave-id="<?php echo $leave['Permission']['id'] ?>" status="0">Pending</a></li>
                                            -->                    <li class=""><a class="check-access" id="a-permission-approve-<?php echo $leave['Permission']['id'] ?>" data-href="/admin/permission/add_remarks" href="javascript:permission_sent(<?php echo $leave['Permission']['id'] ?>, 'approve', <?php echo $leave['Permission']['user_id'] ?>,<?php echo $leave['Permission']['id'] ?>)" status="1">Approved</a></li>
                                            <li class=""><a class="check-access" id="a-permission-decline-<?php echo $leave['Permission']['id'] ?>" data-href="/admin/permission/add_remarks" href="javascript:permission_sent(<?php echo $leave['Permission']['id'] ?>, 'decline', <?php echo $leave['Permission']['user_id'] ?>,<?php echo $leave['Permission']['id'] ?>)" status="2">Declined</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td><span id="remarks_<?php echo $leave['Permission']['id'] ?>"><?php echo h($leave['Permission']['remarks']) ?></span></td>
                                </td>
                            </tr>
                            <?php
                            $counter++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
                <?php
//                $this->paginator->options(array('update' => '#content'));
//        $paginator = $this->Paginator;
                $summary = $this->Paginator->counter('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}');
                echo "<div class='dataTables_info' id='leave-index_info'>{$summary}</div>";
                echo "<div class='dataTables_paginate paging_full_numbers'>";
                echo $this->paginator->first("<< First", array('class' => 'paginate_button', 'tag' => false));
                if ($this->paginator->hasPrev()) {
                    echo $this->paginator->prev("Prev", array('class' => 'paginate_button', 'tag' => false));
                }
                echo $this->paginator->numbers(array('modulus' => 2, 'class' => 'paginate_button', 'tag' => false));
                if ($this->paginator->hasNext()) {
                    echo $this->paginator->next("Next", array('class' => 'paginate_button', 'tag' => false));
                }
                echo $this->paginator->last(" Last >>", array('class' => 'paginate_button', 'tag' => false));
                echo "</div>";
                ?>
                <?php echo $this->Js->writeBuffer(); ?> <!– This is mandotary –>
                <div class="clear"></div>
            </div>
            <?php if (!$this->request->is('ajax')) { ?>
            </div>
        </div>
        <div class="dr"><span></span></div>
    </div>
<?php } ?>