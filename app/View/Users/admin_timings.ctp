<style>
    .ui-widget-overlay{
        height: 1368px !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        $("#timings-index").dataTable({
            "iDisplayLength": 10,
            "sPaginationType": "full_numbers",
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": true,
            "bPaginate": true
        });

        $(".default_popup").dialog({
            autoOpen: false,
            modal: true,
            width: 600,
            buttons: {
                "Ok": function () {
                    var dataArr = '';
                    $('input:checked').each(function () {
                        if ($(this).closest('tr[id]').attr('id') != 'header') {
                            dataArr += $(this).closest('tr[id]').attr('id') + ','; // insert rowid's to array
                        }
                    });
                    dataArr = dataArr.slice(0, -1);
                    $('#default-ids').val(dataArr);
                    $("#default-submit").trigger('click');
//                    $(this).dialog("close");
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
        });

        $(".indiv_popup").dialog({
            autoOpen: false,
            modal: true,
            width: 600,
        });
    });

    function open_edit(id) {
        $("#indiv_form_" + id).dialog('open');
        $("#indiv_form_" + id).dialog({
            buttons: {
                "Ok": function () {
                    $("#indiv-submit"+id).trigger('click');
//                    $(this).dialog("close");
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
        });
    }

    function change_multiple()
    {
        var count = $(":checkbox").filter(':checked').length;
        if (count > 0) {
            $("#multi_change").dialog('open');
        } else {
            alert('Please check atleast one checkbox');
        }
    }

</script>

<?php
$hours = array('0' => '00', '1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06', '7' => '07', '8' => '08', '9' => '09', '10' => '10', '11' => '11', '12' => '12');
$minutes = array('00' => '00', '05' => '05', '10' => '10', '15' => '15', '20' => '20', '25' => '25', '30' => '30', '35' => '35', '40' => '40', '45' => '45', '50' => '50', '55' => '55');
$mer = array('am' => 'am', 'pm' => 'pm');
?>

<div class="workplace">

    <div class="row-fluid">
        <div class="span3" style="float:right">
            <button type="button" class="btn btn-block btn-primary" onclick="javascript:change_multiple()">Multi Change</button>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-users"></div>
                <h1>Employees List</h1>
                <div class="clear"></div>
            </div>
            <div class="block-fluid table-sorting">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="timings-index">
                    <thead>
                        <tr id='header'>
                            <th width="5%"><input type="checkbox" name="checkall"/></th>
                            <th width="20%">Employee ID</th>
                            <th width="30%">Name</th>
                            <th width="35%">Designation</th>
                            <th width="10%">Edit Timings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($users as $user):
                            ?>
                            <tr id="<?php echo $user['User']['id'] ?>">
                                <td><input id="checkbox<?php echo $user['User']['id'] ?>" name="checkbox<?php echo $user['User']['id'] ?>" type="checkbox" value="<?php echo $user['User']['id'] ?>" /></td>
                                <td><?php echo h($user['User']['employee_id']); ?></td>
                                <td><?php echo h($user['User']['employee_name']); ?></td>
                                <td><?php echo h($user['User']['designation']); ?></td>
                                <td>
                                    <a class="check-access" href="javascript:open_edit(<?php echo $user['User']['id'] ?>)" title="Edit"><span class="icon-pencil"></span></a>                                 
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

<?php
foreach ($users as $key => $user) {
    $timings = json_decode($user['User']['timings'], true);
    $arr_times = array(
        'office_hours' => array(
            'merid' => false,
            'label' => 'Office Hours',
            'value' => $timings['office_hours'],
        ),
        'total_hours_in_office' => array(
            'merid' => false,
            'label' => 'Total Office Hours',
            'value' => $timings['total_hours_in_office'],
        ),
        'office_start_time' => array(
            'merid' => true,
            'label' => 'Office Start Time',
            'value' => $timings['office_start_time'],
        ),
        'office_end_time' => array(
            'merid' => true,
            'label' => 'Office End Time',
            'value' => $timings['office_end_time'],
        ),
        'excuse_time' => array(
            'merid' => true,
            'label' => 'Late Entry Excuse Time',
            'value' => $timings['excuse_time'],
        ),
        'permission_start_time' => array(
            'merid' => true,
            'label' => 'Late Entry Permission Time',
            'value' => $timings['permission_start_time'],
        ),
        'permission_max_time' => array(
            'merid' => true,
            'label' => 'Late Entry Permission Max Time',
            'value' => $timings['permission_max_time'],
        ),
        'half_day_excuse_time' => array(
            'merid' => true,
            'label' => 'Late Entry Halfday Leave Excuse (Include grace time)',
            'value' => $timings['half_day_excuse_time'],
        ),
        'half_day_grace_time' => array(
            'merid' => false,
            'label' => 'Late Entry Halfday Grace Minutes',
            'value' => $timings['half_day_grace_time'],
        ),
        'late_entry_end_time' => array(
            'merid' => true,
            'label' => 'Late Entry Users Leave Time',
            'value' => $timings['late_entry_end_time'],
        ),
        'permission_hours' => array(
            'merid' => false,
            'label' => 'Permission Hours',
            'value' => $timings['permission_hours'],
        ),
        'report_send_grace_time' => array(
            'merid' => false,
            'label' => 'Report Send Grace Minutes',
            'value' => $timings['report_send_grace_time'],
        ),
        'permission_back_time' => array(
            'merid' => false,
            'label' => 'Min. Office Hours (Less than this hour is halfday leave)',
            'value' => $timings['permission_back_time'],
        ),
    );
    ?>
    <div id="indiv_form_<?php echo $user['User']['id'] ?>" class="dialog b_popup_4 indiv_popup" style="display: none;" title="<?php echo h($user['User']['employee_name']); ?> Office Timings">                                
        <div class="block">

            <?php echo $this->Form->create('Users', array('id' => 'indiv-form-'.$user['User']['id'], 'action' => 'change_timings')) ?>
            <?php echo $this->Form->hidden('ids', array('id' => 'indiv-ids', 'value' => $user['User']['id'])) ?>

            <?php foreach ($arr_times as $key => $times) { ?>
                <div class="row-form">
                    <div class="span2"><?php echo $times['label'] ?>*:</div>
                    <div class="span1">
                        <?php
                        $set_hours = $times['merid'] == true ? date('h', strtotime($times['value'])) : substr($times['value'], 0, 2);
                        ?>
                        <?php echo $this->Form->input('hours', array('class' => 'validate[required]', 'type' => 'select', 'options' => $hours, 'label' => false, 'name' => "data[Users][{$key}][hours]", 'value' => $set_hours)); ?>
                    </div>
                    <div class="span1">
                        <?php echo $this->Form->input('minutes', array('class' => 'validate[required]', 'type' => 'select', 'options' => $minutes, 'label' => false, 'name' => "data[Users][{$key}][minutes]", 'value' => date('i', strtotime($times['value'])))); ?>
                    </div>
                    <?php if ($times['merid']) { ?>
                        <div class="span1">
                            <?php echo $this->Form->input('mer', array('class' => 'validate[required]', 'type' => 'select', 'options' => $mer, 'label' => false, 'name' => "data[Users][{$key}][meridian]", 'value' => date('a', strtotime($times['value'])))); ?>
                        </div>
                    <?php } ?>
                    <div class="clear"></div>
                </div>
            <?php } ?>
            <?php echo $this->Form->submit('submit', array('class' => 'hidden',  'id' => 'indiv-submit'.$user['User']['id'])); ?>
            <?php echo $this->Form->end(); ?>

            <div class="dr"><span></span></div>
        </div>
    </div>
<?php } ?>

<div id="multi_change" class="dialog b_popup_4 default_popup" style="display: none;" title="Default Timings">                                
    <div class="block">
        <?php echo $this->Form->create('Users', array('id' => 'default-form', 'action' => 'change_timings')) ?>
        <?php echo $this->Form->hidden('ids', array('id' => 'default-ids')) ?>

        <?php
        $arr_times = array(
            'office_hours' => array(
                'merid' => false,
                'label' => 'Office Hours',
                'value' => '08:00:00',
            ),
            'total_hours_in_office' => array(
                'merid' => false,
                'label' => 'Total Office Hours',
                'value' => '09:30:00'
            ),
            'office_start_time' => array(
                'merid' => true,
                'label' => 'Office Start Time',
                'value' => '09:30:00'
            ),
            'office_end_time' => array(
                'merid' => true,
                'label' => 'Office End Time',
                'value' => '19:00:00'
            ),
            'excuse_time' => array(
                'merid' => true,
                'label' => 'Late Entry Excuse Time',
                'value' => '10:05:00'
            ),
            'permission_start_time' => array(
                'merid' => true,
                'label' => 'Late Entry Permission Time',
                'value' => '10:30:00'
            ),
            'permission_max_time' => array(
                'merid' => true,
                'label' => 'Late Entry Permission Max Time',
                'value' => '11:30:00'
            ),
            'half_day_excuse_time' => array(
                'merid' => true,
                'label' => 'Late Entry Halfday Leave Excuse (Include grace time)',
                'value' => '11:45:00'
            ),
            'half_day_grace_time' => array(
                'merid' => false,
                'label' => 'Late Entry Halfday Grace Minutes',
                'value' => '00:15:00'
            ),
            'late_entry_end_time' => array(
                'merid' => true,
                'label' => 'Late Entry Users Leave Time',
                'value' => '19:30:00'
            ),
            'permission_hours' => array(
                'merid' => false,
                'label' => 'Permission Hours',
                'value' => '02:00:00'
            ),
            'report_send_grace_time' => array(
                'merid' => false,
                'label' => 'Report Send Grace Minutes',
                'value' => '00:10:00'
            ),
            'permission_back_time' => array(
                'merid' => false,
                'label' => 'Min. Office Hours (Less than this hour is halfday leave)',
                'value' => '06:00:00'
            ),
        );
        ?>

        <?php foreach ($arr_times as $key => $times) { ?>
            <div class="row-form">
                <div class="span2"><?php echo $times['label'] ?>*:</div>
                <div class="span1">
                    <?php
                    $set_hours = $times['merid'] == true ? date('h', strtotime($times['value'])) : substr($times['value'], 0, 2);
                    ?>
                    <?php echo $this->Form->input('hours', array('class' => 'validate[required]', 'type' => 'select', 'options' => $hours, 'label' => false, 'name' => "data[Users][{$key}][hours]", 'value' => $set_hours)); ?>
                </div>
                <div class="span1">
                    <?php echo $this->Form->input('minutes', array('class' => 'validate[required]', 'type' => 'select', 'options' => $minutes, 'label' => false, 'name' => "data[Users][{$key}][minutes]", 'value' => date('i', strtotime($times['value'])))); ?>
                </div>
                <?php if ($times['merid']) { ?>
                    <div class="span1">
                        <?php echo $this->Form->input('mer', array('class' => 'validate[required]', 'type' => 'select', 'options' => $mer, 'label' => false, 'name' => "data[Users][{$key}][meridian]", 'value' => date('a', strtotime($times['value'])))); ?>
                    </div>
                <?php } ?>
                <div class="clear"></div>
            </div>
        <?php } ?>

        <?php echo $this->Form->submit('submit', array('class' => 'hidden', 'id' => 'default-submit')); ?>
        <div class="dr"><span></span></div>
                <?php echo $this->Form->end(); ?>

    </div>
</div>