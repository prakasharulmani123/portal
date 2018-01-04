<style>
/*
.ui-datepicker-calendar {
    display: none;
    }
	*/
</style>

<script>
$(document).ready(function() {

	$("#b_popup_1").dialog({
		width: 500
	});
	
        $("#b_popup_2").dialog({
            width: 700
        });

        $("#b_popup_3").dialog({
            width: 900
        });

	$("#dailyreport-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": true,
		"bPaginate": false
	});
	
	<?php 
	if(empty($all['month'])){
		$year = date('Y'); 
		$month = date('m') - 1; 
	}
	else{
		$year = $all['year']; 
		$month = $all['month'] - 1; 
	}
	?>
	
    $('#DailyStatusMonth').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'MM yy',
		defaultDate: new Date(<?php echo $year?>, <?php echo $month?>, 1),
        onClose: function(dateText, inst) { 
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
		    $(".ui-datepicker-calendar").show();
        }
    });
	
	$("#DailyStatusMonth").focus(function () {
	  $(".ui-datepicker-calendar").hide();
   });

	sunday_count = $('#sunday_count').html();
	offical_leave_count = $('#official_leave_count').html();
	leave_count = $('#leave_count').html();
	half_day_count = $('#half_day_count').html();
	permission_count = $('#permission_count').html();
	no_record_count = $('#no_record_count').html();
	
	$('#td_sunday_count').html('<b>' + sunday_count + '</b>');
	$('#td_official_leave_count').html('<b>' + offical_leave_count + '</b>');
	$('#td_leave_count').html('<b>' + leave_count+ '</b>');
	$('#td_half_day_count').html('<b>' + half_day_count + '</b>');
	$('#td_permission_count').html('<b>' + permission_count + '</b>');
	$('#td_no_record_count').html('<b>' + no_record_count + '</b>');
});
</script>

<?php $late_entries = $this->requestAction('late_entries/get_late_entry_by_user_id/'.$all['user_id']);?>
<?php $late_fee = 0;?>

<?php if(!empty($late_entries)):?>
  <div class="dialog" id="b_popup_1" style="display: none;" title="Late Entries">                                
    <div class="block">
    <table border="1" width="100%">
    <thead>
    <th width="30%">Date</th>
    <th width="40%">Late Details</th>
    <th width="30%">Amount</th>
    </thead>
    <tbody>
    <?php foreach($late_entries as $late_entry){?>
    <?php
		$start_time = date('d-m-Y', strtotime($late_entry['LateEntry']['date'])).' 10:35:00';
		$datetime1 = new DateTime($start_time);
		$datetime2 = new DateTime($late_entry['LateEntry']['created']);
		$interval = $datetime1->diff($datetime2);
	?>
    <tr>
    <td><?php echo date('Y-m-d', strtotime($late_entry['LateEntry']['date']))?></td>
    <td><?php echo h($interval->format('%h').' hours '.$interval->format('%i').' minutes '.$interval->format('%s').' seconds') ?></td>
    <td><?php echo $late_entry['LateEntry']['amount'] == 0 ? 'warning' : $late_entry['LateEntry']['amount']?></td>
    </tr>
    <?php $late_fee += $late_entry['LateEntry']['amount'];} ?>
    </tbody>
    </table>
    </div>
  </div>                                        
<?php endif ?>

<?php $user_complaints = $this->requestAction('user_complaints/get_user_their_complaint_by_user_id/' . $all['user_id']); ?>
<?php $fine = 0; ?>

<?php if (!empty($user_complaints)): ?>
    <div class="dialog" id="b_popup_2" style="display: none;" title="Complaints">                                
        <div class="block">
            <table border="1" width="100%">
                <thead>
                <!--<th width="20%">Person Name</th>-->
                <th width="10%">Date</th>
                <th width="50%">Complaint</th>
                <th width="10%">Fine Amount</th>
                </thead>
                <tbody>
                    <?php foreach ($user_complaints as $user_complaint) { ?>
                        <tr>
                        <!--<td><?php echo h($user_complaint['Sender']['employee_name']); ?></td>-->
                            <td><?php echo date('Y-m-d', strtotime($user_complaint['UserComplaint']['created'])) ?></td>
                            <td><?php
                                echo h($user_complaint['UserComplaint']['reason']);
                                if ($user_complaint['UserComplaint']['file']) {
                                    echo '<br />';
                                    echo $this->Html->link('(file attached)', Router::url('/' . $user_complaint['UserComplaint']['file'], true), array('title' => 'View File', 'escape' => false, 'target' => '_blank')) . ' &nbsp;';
                                }
                                ?>
                            </td>
                            <td><?php echo h($user_complaint['UserComplaint']['fine_amount']); ?></td>
                        </tr>
                        <?php $fine += $user_complaint['UserComplaint']['fine_amount'];
                    } ?>
                </tbody>
            </table>
        </div>
    </div>                                        
<?php endif ?>

<?php $my_complaints = $this->requestAction('user_complaints/get_user_my_complaint_by_user_id/' . $all['user_id']); ?>
<?php $earnings = 0; ?>
<?php if (!empty($my_complaints)): ?>
    <div class="dialog" id="b_popup_3" style="display: none;" title="Complaints">                                
        <div class="block">
            <table border="1" width="100%">
                <thead>
                <th width="20%">Person Name</th>
                <th width="10%">Date</th>
                <th width="50%">Complaint</th>
                <th width="10%">Earned Amount</th>
                </thead>
                <tbody>
                    <?php foreach ($my_complaints as $my_complaint) { ?>
                        <tr>
                        <td><?php echo h($my_complaint['Receiver']['employee_name']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($my_complaint['UserComplaint']['created'])) ?></td>
                            <td><?php
                                echo h($my_complaint['UserComplaint']['reason']);
                                if ($my_complaint['UserComplaint']['file']) {
                                    echo '<br />';
                                    echo $this->Html->link('(file attached)', Router::url('/' . $my_complaint['UserComplaint']['file'], true), array('title' => 'View File', 'escape' => false, 'target' => '_blank')) . ' &nbsp;';
                                }
                                ?>
                            </td>
                            <td><?php echo h($my_complaint['UserComplaint']['fine_amount']); ?></td>
                        </tr>
                        <?php $earnings += $my_complaint['UserComplaint']['fine_amount'];
                    } ?>
                </tbody>
            </table>
        </div>
    </div>                                        
<?php endif ?>

<div style="margin:10px 50px 10px 50px;">
<?php echo $this->Form->create('DailyStatus'); ?>
    <?php 
		$all_user = array();
		
		foreach($users as $user){
			$all_user[$user['User']['id']] = $user['User']['employee_name'];
		}
		
		$month = '01-'.$all['month'].'-'.$all['year'];
		
		if(empty($all['month'])){
			$value = "";
		}
		else{
			$value = date('F Y', strtotime($month));
		}
		
		 ?>
        <b><?php echo " Employee : "?></b>
      <?php echo $this->Form->input('user_id', array('label' => false, 'div' => false, 'class' => 'form-control','options' => array($all_user), 'selected' => $all['user_id'], 'style'=>'width:200px; margin-top:6px;')); ?>
        <b><?php echo " Month : "?></b>
      <?php echo $this->Form->input('month', array('label' => false, 'div' => false, 'class'=>'form-control', 'value'=> $value, 'style'=>'width:200px; margin-top:6px;')); ?>
      <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?></td>
      <?php echo $this->Html->link('Reset', array('controller' => 'dailystatus', 'action' => 'reset_month', 'admin' => true), array('class' => 'btn btn-danger')); ?>
  <?php echo $this->Form->end(); ?>
  <div class="clear"></div>
</div>

<?php if($all['user_id'] != '' && $all['month'] != '' && $all['year'] != '') {
	$employee = $this->requestAction('users/get_user',array('pass' => array($all['user_id']))); 
	$leaves_month = $this->requestAction('leave/get_current_month_leave_approved/'.$all['user_id'].'/'.$all['month'].'/'.$all['year']);
	$holidays_month = $this->requestAction('holidays/get_holidays_per_month/'.$all['month'].'/'.$all['year']);
	$leaves = $leaves_month;
	
	$check_sun_day = false;
	$holidays = array();
	
	foreach($holidays_month as $holiday_month){
		$holidays[$holiday_month['Holiday']['name'].','.$holiday_month['Holiday']['date']] = $holiday_month['Holiday']['date'];
	}
	
	$permissions = $this->requestAction('permission/get_permission_approved_per_month/'.$all['user_id'].'/'.$all['month'].'/'.$all['year']);
	?>
<div class="workplace">

  <div class="row-fluid">
    <div align="left" class="span2">
        <input type="button" name="export" id="export" value="Export" class="btn btn-primary" onclick="location.href='<?php echo $this->base; ?>/admin/dailystatus/export_to_csv'" />
    </div>

    <div align="left" class="span8" style="margin-bottom:10px;">
    <table>
        <tr>
            <td width="30" style="background-color:#B4FF80; border:1px solid black;" id="td_sunday_count" align="center">&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td><span style="font-size:18px;"> Sunday</span></td>
            <td width="10">&nbsp;</td>
            <td width="30" style="background-color:#FCEB77; border:1px solid black;" id="td_official_leave_count" align="center">&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td><span style="font-size:18px;"> Official Leave</span></td>
            <td width="10">&nbsp;</td>
            <td width="30" style="background-color:#F84848; border:1px solid black;" id="td_leave_count" align="center">&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td><span style="font-size:18px;"> Leave</span></td>
            <td width="10">&nbsp;</td>
            <td width="30" style="background-color:#E47296; border:1px solid black;" id="td_half_day_count" align="center">&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td><span style="font-size:18px;"> Half Day</span></td>
            <td width="10">&nbsp;</td>
            <td width="30" style="background-color:#FFC0CB; border:1px solid black;" id="td_permission_count" align="center">&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td><span style="font-size:18px;"> Permission</span></td>
            <td width="10">&nbsp;</td>
            <td width="30" style="background-color:#D1C0A6; border:1px solid black;" id="td_no_record_count" align="center">&nbsp;</td>
            <td width="10">&nbsp;</td>
            <td><span style="font-size:18px;"> No Record</span></td>
        </tr>
    </table>
  </div>
            <div align="left" class="span2">
                <?php if ($fine != 0) { ?>
                    <button class="badge badge-important" id="popup_2">Fine : Rs.<?php echo $fine ?></button>
                <?php } ?>
                <?php if ($earnings != 0) { ?>
                    <button class="badge badge-info" id="popup_3">Earning : Rs.<?php echo $earnings ?></button>
                <?php } ?>
                <?php if ($fine != 0 && $earnings != 0) { ?>
                    <button class="badge badge-success">Balance : Rs.<?php echo $fine - $earnings ?></button>
                <?php } ?>
                <?php if ($late_fee != 0) { ?>
                    <button class="badge badge-important" id="popup_1">Late Fee : Rs.<?php echo $late_fee ?></button>
                <?php } ?>
            </div>
  </div>
  
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-calendar"></div>
        <h1>Monthly Reports</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="dailyreport-index">
          <thead>
            <tr>
              <th width="5%">No</th>
              <th width="10%">Date</th>
              <th width="10%">Day</th>
              <th width="40%">Project Name</th>
              <th width="10%">Total Hours</th>
              <th width="10%">Break Hours</th>
              <th width="10%">Worked Hours</th>
              <th width="5%">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
			$row=1; 

			$half_day = '';
			$sunday_count = 0;
			$official_leave_count = 0;
			$leave_count = 0;
			$half_day_count = 0;
			$permission_count = 0;
			$no_record_count = 0;
			
			foreach ($dailyreports as $dailyreport):  
				$color = '';
				$tooltip_color = '#4F7298';
				
				$worked_hours = 0;
				$projects = "";
				$hours = 0;
				$break_hours = 0;
				
				$check_sunday = false;
				$check_leave = false;
				$check_holiday = false;
				$check_working_day = false;
				$check_permission = false;
				$half_day_showed = false;
				$permission_showed = false;
				$no_record_hide = false;
				
				date('D', strtotime($dailyreport)) == 'Sun' ? $check_sunday = true : $check_sunday = false;
				
				if(!empty($leaves)){
					if(array_key_exists(date('Y-m-d', strtotime($dailyreport)),$leaves)){
						$days = array_search(date('Y-m-d', strtotime($dailyreport)), $leaves);
						$check_leave = true;
						$leave_row = $this->requestAction('leave/get_leave_by_userid_date/'.$all['user_id'].'/'.$dailyreport);
						if($leave_row['SubLeave'][0]['day'] == 0.5){
							$tooltip_color = '#E47296';
						}
						else{
							$tooltip_color = '#F84848';
						}
					}
				}

				if(!empty($holidays)){
					if(in_array(date('Y-m-d', strtotime($dailyreport)),$holidays)){
						$holiday_name = str_replace(','.$dailyreport,' ',array_search(date('Y-m-d', strtotime($dailyreport)), $holidays));
						$check_holiday = true;
						$tooltip_color = 'yellow';
					}
				}
				
				if(!empty($permissions)){
					if(in_array(date('Y-m-d', strtotime($dailyreport)),$permissions)){
						$check_permission = true;
						$tooltip_color = 'pink';
					}
				}
				
				if($check_sunday == true){
					$tooltip_color = '#B4FF80';
				}
				
				$reports = $this->requestAction('daily_status/get_reports_by_id_and_date/'.$all['user_id'].'/'.$dailyreport);
				!empty($reports) ? $check_working_day = true : $check_working_day = false;
/*				
				if($check_working_day == true){
					if($check_leave == true){
						$tooltip_color = '#E47296';
					}
				}
*/				if($check_working_day == false && $check_sunday == false && $check_holiday == false && $check_leave == false){
					$tooltip_color = '#D1C0A6';
				}
				
				foreach($reports as $report):
					
					$start_time = strtotime($report['DailyStatus']['start_time']);
					$end_time = strtotime($report['DailyStatus']['end_time']);
					
					$datetime1 = new DateTime($report['DailyStatus']['start_time']);
					$datetime2 = new DateTime($report['DailyStatus']['end_time']);
					$interval = $datetime1->diff($datetime2);
					$elapsed = $interval->format('%h hour %i minute');
					$hours += ($interval->format('%h')*60)+($interval->format('%i'));
					
					if($report['DailyStatus']['projectname']){
						$projects .= $report['DailyStatus']['projectname'] .' , ';
					}
	
					if($report['DailyStatus']['category_id'] != 23 && $report['DailyStatus']['category_id'] != 22 && $report['DailyStatus']['category_id'] != 24){
						$worked_hours += ($interval->format('%h')*60)+($interval->format('%i'));
					}
					elseif($report['DailyStatus']['category_id'] != 24){
						$break_hours += ($interval->format('%h')*60)+($interval->format('%i'));
					}
					
				endforeach;
	
				$day_num=date("j", strtotime($dailyreport)); 
				$month_num = date("m", strtotime($dailyreport)); 
				$year = date("Y", strtotime($dailyreport)); 
				
				$date_today = getdate(mktime(0,0,0,$month_num,1,$year)); 
				$month_name = $date_today["month"]; 
				$first_week_day = $date_today["wday"]; 
				
				$cont = true;
				$today = 27; 
				while (($today <= 32) && ($cont)) 
				{
					$date_today = getdate(mktime(0,0,0,$month_num,$today,$year));
					if ($date_today["mon"] != $month_num)
					{
						$lastday = $today - 1; 
						$cont = false; 
					}
					$today++;
				} 
			
				$txt = "<table cellspacing=0 cellpadding=5 frame='all' rules='all' style='border:#808080 1px solid;'>
				<caption><b>$month_name $year</b></caption>
				<tr align=left><th>Su</th><th>M</th><th>Tu</th><th>W</th><th>Th</th><th>F</th><th>Sa</th></tr>";
				
				$day = 1; 
				$wday = $first_week_day; 
				$firstweek = true; 
				while ( $day <= $lastday) 
					{
					if ($firstweek) 
					{
						$txt .= "<tr align=left>";
						for ($i=1; $i<=$first_week_day; $i++)
						{
							$txt .= "<td> </td>"; 
						}
						$firstweek = false; 
					}
					
					if ($wday==0) 
					$txt .= "<tr align=left>";
					$txt .= "<td";
					if($day==$day_num) $txt .= " bgcolor='".$tooltip_color."'"; 
						$txt .= ">$day</td>";
					if ($wday==6)
						$txt .= "</tr>"; 
						
						$wday++; 
						$wday = $wday % 7; 
						$day++; 
				}
				
				while($wday <=6 ) 
				{
					$txt .= "<td> </td>"; 
					$wday++;
				}
				$txt .= "</tr></table>";
				
				?>
            <!----------Daily Reports------------------>
            <?php 
			if($check_working_day == true){ 
				if($check_permission == true){
					$permission_row = $this->requestAction('permission/get_permission_by_userid_date/'.$all['user_id'].'/'.$dailyreport);
					$datetime1 = new DateTime($permission_row['Permission']['from_time']);
					$datetime2 = new DateTime($permission_row['Permission']['to_time']);
					$interval = $datetime1->diff($datetime2);
					$hours = ($interval->format('%h')*60)+($interval->format('%i'));
					
					$color='style="background-color:pink"';
					$permission_count++;
					
					$permission_showed = true;
				}
				else{
					$color='';
				}
				
				//for half a day
				if($check_leave == true){
					$leave_row = $this->requestAction('leave/get_leave_by_userid_date/'.$all['user_id'].'/'.$dailyreport);
					if($leave_row['SubLeave'][0]['day'] == 0.5){
						
						$color='style="background-color:#E47296"';
						$half_day_count++;
						$half_day_showed = true;
					}
				}
/*				if($check_leave == true){
					$color='style="background-color:#E47296"';
					$half_day_count++;
				}
*/				?>			          
             <tr>
              <td <?php echo $color != '' ? $color : ''?>><?php echo h($row++); ?></td>
              <td <?php echo $color != '' ? $color : ''?>class="ttRC" title="<?php echo $txt?>"><?php echo date('d-m-Y',strtotime($dailyreport)); echo $check_permission == true ? '&nbsp;(Permission : '.gmdate("H:i", ($hours* 60)).' hour)' : ''; echo $check_leave == true ? '&nbsp;(Half a day Leave)' : ''?></td>
              <td <?php echo $color != '' ? $color : ''?>><?php echo date('l',strtotime($dailyreport)); ?></td>
              <td <?php echo $color != '' ? $color : ''?>><?php echo rtrim($projects, ' , '); ?></td>
              <td <?php echo $color != '' ? $color : ''?>><?php echo gmdate("H:i", ($hours* 60));?></td>
              <td <?php echo $color != '' ? $color : ''?>><?php echo gmdate("H:i", ($break_hours* 60));?></td>
              <td <?php echo $color != '' ? $color : ''?>><b><?php echo gmdate("H:i", ($worked_hours* 60));?></b></td>
              <td <?php echo $color != '' ? $color : ''?>><a href="<?php echo $this->base?>/admin/dailystatus/view/<?php echo $reports[0]['DailyStatus']['id']?>" title="View Report"><span class="isb-text_document"></span></a></td>
            </tr>
            <?php $check_leave == true ? $check_leave = false : '';}?>
            
            <!----------Check Permission------------------>
            <?php if($check_permission == true && $permission_showed == false){ 
			$permission_row = $this->requestAction('permission/get_permission_by_userid_date/'.$all['user_id'].'/'.$dailyreport);
					$datetime1 = new DateTime($permission_row['Permission']['from_time']);
					$datetime2 = new DateTime($permission_row['Permission']['to_time']);
					$interval = $datetime1->diff($datetime2);
					$hours = ($interval->format('%h')*60)+($interval->format('%i'));?>
             <tr>
              <td style="background-color:pink"><?php echo h($row++); ?></td>
              <td style="background-color:pink"class="ttRC" title="<?php echo $txt?>"><?php echo date('d-m-Y',strtotime($dailyreport)); echo '&nbsp;(Permission : '.gmdate("H:i", ($hours* 60)).' hour)';?></td>
              <td style="background-color:pink"><?php echo date('l',strtotime($dailyreport)); ?></td>
              <td style="background-color:pink"><?php echo $permission_row['Permission']['reason']?></td>
              <td style="background-color:#D1C0A6">No record</td>
              <td style="background-color:#D1C0A6">No record</td>
              <td style="background-color:#D1C0A6"><b>No record</b></td>
              <td style="background-color:#D1C0A6">--</td>
            </tr>
            <?php $no_record_hide = true; $permission_count++;$no_record_count++;} ?>
            
            <!----------Check Sunday------------------>
            <?php if($check_sunday == true){?>
            <tr>
              <td style="background-color:#B4FF80;"><?php echo h($row++); ?></td>
              <td style="background-color:#B4FF80;" class="ttRC" title="<?php echo $txt?>"><?php echo date('d-m-Y',strtotime($dailyreport)); ?></td>
              <td style="background-color:#B4FF80;"><?php echo date('l',strtotime($dailyreport)); ?></td>
              <td style="background-color:#B4FF80;"><?php echo 'Sunday'?></td>
              <td style="background-color:#B4FF80;"></td>
              <td style="background-color:#B4FF80;"></td>
              <td style="background-color:#B4FF80;"><b><?php echo 'Sunday'?></b></td>
              <td style="background-color:#B4FF80;"></td>
            </tr>
            <?php $sunday_count++;} ?>

            <!----------Check Holiday------------------>
            <?php if($check_holiday == true){?>
            <tr>
              <td style="background-color:#FCEB77;"><?php echo h($row++); ?></td>
              <td style="background-color:#FCEB77;" class="ttRC" title="<?php echo $txt?>"><?php echo date('d-m-Y',strtotime($dailyreport)); ?></td>
              <td style="background-color:#FCEB77;"><?php echo date('l',strtotime($dailyreport)); ?></td>
              <td style="background-color:#FCEB77;"><?php echo $holiday_name?></td>
              <td style="background-color:#FCEB77;"></td>
              <td style="background-color:#FCEB77;"></td>
              <td style="background-color:#FCEB77;"><b><?php echo $holiday_name?></b></td>
              <td style="background-color:#FCEB77;"></td>
            </tr>
            <?php $official_leave_count++;} ?>
            
            <?php if($check_leave == true){?>
            <?php $leave_row = $this->requestAction('leave/get_leave_by_userid_date/'.$all['user_id'].'/'.$dailyreport);?>
            <?php
				if($leave_row['SubLeave'][0]['day'] == 1){
			?>
            <!----------Check Leave------------------>
            <tr>
              <td style="background-color:#F84848"><?php echo h($row++);?></td>
              <td style="background-color:#F84848" class="ttRC" title="<?php echo $txt?>"><?php echo date('d-m-Y',strtotime($dailyreport)) ?></td>
              <td style="background-color:#F84848"><?php echo date('l', strtotime($dailyreport))?></td>
              <td style="background-color:#F84848"><?php echo $leave_row['Leave']['reason'];?></td>
              <td style="background-color:#F84848">--</td>
              <td style="background-color:#F84848">--</td>
              <td style="background-color:#F84848"><b><?php echo 'Leave'?></b></td>
              <td style="background-color:#F84848">--</td>
            </tr>
            <?php $leave_count++; ?>
			<?php }elseif($leave_row['SubLeave'][0]['day'] == 0.5 && $half_day_showed == false){?>
            <!----------Check Half a day Leave------------------>
            <tr>
              <td style="background-color:#E47296"><?php echo h($row++);?></td>
              <td style="background-color:#E47296" class="ttRC" title="<?php echo $txt?>"><?php echo date('d-m-Y',strtotime($dailyreport)) ?>&nbsp;(Half a day Leave)</td>
              <td style="background-color:#E47296"><?php echo date('l', strtotime($dailyreport))?></td>
              <td style="background-color:#E47296"><?php echo $leave_row['Leave']['reason'];?></td>
              <td style="background-color:#D1C0A6">No record</td>
              <td style="background-color:#D1C0A6">No record</td>
              <td style="background-color:#D1C0A6"><b><?php echo 'Hal a Day Leave'?></b></td>
              <td style="background-color:#D1C0A6">--</td>
            </tr>
			<?php $half_day_count++;$no_record_count++;}?>
            <?php } ?>
            
            <!----------No Record------------------>
            <?php if($check_working_day == false && $check_sunday == false && $check_holiday == false && $check_leave == false && $no_record_hide == false){?>
            <tr >
              <td style="background-color:#D1C0A6"><?php echo h($row++); ?></td>
              <td style="background-color:#D1C0A6" class="ttRC" title="<?php echo $txt?>"><?php echo date('d-m-Y',strtotime($dailyreport)) ?></td>
              <td style="background-color:#D1C0A6"><?php echo date('l', strtotime($dailyreport))?></td>
              <td style="background-color:#D1C0A6">No record</td>
              <td style="background-color:#D1C0A6">No record</td>
              <td style="background-color:#D1C0A6">No record</td>
              <td style="background-color:#D1C0A6">No record</td>
              <td style="background-color:#D1C0A6">--</td>
            </tr>
            <?php $no_record_count++;} ?>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div style="display:none;">
        <span id="sunday_count"><?php echo $sunday_count?></span>
        <span id="official_leave_count"><?php echo $official_leave_count?></span>
        <span id="leave_count"><?php echo $leave_count?></span>
        <span id="half_day_count"><?php echo $half_day_count?></span>
        <span id="permission_count"><?php echo $permission_count?></span>
        <span id="no_record_count"><?php echo $no_record_count?></span>
        </div>
        <div class="clear"></div>
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
</div>
<?php } ?>
