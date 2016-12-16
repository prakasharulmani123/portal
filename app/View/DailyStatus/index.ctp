<?php

/*
  if(!empty($reports) && $edit_id == '') {
  echo $this->Html->script('scroll');
  } */
?>
<script type="text/javascript">

    $(document).ready(function () {
        var cate_id = document.getElementById('DailyStatusCategoryId').value;
        get_lunch(cate_id);

<?php //$check = $this->Session->read('DailyReportSend')   ?>
<?php //if($this->Session->check('Message.flash')){   ?>
<?php if ($report_send == 1) { ?>
        $("#popup_2").trigger("click");
<?php } ?>

        $("#popup_submit").click(function () {
            _end_time = $('#office_end_time');
            _stopContinue = false;
            if (_end_time.length == 1) {
                _send_end_time = moment().unix();
                _actual_end_time = moment(_end_time.html()).unix();

                if (_send_end_time < _actual_end_time) {
                    $("#b_popup_3").dialog("close");
                    var _stopContinue = !confirm("Now you are sending report earlier from Actual Office End time : " + moment(_end_time.html()).format('h:mm:ss a') + "\nAre you sure to Proceed?");
                }
            }
            if (_stopContinue)
                return false;

            $("#b_popup_3").dialog("open");
            $("#popup_submit").attr('disabled', true);
            $('#load_image_send').show();

            //checking temp reports....
            $.ajax({
                url: BaseURL + "/temp_reports/check_daily_reports/" + "<?php echo $this->Session->read('User.id') ?>" + "/" + "<?php echo date('Y-m-d') ?>",
                dataType: "JSON",
                success: function (msg) {
                    $('#load_image_send').show();
                    if (msg.success == 1)
                    {
                        //end time checking....
                        $.ajax({
                            url: BaseURL + "/temp_reports/check_daily_report_time/" + "<?php echo $this->Session->read('User.id') ?>" + "/" + "<?php echo date('Y-m-d') ?>",
                            dataType: "JSON",
                            success: function (msg) {
                                if (msg.success == 1)
                                {
                                    //Check if any one works less than 9.30 Hours in a day (Ex: 10.00 to 7.20)
                                    $.ajax({
                                        url: BaseURL + "/temp_reports/check_report_entries/" + "<?php echo $this->Session->read('User.id') ?>" + "/" + "<?php echo date('Y-m-d') ?>",
                                        success: function (msg) {
                                            $("#send-report").trigger("click");
                                        },
                                        error: function () {
                                            $("#b_popup_3").dialog("close");
                                            $("#popup_submit").attr('disabled', false);
                                            $('#load_image_send').hide();
                                            alert('An error occured while sent a report. Please try again');
                                        }
                                    });
                                }
                                else {
                                    $("#b_popup_3").dialog("close");
                                    $('#load_image_send').hide();
                                    $("#popup_submit").attr('disabled', false);
                                    alert('Report entry End time is ' + msg.lead_lag + ' ' + msg.late_time + ' minutes from current time (' + msg.current_time + ')\nPlease correct end time in last report entry \nLead Time must be less than or equals to 5 minutes from current time');
                                }
                            }
                        });
                    }
                    else
                    {
                        $("#b_popup_3").dialog("close");
                        $('#load_image_send').hide();
                        $("#popup_submit").attr('disabled', false);

                        if (msg.success == -1)
                        {
                            alert('Please check your report entries. Start and End Time Mismatch in ' + msg.error_row_1 + ' & ' + msg.error_row_2 + ' rows');
                        }
                        else if (msg.success == -2)
                        {
                            alert('Please correct your permission start and end time. mismatch occurs \nYour today permission hours are ' + msg.from_time + ' - ' + msg.to_time + '\n But your entered hours in report are ' + msg.error_from + ' - ' + msg.error_to + '\n Please Change start and end time on Permission row.');
                        }
                        else if (msg.success == -3)
                        {
                            alert("Please add your today's Permission Hours \nChoose Permission from category drop down and add to your entry");
                        }
                        else if (msg.success == -4)
                        {
                            alert("Please enter correct time. \n Timer Turn ON : " + msg.correct_time + "\n Your Report Start Time :" + msg.wrong_time);
                        }
                    }
                }
            });

        });

<?php if (!empty($reports)) { ?>
        $('#send_div').show();
<?php } else { ?>
        $('#send_div').hide();
<?php } ?>

        $(".b_popup_4").dialog({
            autoOpen: false,
            modal: true,
            width: 500,
            buttons: {
                "Ok": function () {
                    var time_in_hours = $('#PendingReportStartHours').val();
                    var time_in_minutes = $('#PendingReportStartMinutes').val();
                    var time_in_mer = $('#PendingReportStartMer').val();
                    var reason = $('#PendingReportReason').val();

                    if (time_in_hours == '')
                    {
                        alert('Please Enter Hours');
                        $('#PendingReportStartHours').focus();
                        return false;
                    }

                    if (time_in_minutes == '')
                    {
                        alert('Please Enter Minutes');
                        $('#PendingReportStartMinutes').focus();
                        return false;
                    }

                    if (time_in_mer == '')
                    {
                        alert('Please Enter Merdian');
                        $('#PendingReportStartMer').focus();
                        return false;
                    }

                    if (reason == '')
                    {
                        alert('Please Enter Reason');
                        $('#PendingReportReason').focus();
                        return false;
                    }

                    $("#send-pending-report").trigger("click");

                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });

    });

    jQuery(document).ready(function () {
        $(".b_popup_2").dialog({
            width: 300,
            modal: true,
            buttons: {
                "Turn OFF": function () {
                    window.location.href = BaseURL + '/entries/entry/<?php echo $entry_id ?>';
                },
                Cancel: function () {
                    $(this).dialog('close');
                }
            }
        });
    });

    function get_lunch(id)
    {
        if (id == 22 || id == 23 || id == 24) {
            var project = document.getElementById('DailyStatusProjectname');
            var work = document.getElementById('DailyStatusWorkId');
            var status = document.getElementById('DailyStatusStatus');
            var comments = document.getElementById('DailyStatusComments');

            document.getElementById('DailyStatusProjectname').value = '';
            document.getElementById('DailyStatusWorkId').value = '';
            document.getElementById('DailyStatusStatus').value = '';
            document.getElementById('DailyStatusComments').value = '';

            project.setAttribute("class", "none");
            work.setAttribute("class", "none");
            status.setAttribute("class", "none");
            comments.setAttribute("class", "none");

            document.getElementById('display-lunch').style.display = 'block';
            document.getElementById('display-comment').style.display = 'none';

            document.getElementById('display-upper-block').style.display = 'none';
            document.getElementById('display-lower-block').style.display = 'none';

            if (id == 24) {
                $.ajax({
                    url: BaseURL + "/permission/check_permission_exists/" + "<?php echo $this->Session->read('User.id') ?>" + "/" + "<?php echo date('Y-m-d') ?>",
                    beforeSend: function () {
                        $('#permission_loader').show();
                    },
                    dataType: "JSON",
                    success: function (permission) {
                        $('#permission_loader').hide();

                        if (permission['exists'] == 1) {
                            $('#new_start_hours').val(permission['start_hours']);
                            $('#new_start_minutes').val(permission['start_minutes']);
                            $('#new_start_meridian').val(permission['start_meridian']);

                            $('#DailyStatusStartHours').val(permission['start_hours']);
                            $('#DailyStatusStartMinutes').val(permission['start_minutes']);
                            $('#DailyStatusStartMer').val(permission['start_meridian']);

                            $('#DailyStatusEndHours').val(permission['end_hours']);
                            $('#DailyStatusEndMinutes').val(permission['end_minutes']);
                            $('#DailyStatusEndMer').val(permission['end_meridian']);

                            $('#DailyStatusComments').val(permission['reason']);
                        }
                        else {
                            alert("You don't have permission entry today");
                            $('#DailyStatusCategoryId').val('');
                        }
                    }
                });
            }
        }
        else {
            var project = document.getElementById('DailyStatusProjectname');
            var work = document.getElementById('DailyStatusWorkId');
            var status = document.getElementById('DailyStatusStatus');
            var comments = document.getElementById('DailyStatusComments');

            project.setAttribute("class", "validate[required]");
            work.setAttribute("class", "validate[required]");
            status.setAttribute("class", "validate[required]");
            comments.setAttribute("class", "validate[required]");

            document.getElementById('display-lunch').style.display = 'none';
            document.getElementById('display-comment').style.display = 'block';

            document.getElementById('display-upper-block').style.display = 'block';
            document.getElementById('display-lower-block').style.display = 'block';
        }
    }

    function validateForm()
    {
//	$('#validation').submit(function(){
        var start_hour = document.getElementById('new_start_hours').value;
        var start_minute = document.getElementById('new_start_minutes').value;
        var start_meridian = document.getElementById('new_start_meridian').value;

        var end_hour = document.getElementById('DailyStatusEndHours').value;
        var end_minute = document.getElementById('DailyStatusEndMinutes').value;
        var end_meridian = document.getElementById('DailyStatusEndMer').value;

        if (start_hour == end_hour && start_minute == end_minute && start_meridian == end_meridian)
        {
            alert('Start & End Time are same. Please Change');
            return false;
        }

        var category = document.getElementById('DailyStatusCategoryId').value;
        var project = document.getElementById('DailyStatusProjectname').value;
        var work = document.getElementById('DailyStatusWorkId').value;
        var status = document.getElementById('DailyStatusStatus').value;
        var comments = document.getElementById('DailyStatusComments').value;

        if (category != '' && start_hour != '' && start_minute != '' && start_meridian != '' && end_hour != '' && end_minute != '' && end_meridian != '') {
            if (category == 22 || category == 23 || category == 24) {
                add_report();
            }
            else {
                if (project != '' && work != '' && status != '' && comments != '') {
                    add_report();
                }
            }
        }

//		if(category != '' && project != '' && work != '' && status != '' && comments != ''){
//			add_report();
//		}
        return false;
//	});
    }

    function add_report()
    {
        $('#load_image').show();
        $("#save").prop('disabled', true);
        var id = $('#DailyStatusId').val();

        var user_id = '<?php echo $this->Session->read('User.id') ?>';
        var date = '<?php echo date('Y-m-d') ?>';
        var category_id = $('#DailyStatusCategoryId').val();
        var projectname = $('#DailyStatusProjectname').val();
        var work_id = $('#DailyStatusWorkId').val();

        var start_hours = $('#new_start_hours').val();
        var start_minutes = $('#new_start_minutes').val();
        var start_meridian = $('#new_start_meridian').val();

        var end_hours = $('#DailyStatusEndHours').val();
        var end_minutes = $('#DailyStatusEndMinutes').val();
        var end_meridian = $('#DailyStatusEndMer').val();

        var status = $('#DailyStatusStatus').val();
        var comments = $('#DailyStatusComments').val();

        if (status == '' || status == null)
        {
            status = 0;
        }

        $.ajax({
            url: BaseURL + "/dailystatus/add_reports",
            cache: false,
            type: 'POST',
            dataType: 'HTML',
            data: {'id': id, 'user_id': user_id, 'date': date, 'category_id': category_id, 'projectname': projectname, 'work_id': work_id, 'start_hours': start_hours, 'start_minutes': start_minutes, 'start_meridian': start_meridian, 'end_hours': end_hours, 'end_minutes': end_minutes, 'end_meridian': end_meridian, 'status': status, 'comments': comments},
            success: function (data) {
                $('#load_image').hide();
                $("#save").prop('disabled', false);
                $('#added_reports').html(data);

                $("html, body").animate({scrollTop: $(document).height()}, 1500);

                if (id != '' || id != null)
                {
                    $("#save").removeClass("btn btn-warning");
                    $("#save").addClass("btn btn-primary");
                    $("#save").val("Add");

                    $('#DailyStatusId').val('');
                }

                $('#DailyStatusCategoryId').val('');
                $('#DailyStatusProjectname').val('');
                $('#DailyStatusWorkId').val('');

                $('#new_start_hours').removeAttr('disabled');
                $('#new_start_minutes').removeAttr('disabled');
                $('#new_start_meridian').removeAttr('disabled');

                end_time = $('#last_row_end_time').html().split(":");

                var end_hours = parseInt(end_time[0]);
                var end_minutes = end_time[1];
                var end_meridian = end_time[2];

                $('#new_start_hours').val(end_hours);
                $('#new_start_minutes').val(end_minutes);
                $('#new_start_meridian').val(end_meridian)
                        ;
                $('#DailyStatusStartHours').val(end_hours);
                $('#DailyStatusStartMinutes').val(end_minutes);
                $('#DailyStatusStartMer').val(end_meridian);

                $('#DailyStatusEndHours').val(end_hours);
                $('#DailyStatusEndMinutes').val(end_minutes);
                $('#DailyStatusEndMer').val(end_meridian);

                $('#DailyStatusStatus').val('');
                $('#DailyStatusComments').val('');

                $('#send_div').show();
            }
        });
    }

    function HTMLDecode(s) {
        return $('<div></div>').html(s).text();
    }
    
    function edit_row(id)
    {
        var category = $('#td_category_' + id).html();
        var projectname = $('#td_projectname_' + id).html();
        var work_id = $('#td_work_id_' + id).html();
        
        category = HTMLDecode(category);
        projectname = HTMLDecode(projectname);
        work_id = HTMLDecode(work_id);
        
        start_time = $('#td_start_time_' + id).html().split(":");
        end_time = $('#td_end_time_' + id).html().split(":");

        var start_hours = parseInt(start_time[0]);
        var start_minutes = start_time[1];
        var start_meridian = start_time[2];

        var end_hours = parseInt(end_time[0]);
        var end_minutes = end_time[1];
        var end_meridian = end_time[2];

        var status = $('#td_status_' + id).html();
        var comments = $('#td_comments_' + id).html();
        comments = HTMLDecode(comments);

        var disabled = $('#td_disabled_' + id).html();

        $('#DailyStatusId').val(id);

        $('#DailyStatusCategoryId').val(category);
        $('#DailyStatusProjectname').val(projectname);
        $('#DailyStatusWorkId').val(work_id);

        if (disabled == 'disabled') {
            document.getElementById("new_start_hours").disabled = true;
            document.getElementById("new_start_minutes").disabled = true;
            document.getElementById("new_start_meridian").disabled = true;
        }
        else {
            $('#new_start_hours').removeAttr('disabled');
            $('#new_start_minutes').removeAttr('disabled');
            $('#new_start_meridian').removeAttr('disabled');
        }

        $('#new_start_hours').val(start_hours);
        $('#new_start_minutes').val(start_minutes);
        $('#new_start_meridian').val(start_meridian);

        $('#DailyStatusStartHours').val(start_hours);
        $('#DailyStatusStartMinutes').val(start_minutes);
        $('#DailyStatusStartMer').val(start_meridian);

        $('#DailyStatusEndHours').val(end_hours);
        $('#DailyStatusEndMinutes').val(end_minutes);
        $('#DailyStatusEndMer').val(end_meridian);

        $('#DailyStatusStatus').val(status);
        $('#DailyStatusComments').val(comments);

        get_lunch(category);

        $("#save").removeClass("btn btn-primary");
        $("#save").addClass("btn btn-warning");
        $("#save").val("Edit");

        $("html, body").animate({scrollTop: 0}, 1500);
    }

    function delete_row(id)
    {
        $('#load_image').show();
        $.ajax({
            url: BaseURL + "/dailystatus/delete_reports",
            cache: false,
            type: 'POST',
            dataType: 'HTML',
            data: {'id': id},
            success: function (data) {
                $('#load_image').hide();
                $('#added_reports').html(data);

                end_time = $('#last_row_end_time').html().split(":");

                var end_hours = parseInt(end_time[0]);
                var end_minutes = end_time[1];
                var end_meridian = end_time[2];

                $('#new_start_hours').val(end_hours);
                $('#new_start_minutes').val(end_minutes);
                $('#new_start_meridian').val(end_meridian)
                        ;
                $('#DailyStatusStartHours').val(end_hours);
                $('#DailyStatusStartMinutes').val(end_minutes);
                $('#DailyStatusStartMer').val(end_meridian);

                $('#DailyStatusEndHours').val(end_hours);
                $('#DailyStatusEndMinutes').val(end_minutes);
                $('#DailyStatusEndMer').val(end_meridian);
            }
        });
    }

    function copy_row(id)
    {
        var category = $('#td_category_' + id).html();
        var projectname = $('#td_projectname_' + id).html();
        var work_id = $('#td_work_id_' + id).html();
        var status = $('#td_status_' + id).html();
        var comments = $('#td_comments_' + id).html();

        category = HTMLDecode(category);
        projectname = HTMLDecode(projectname);
        work_id = HTMLDecode(work_id);
        comments = HTMLDecode(comments);

        $('#DailyStatusCategoryId').val(category);
        $('#DailyStatusProjectname').val(projectname);
        $('#DailyStatusWorkId').val(work_id);
        $('#DailyStatusStatus').val(status);
        $('#DailyStatusComments').val(comments);
        get_lunch(category);

        $("html, body").animate({scrollTop: 0}, 1500);
    }

</script>

<?php
$hours = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12');
$minutes = array('00' => '00', '05' => '05', '10' => '10', '15' => '15', '20' => '20', '25' => '25', '30' => '30', '35' => '35', '40' => '40', '45' => '45', '50' => '50', '55' => '55');
$mer = array('am' => 'am', 'pm' => 'pm');
?>

<div class="dialog" id="b_popup_3" style="display: none;" title="Report Confirmation">                                
    <div class="block">
        <p align="center">Are you sure to send daily status report ?</p>
        <p align="center">
            <span id="load_image_send" style="display:none">
                <img title="loading..." src="<?php echo $this->base ?>/img/loaders/loader_ye.gif">
            </span>

            <button class="btn" type="button" id="popup_submit">Send</button>
        </p>
    </div>
</div>                                        

<div class="dialog b_popup_2" id="b_popup_2" style="display: none;" title="Timer Alert">
    <h4 align="center" style="color:#56799F;"><?php echo $this->Html->image('/img/clock.png', array('alt' => 'time')); ?> Turn Off Your Timer ?</h4>                
</div>            

<div class="dialog b_popup_4" id="b_popup_4" style="display: none;" title="Request for Enable Report">
    <?php echo $this->Form->create('PendingReport', array('id' => 'pending_validation', 'controller' => 'pending_reports', 'action' => 'send_request_on_timer')); ?>
    <div class="block">
        <span>Time In:</span>
        <p>
            <?php
            echo $this->Form->input('start_time', array('type' => 'hidden'));
            echo $this->Form->input('user_id', array('type' => 'hidden', 'value' => $this->Session->read('User.id')));
            echo $this->Form->input('date', array('type' => 'hidden', 'value' => date('Y-m-d')));
            echo $this->Form->input('status', array('type' => 'hidden', 'value' => 0));

            echo $this->Form->input('start_hours', array('type' => 'select', 'options' => $hours, 'label' => false, 'empty' => 'hours', 'name' => 'data[PendingReport][start][hours]', 'div' => false, 'style' => 'width: 123px;'));
            echo $this->Form->input('start_minutes', array('type' => 'select', 'options' => $minutes, 'label' => false, 'empty' => 'minutes', 'name' => 'data[PendingReport][start][minutes]', 'div' => false, 'style' => 'width: 123px;'));
            echo $this->Form->input('start_mer', array('type' => 'select', 'options' => $mer, 'label' => false, 'empty' => 'meridian', 'name' => 'data[PendingReport][start][meridian]', 'div' => false, 'style' => 'width: 123px;'));
            ?>
        </p>
        <span>Reason:</span>
        <p>
            <?php echo $this->Form->input('reason', array('type' => 'textarea', 'label' => false, 'div' => false)) ?>
        </p>
        <div class="dr">
            <span></span>
        </div>
        <p style="color:blue">Your request will sent to admin. You can enter the report after admin approves</p>
    </div>
    <input type="submit" name="save" id="send-pending-report" value="Sent" style="display:none"/>
    <?php echo $this->Form->end(); ?>
</div>            

<div class="workplace">
    <?php echo $this->Form->create('DailyStatus', array('id' => 'validation', 'onsubmit' => 'return validateForm()')); ?>

    <?php echo $this->Form->hidden('id'); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-documents"></div>
                <h1>Daily Status Report<span><?php echo " ( " . date('d-m-Y') . " : " . date('l') . " )"; ?></span></h1>
                <div class="clear"></div>
            </div>

            <div class="block-fluid">
                <div class="row-form" style="padding: 8px 16px;">
                    <div class="span3">Employee Name:</div>
                    <div class="span9">
                        <b><?php echo $users['User']['employee_name']; ?></b>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="row-form" style="padding: 8px 16px;">
                    <div class="span3">ID No:</div>
                    <div class="span9">
                        <?php echo $users['User']['employee_id'] ?>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="row-form" style="padding: 8px 16px;">
                    <div class="span3">Designation:</div>
                    <div class="span9">
                        <?php echo $users['User']['designation'] ?>
                    </div>
                    <div class="clear"></div>
                </div>

                <?php
                $categories = $this->requestAction('Categories/get_all_categories');

                $all_category = array();
                foreach ($categories as $category) {
                    $all_category[$category['Category']['id']] = $category['Category']['category'];
                }
                ?>

                <div class="row-form">
                    <div class="span3">Category*:</div>
                    <div class="span4">
                        <?php echo $this->Form->input('category_id', array('type' => 'select', 'options' => array($all_category), 'class' => 'validate[required]', 'label' => false, 'empty' => 'Select Category', 'onchange' => 'get_lunch(this.value)')); ?>

                        <span id="display-lunch" style="color:red; font-size:14px; display:none"></span>
                    </div>

                    <?php echo $this->Html->image('loaders/s_loader.gif', array('id' => 'permission_loader', 'style' => 'display: none;margin: 6px 4px 7px;padding-left: 6px;')); ?>
                    <div class="clear"></div>
                </div>

                <div id="display-upper-block" style="display:block; border-bottom: 1px solid #DDDDDD;">
                    <div class="row-form">
                        <div class="span3">Project Name*:</div>
                        <div class="span6">
                            <?php echo $this->Form->input('projectname', array('class' => 'validate[required]', 'label' => false)); ?>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="row-form">
                        <div class="span3">Kind of works*:</div>
                        <div class="span6">
                            <?php echo $this->Form->input('work_id', array('type' => 'text', 'class' => 'validate[required]', 'label' => false)); ?>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>

                <?php
                $selected = "";

                $time_in_hour = "";
                $time_in_minute = "";
                $time_in_merdian = "";

                $time_out_hour = "";
                $time_out_minute = "";
                $time_out_merdian = "";
                $i = 0;

                //edit not used
                $edit = $this->Session->read('DailyReportEdit');
                $entry = $this->requestAction('entries/check_time_in_out/' . $this->Session->read('User.id'));

                if ($edit == NULL || $edit == "") {
                    if (empty($reports)) {
                        if ($entry) {
                            $time_in_hour = $time_out_hour = date('H', strtotime($entry['Entry']['time_in']));

                            if ($time_in_hour > 12) {
                                $time_in_hour = $time_out_hour = ($time_in_hour - 12);
                            }

                            $time_in_minute = $time_out_minute = date('i', strtotime($entry['Entry']['time_in']));

                            $minute_part_one = substr(date('i', strtotime($entry['Entry']['time_in'])), 0, 1);
                            $minute_part_two = substr(date('i', strtotime($entry['Entry']['time_in'])), 1, 2);

                            if ($minute_part_two < 5) {
                                if ($minute_part_two < 3) {
                                    $minute_part_two = 0;
                                } else {
                                    $minute_part_two = 5;
                                }
                            } elseif ($minute_part_two > 5) {
                                if ($minute_part_one == '5') {
                                    if ($minute_part_two < 7) {
                                        $minute_part_two = 5;
                                    } else {
                                        if ($time_in_hour == '12') {
                                            $time_in_hour = $time_out_hour = 1;

                                            $minute_part_one = 0;
                                            $minute_part_two = 0;
                                        } else {
                                            $time_in_hour = $time_in_hour + 1;
                                            $time_out_hour = $time_out_hour + 1;

                                            $minute_part_one = 0;
                                            $minute_part_two = 0;
                                        }
                                    }
                                } else {
                                    if ($minute_part_two < 7) {
                                        $minute_part_two = 5;
                                    } else {
                                        $minute_part_two = 0;
                                        $minute_part_one = $minute_part_one + 1;
                                    }
                                }
                            }

                            $time_in_minute = $time_out_minute = $minute_part_one . $minute_part_two;
                            $time_in_merdian = $time_out_merdian = strtolower(date('A', strtotime($entry['Entry']['time_in'])));
                        }
                    } else {
                        $end = end($reports);

                        $time_in_hour = $time_out_hour = date('h', strtotime($end['TempReport']['end_time']));
                        $time_in_minute = $time_out_minute = date('i', strtotime($end['TempReport']['end_time']));
                        $time_in_merdian = $time_out_merdian = date('a', strtotime($end['TempReport']['end_time']));
                    }
                } else {
                    $time_in_hour = date('h', strtotime($this->data['DailyStatus']['start_time']));
                    $time_in_minute = date('i', strtotime($this->data['DailyStatus']['start_time']));
                    $time_in_merdian = date('a', strtotime($this->data['DailyStatus']['start_time']));

                    $time_out_hour = date('h', strtotime($this->data['DailyStatus']['end_time']));
                    $time_out_minute = date('i', strtotime($this->data['DailyStatus']['end_time']));
                    $time_out_merdian = date('a', strtotime($this->data['DailyStatus']['end_time']));
                }
                ?>

                <?php
                if (!empty($entry)) {
                    $timer = '1';
                    if (empty($reports)) {
                        $disabled = 'disabled';
                    } else {
                        $first_array = reset($reports);

                        if (date('h', strtotime($first_array['TempReport']['start_time'])) == $time_in_hour && date('i', strtotime($first_array['TempReport']['start_time'])) == $time_in_minute && date('a', strtotime($first_array['TempReport']['start_time'])) == $time_in_merdian) {
                            $disabled = 'disabled';
                        } else {
                            $disabled = '';
                        }
                    }
                } else {
                    $disabled = 'disabled';
                    $timer = '0';
                }

                if ($disabled == 'disabled') {
                    echo $this->Form->input('start_hours', array('class' => 'validate[required]', 'type' => 'hidden', 'value' => $time_in_hour, 'name' => 'data[DailyStatus][start][hours]'));
                    echo $this->Form->input('start_minutes', array('class' => 'validate[required]', 'type' => 'hidden', 'value' => $time_in_minute, 'name' => 'data[DailyStatus][start][minutes]'));
                    echo $this->Form->input('start_mer', array('class' => 'validate[required]', 'type' => 'hidden', 'value' => $time_in_merdian, 'name' => 'data[DailyStatus][start][meridian]'));
                }
                ?>
                <div class="row-form">
                    <div class="span3">Start Time*:</div>
                    <?php echo $this->Form->input('start_time', array('class' => 'validate[required]', 'type' => 'hidden')); ?>
                    <div class="span1">
                        <?php echo $this->Form->input('start_hours', array('class' => 'validate[required]', 'type' => 'select', 'options' => $hours, 'label' => false, 'empty' => 'hours', 'name' => 'data[DailyStatus][start][hours]', 'selected' => $time_in_hour, $disabled, 'id' => 'new_start_hours')); ?>
                    </div>
                    <div class="span1">
                        <?php echo $this->Form->input('start_minutes', array('class' => 'validate[required]', 'type' => 'select', 'options' => $minutes, 'label' => false, 'empty' => 'minutes', 'name' => 'data[DailyStatus][start][minutes]', 'selected' => $time_in_minute, $disabled, 'id' => 'new_start_minutes')); ?>
                    </div>
                    <div class="span1">
                        <?php echo $this->Form->input('start_mer', array('class' => 'validate[required]', 'type' => 'select', 'options' => $mer, 'label' => false, 'empty' => 'meridian', 'name' => 'data[DailyStatus][start][meridian]', 'selected' => $time_in_merdian, $disabled, 'id' => 'new_start_meridian')); ?>
                    </div>
                    <?php if ($timer == 0) { ?>
                        <?php $request_status = $this->requestAction('pending_reports/check_status_by_date/' . date('Y-m-d')); ?>
                    <div class="span6"><span style="color:red; font-size:15px">You didn't ON Timer - </span>
                            <?php if (empty($request_status)) { ?>
                        <a href="#" id="popup_4">Click to Send Request For Enable Report</a>
                                <?php
                            } else {
                                ?>
                        <span style="font-size:15px">Request Sent</span>
                            <?php } ?>
                    </div>
                    <?php } ?>
                    <div class="clear"></div>
                </div>

                <div class="row-form">
                    <div class="span3">End Time*:</div>
                    <?php echo $this->Form->input('end_time', array('class' => 'validate[required]', 'type' => 'hidden')); ?>
                    <div class="span1">
                        <?php echo $this->Form->input('end_hours', array('class' => 'validate[required]', 'type' => 'select', 'options' => $hours, 'label' => false, 'empty' => 'hours', 'name' => 'data[DailyStatus][end][hours]', 'selected' => $time_out_hour)); ?>
                    </div>
                    <div class="span1">
                        <?php echo $this->Form->input('end_minutes', array('class' => 'validate[required]', 'type' => 'select', 'options' => $minutes, 'label' => false, 'empty' => 'minutes', 'name' => 'data[DailyStatus][end][minutes]', 'selected' => $time_out_minute)); ?>
                    </div>
                    <div class="span1">
                        <?php echo $this->Form->input('end_mer', array('class' => 'validate[required]', 'type' => 'select', 'options' => $mer, 'label' => false, 'empty' => 'meridian', 'name' => 'data[DailyStatus][end][meridian]', 'selected' => $time_out_merdian)); ?>
                    </div>
                    <div class="clear"></div>
                </div>

                <div id="display-lower-block" style="display:block;">
                    <div class="row-form">
                        <div class="span3">Status*:</div>
                        <?php $status = array('1' => 'progress', '2' => 'completed', '3' => 'in-completed', '4' => 'cancelled'); ?>
                        <div class="span3">
                            <?php echo $this->Form->input('status', array('type' => 'select', 'options' => $status, 'class' => 'validate[required]', 'label' => false, 'empty' => 'select')); ?>
                        </div>
                        <div class="clear"></div>
                    </div>

                    <div class="row-form">
                        <div class="span3">Comments*:</div>
                        <div class="span9">
                            <?php echo $this->Form->input('comments', array('class' => 'validate[required]', 'label' => false)); ?>
                            <span id="display-comment" style="color:red; font-size:14px">*note:just type comments only</span>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>

                <div class="row-fluid">                
                    <div class="row-form">
                        <div align="right" class="span12">
                            <?php
                            if ($edit == NULL || $edit == "") {
                                $button_name = 'Add';
                                $button_type = 'primary';
                            } else {
                                $button_name = 'Edit';
                                $button_type = 'warning';
                            }
                            ?>

                            <span id="load_image" style="display:none">
                                <img title="loader_ye.gif" src="<?php echo $this->base ?>/img/loaders/loader_ye.gif">
                                <!--<img title="c_loader_ye.gif" src="<?php echo $this->base ?>/img/loaders/c_loader_ye.gif">-->
                            </span>
                            <?php if ($timer == 1) { ?>
                            <input type="submit" name="save" id="save" value="<?php echo $button_name ?>" class="btn btn-<?php echo $button_type ?>" />
                            <?php } ?>

                            <button type="button" value="Send" class="btn" id="popup_2" style="display:none;">Alert</button>
                        </div>
                    </div>
                </div>

                <div id="added_reports">

                    <?php if (!empty($reports)) { ?>
                    <div><h4 align="center">Your Daily Status Report</h4></div>
                        <?php
                        $worked_hours = $total_hours = 0;

                        foreach ($reports as $key => $report) {
                            $datetime1 = new DateTime($report['TempReport']['start_time']);
                            $datetime2 = new DateTime($report['TempReport']['end_time']);
                            $interval = $datetime1->diff($datetime2);
                            $total_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));

                            if ($report['TempReport']['category_id'] != 23 && $report['TempReport']['category_id'] != 22 && $report['TempReport']['category_id'] != 24) {
                                $worked_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
                            }
                        }
                        $time_ob_1 = new DateTime($reports[0]['TempReport']['start_time']);
                        $time_ob_1->add(new DateInterval('PT' . 9 . 'H' . 30 . 'M'));
                        $end_time = $time_ob_1->format('h:i A');
                        $validate_end_time = $time_ob_1->format('Y-m-d H:i:s');

                        if (!empty($late_entry) && empty($permission_exists)) {
                            $end_time = date('h:i A', strtotime($office_times['late_entry_end_time']));
                            $validate_end_time = date('Y-m-d H:i:s', strtotime($office_times['late_entry_end_time']));
                        }
                        ?>
                    <div style="margin-left:20px; color:#00C;"><h6 align="left">Worked Hours : <?php echo gmdate("H:i", ($worked_hours * 60)); ?></h6></div>
                    <div style="margin-left:20px; color:#00C;"><h6 align="left">Total Hours : <?php echo gmdate("H:i", ($total_hours * 60)); ?></h6></div>
                        <?php if (empty($leave)) { ?>
                    <div style="margin-right:20px; color:#00C; float: right"><h6 align="left">Your Office End Time  : <?php echo $end_time; ?></h6></div>
                    <span id="office_end_time" style="display: none;"><?php echo $validate_end_time; ?></span>
                        <?php } ?>
                    <div class="row-form">
                        <table border="1" bordercolor="#52759B" style="box-shadow:5px 5px #52759B;" cellpadding="0" cellspacing="0" width="100%" class="table">
                            <thead>
                            <th width="6%">No.</th>
                            <th width="12%">Project Name</th>
                            <th width="12%">Category</th>
                            <th width="14%">Work</th>
                            <th width="10%">Start Time</th>
                            <th width="10%">End Time</th>
                            <th width="5%">Status</th>
                            <th width="22%">Comments</th>
                            <th width="9%">Action</th>
                            </thead>
                            <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($reports as $key => $report) {
                                        $ctgy = $this->requestAction('Categories/get_category_by_id', array('pass' => array('Category.id' => $report['TempReport']['category_id'])));
                                        $work = $this->requestAction('Works/get_work_by_id', array('pass' => array('Work.id' => $report['TempReport']['work_id'])));

                                        $sts = "";
                                        if ($report['TempReport']['status'] == 1) {
                                            $sts = 'progress';
                                        } elseif ($report['TempReport']['status'] == 2) {
                                            $sts = 'completed';
                                        } elseif ($report['TempReport']['status'] == 3) {
                                            $sts = 'in-completed';
                                        } elseif ($report['TempReport']['status'] == 4) {
                                            $sts = 'cancelled';
                                        }
                                        ?>				
                                <tr>
                                    <!--edit purpose --->
                            <span id="td_category_<?php echo $report['TempReport']['id'] ?>" style="display:none"><?php echo $report['TempReport']['category_id'] ?></span>
                            <span style="display:none" id="td_status_<?php echo $report['TempReport']['id'] ?>"><?php echo $report['TempReport']['status'] ?></span>
                            <span style="display:none" id="td_start_time_<?php echo $report['TempReport']['id'] ?>"><?php echo date('h:i:a', strtotime($report['TempReport']['start_time'])) ?></span>
                            <span style="display:none" id="td_end_time_<?php echo $report['TempReport']['id'] ?>"><?php echo date('h:i:a', strtotime($report['TempReport']['end_time'])) ?></span>
                                    <?php if ($i == 1) { ?>
                            <span style="display:none" id="td_disabled_<?php echo $report['TempReport']['id'] ?>">disabled</span>
                                    <?php } else { ?>
                            <span style="display:none" id="td_disabled_<?php echo $report['TempReport']['id'] ?>"> </span>
                                    <?php } ?>
                            <!--end-->

                            <td><?php echo $i++ ?></td>
                            <td id="td_projectname_<?php echo $report['TempReport']['id'] ?>"><?php echo htmlspecialchars_decode($report['TempReport']['projectname']) ?></td>
                            <td><?php echo $ctgy['Category']['category'] ?></td>
                            <td id="td_work_id_<?php echo $report['TempReport']['id'] ?>"><?php echo htmlspecialchars_decode($report['TempReport']['work_id'])//echo $work['Work']['work']  ?></td>
                            <td><?php echo date('g:i A', strtotime(strval(str_replace('.', ':', $report['TempReport']['start_time'])))) ?></td>
                            <td><?php echo date('g:i A', strtotime(strval(str_replace('.', ':', $report['TempReport']['end_time'])))) ?></td>
                            <td><?php echo $sts ?></td>
                            <td id="td_comments_<?php echo $report['TempReport']['id'] ?>"><?php echo htmlspecialchars_decode($report['TempReport']['comments']) ?></td>
                            <td>
                                <a href="javascript:edit_row(<?php echo $report['TempReport']['id'] ?>)" title="edit"><span class="icon-edit"></span></a>
                                <a href="javascript:copy_row(<?php echo $report['TempReport']['id'] ?>)" title="copy"><span class="icon-file"></span></a>
                                        <?php
                                        if ($i == 2) {
                                            if (count($reports) == 1) {
                                                echo $this->Html->link('<span class="icon-remove"></span>', array('controller' => 'dailystatus', 'action' => 'delete_row', $report['TempReport']['id']), array('title' => 'Delete Row', 'escape' => false, 'onclick' => "return confirm('Are you sure to delete ?')"));
                                            }
                                        } else {
                                            ?>
                                <a href="javascript:delete_row(<?php echo $report['TempReport']['id'] ?>)" onclick="return confirm('Are you sure to delete ?')" title="delete row"><span class="icon-remove"></span></a>
                                            <?php
                                        }
                                        ?>

                                        <?php //echo $this->Html->link('<span class="icon-edit"></span>',array('controller'=>'dailystatus', 'action'=>'edit_row', $report['TempReport']['id']), array('title'=>'Edit Row', 'escape'=>false));  ?>
                                        <?php /*
                                          if($i == 2)
                                          {
                                          echo  count($reports) == 1 ? $this->Html->link('<span class="icon-remove"></span>',array('controller'=>'dailystatus', 'action'=>'delete_row', $report['TempReport']['id']), array('title'=>'Delete Row', 'escape'=>false, 'onclick'=>"return confirm('Are you sure to delete ?')")) : '';
                                          }
                                          else{
                                          echo $this->Html->link('<span class="icon-remove"></span>',array('controller'=>'dailystatus', 'action'=>'delete_row', $report['TempReport']['id']), array('title'=>'Delete Row', 'escape'=>false, 'onclick'=>"return confirm('Are you sure to delete ?')"));
                                          } */
                                        ?>
                            </td>
                            </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                        <?php $last_row = end($reports); ?>
                    <span style="display:none" id="last_row_end_time"><?php echo date('h:i:a', strtotime($last_row['TempReport']['end_time'])) ?></span>

                    <?php } ?>

                </div>

                <?php if (!empty($meetings)) { ?>
                <div class="row-form">
                    <div><h4 align="center">Today's MOM</h4></div>
                    <table cellpadding="0" cellspacing="0" width="100%" border="1" class="table" id="meeting-index" style="box-shadow:5px 5px #52759B;" bordercolor="#52759B" >
                        <thead>
                            <tr>
                                <th width="6%">No</th>
                                <th width="10%">Meeting Date</th>
                                <th width="64%">Meeting Detailis</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php
                                $i = 1;
                                foreach ($meetings as $meeting):
                                    ?>
                            <tr>
                                <td><?php echo h($i); ?></td>
                                <td><?php echo h($meeting['Meeting']['meeting_date']); ?></td>
                                <td>
                                            <?php
                                            $projects = $this->requestAction('meetings/get_meeting_by_userid_date/' . $this->Session->read('User.id') . '/' . date('Y-m-d', strtotime($meeting['Meeting']['meeting_date'])));

                                            $y = 1;
                                            $count = count($projects);
                                            foreach ($projects as $project) {
                                                if ($project['Meeting']['status'] == 0) {
                                                    echo '<b>' . $y . '. ' . $project['Project']['projectname'] . '</b>&nbsp;<a href="' . $this->base . '/meetings/edit/' . $project['Meeting']['id'] . '"><span class="icon-pencil"></span></a>';
                                                    echo $project['Meeting']['status'] == 0 ? $this->Html->link('<span class="icon-remove"></span>', array('controller' => 'meetings', 'action' => 'meeting_delete_row', $project['Meeting']['id'], 'dailystatus'), array('title' => 'Delete Meeting', 'escape' => false, 'confirm' => 'Are you sure to delete meeting on ' . $project['Project']['projectname'] . '?')) : '';
                                                    echo '<br>';
                                                    echo 'Schedule From: ' . date('h:i a', strtotime($project['Meeting']['meeting_schedule_start'])) . ' To ' . date('h:i a', strtotime($project['Meeting']['meeting_schedule_end'])) . '<br>';
                                                    echo 'Actual Meeting From: ' . date('h:i a', strtotime($project['Meeting']['meeting_actual_start'])) . ' To ' . date('h:i a', strtotime($project['Meeting']['meeting_actual_end'])) . '<br>';

                                                    echo $count != $y ? '<br>' : '';
                                                    $y++;
                                                }
                                            }
                                            ?>
                                </td>
                                <td>
                                            <?php /* ?><a href="<?php echo $this->base?>/admin/meetings/edit/<?php echo $meeting['Meeting']['id'];?>" title="Edit Meeting"><span class="icon-pencil"></span></a><?php */ ?>
                                    <a href="<?php echo $this->base ?>/meetings/meetings_view/<?php echo $meeting['Meeting']['user_id']; ?>/<?php echo date('Y-m-d', strtotime($meeting['Meeting']['meeting_date'])); ?>" title="View Meeting"><span class="isb-text_document"></span></a>
        <?php echo $meeting['Meeting']['status'] == 0 ? $this->Html->link('<span class="icon-remove"></span>', array('controller' => 'meetings', 'action' => 'meeting_delete', $meeting['Meeting']['meeting_date'], 'dailystatus'), array('title' => 'Delete Meeting', 'escape' => false, 'confirm' => 'Are you sure to delete meeting on ' . $meeting['Meeting']['meeting_date'] . '?')) : ''; ?>
                                </td>
                            </tr>
                                    <?php
                                    $i++;
                                endforeach;
                                ?>
                        </tbody>
                    </table>

                </div>
<?php } ?>
            </div>


            <div id="send_div">
                <div class="dr"><span></span></div>

                <div class="row-fluid">                
                    <div align="center" class="span12">
                        <button class="btn btn-small" type="button" id="popup_3">Send</button>
                        <input type="button" id="send-report" value="Send" class="btn" style="display:none;" onclick="location.href = '<?php echo $this->base; ?>/dailystatus/add_daily_report'"/>
                        <a href="<?php echo $this->base; ?>/dailystatus/cancel" title="cancel" onclick="return confirm('Are you sure to cancel the daily report ?')" class="btn btn-small">Cancel</a>

                    </div>
                </div>
            </div>

<?php echo $this->Form->end(); ?>


        </div>
    </div>