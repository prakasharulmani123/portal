<script type="text/javascript">
    $(document).ready(function () {
        $("#user_complaint-index").dataTable({
            "iDisplayLength": 10,
            "sPaginationType": "full_numbers",
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bPaginate": true});
        
        
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
                        url: BaseURL + "/admin/user_complaints/add_remarks",
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
                        url: BaseURL + "/admin/user_complaints/bulk_status_change",
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
    
    function complaint_sent(id, sts, user_id, leave_id) {
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
        <input type="hidden" value="" id="leave-id" name="data[UserComplaint][id]"/>
        <input type="hidden" value="" id="leave-status" name="data[UserComplaint][approved]"/>
        <input type="hidden" value="" id="user-id" name="data[UserComplaint][sender_id]"/>
        <span>Remarks:</span>
        <p>
            <textarea placeholder="Remarks..." name="data[UserComplaint][remarks]" id="leave-remarks"></textarea>
        </p>
        <div class="dr"><span></span></div>
    </div>
</div>                                        

<div class="dialog b_popup_5" style="display: none;" title="Remarks">                                
    <div class="block"> 
        <input type="hidden" value="" id="leave-all-status"/>
        <span>Remarks:</span>
        <p>
            <textarea placeholder="Remarks..." name="data[UserComplaint][remarks]" id="leave-all-remarks"></textarea>
        </p>
        <div class="dr"><span></span></div>
    </div>
</div>                                        

<div class="workplace">
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-right"></div>
                <h1>Complaints</h1>
                <div class="clear"></div>
            </div>
            <div class="block-fluid table-sorting">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="user_complaint-index">
                    <thead>
                        <tr>
                            <th width="6%">No</th>
                            <th width="20%">Sender</th>
                            <th width="20%">Complaint To</th>
                            <th width="10%">Date</th>
                            <th width="25%">Complaint</th>
                            <!--<th width="5%">Amount</th>-->
                            <th width="12%">Actions</th>
                            <th width="12%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($user_complaints as $user_complaint): ?>
                            <tr>
                                <td><?php echo h($i); ?></td>
                                <td><?php echo h($user_complaint['Sender']['employee_name']); ?></td>
                                <td><?php echo h($user_complaint['Receiver']['employee_name']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user_complaint['UserComplaint']['created'])) ?></td>
                                <td><?php 
                                echo h($user_complaint['UserComplaint']['reason']);
                                if($user_complaint['UserComplaint']['file']){
                                    echo '<br />';                                    
                                    echo $this->Html->link('(file attached)', Router::url('/'.$user_complaint['UserComplaint']['file'], true), array('title' => 'View File', 'escape' => false, 'target' => '_blank')).' &nbsp;';
                                }
                                ?>
                                </td>
                                <!--<td><?php echo h($user_complaint['UserComplaint']['fine_amount']); ?></td>-->
                                <td>
                                    <?php
                                    $status = $user_complaint['UserComplaint']['approved'];
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
                                    <div class="btn-group"> 
                                        <button data-toggle="dropdown" class="btn btn-mini <?php echo $button; ?> dropdown-toggle" id="button_<?php echo $user_complaint['UserComplaint']['id'] ?>">
                                            <span id="span_<?php echo $user_complaint['UserComplaint']['id'] ?>"><?php echo $value; ?></span> <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                           <li><a id="a-permission-approve-<?php echo $user_complaint['UserComplaint']['id'] ?>" href="javascript:complaint_sent(<?php echo $user_complaint['UserComplaint']['id'] ?>, 'approve', <?php echo $user_complaint['UserComplaint']['sender_id'] ?>,<?php echo $user_complaint['UserComplaint']['id'] ?>)" status="1">Approved</a></li>
                                            <li><a id="a-permission-decline-<?php echo $user_complaint['UserComplaint']['id'] ?>" href="javascript:complaint_sent(<?php echo $user_complaint['UserComplaint']['id'] ?>, 'decline', <?php echo $user_complaint['UserComplaint']['sender_id'] ?>,<?php echo $user_complaint['UserComplaint']['id'] ?>)" status="2">Declined</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td><span id="remarks_<?php echo $user_complaint['UserComplaint']['id'] ?>"><?php echo h($user_complaint['UserComplaint']['remarks']) ?></span></td>
                            </tr>
                            <?php $i++;
                        endforeach; ?>
                    </tbody>
                </table>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="dr"><span></span></div>
</div>