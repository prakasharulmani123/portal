<script>
$( document ).ready(function() {
	<?php $current_permissions = $this->requestAction('permission/user_get_current_month_permission_new');
       $add_permissions = $this->requestAction('Compensations/add_current_month_permission_new');  
       $count_pr=3;
       $total_pr=$add_permissions + $count_pr;
           ?>
	<?php if($current_permissions >= $count_pr && $current_permissions > $total_pr){?>
		
		var message = 'You have already taken ' + <?php echo $current_permissions ?>  + ' permissions';
		document.getElementById('permission_details').innerHTML = message;
		$("#popup_3").trigger("click");
		
	<?php } ?>
	
	$( "#popup_2" ).click(function() {
		var request = document.getElementById('PermissionRequest').value;
		var date = document.getElementById('PermissionDate').value;
		var reason = document.getElementById('PermissionReason').value;
		
		var from_hours = document.getElementById('PermissionFromHours').value;
		var from_minutes = document.getElementById('PermissionFromMinutes').value;
		var from_merdian = document.getElementById('PermissionFromMer').value;

		var to_hours = document.getElementById('PermissionToHours').value;
		var to_minutes = document.getElementById('PermissionToMinutes').value;
		var to_merdian = document.getElementById('PermissionToMer').value;
		
		document.getElementById('request').innerHTML = request;
		document.getElementById('date').innerHTML = date;
		document.getElementById('reason').innerHTML = reason;		
		document.getElementById('from_time').innerHTML = from_hours + ':' + from_minutes + ' ' + from_merdian;		
		document.getElementById('to_time').innerHTML = to_hours + ':' + to_minutes + ' ' + to_merdian;		
	});

	$(".b_popup_4").dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		buttons:{                            
			"Ok": function() {
				window.location.href = '<?php echo $this->base?>/leave/leaveform';
				return false;
			},
			Cancel: function() {
				$( ".b_popup_2").dialog( "close" );
				$( this ).dialog( "close" );
			}
		}
	});

	var agreed = false; //has to be global

	$(".b_popup_3").dialog({
   		autoOpen: false,
		modal: true,
		width: 300,
		buttons:{                            
			"Ok": function() {
				agreed = false;
				window.location.href = '<?php echo $this->base?>/leave/leaveform';
				return false;
			},
		},
		close: function(ev, ui) { 
			window.location.href = '<?php echo $this->base?>/permission';
		}
//		beforeclose: function () {
//			return agreed;
//		}
	});
	
	$( "#popup_submit" ).click(function() {
		document.permission.submit();
	});
});

$(function() {
	$("#PermissionDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth:true,
		changeYear:true,
	}).datepicker("setDate", new Date());;
});

function validSubmit(){
	var request = document.getElementById('PermissionRequest').value;
	var date = document.getElementById('PermissionDate').value;
	var reason = document.getElementById('PermissionReason').value;
	
	var from_hours = document.getElementById('PermissionFromHours').value;
	var from_minutes = document.getElementById('PermissionFromMinutes').value;
	var from_merdian = document.getElementById('PermissionFromMer').value;

	var to_hours = document.getElementById('PermissionToHours').value;
	var to_minutes = document.getElementById('PermissionToMinutes').value;
	var to_merdian = document.getElementById('PermissionToMer').value;
	
	$("#validation").submit(function(){
		
		if(request != '' && date != '' && reason != '' && from_hours != '' && from_minutes != '' && from_merdian != '' && to_hours != '' && to_minutes != '' && to_merdian != '')
		{
			<?php if($current_permissions < 3){?>
				if(from_merdian == 'pm'){
					if(from_hours != 12){
						from_hours = parseInt(from_hours) + 12 ; 
					}
				}
	
				if(to_merdian == 'pm'){
					if(to_hours != 12){
						to_hours = parseInt(to_hours) + 12; 
					}
				}
				
				from_hours = from_hours + '.' + from_minutes;
				to_hours = to_hours + '.' + to_minutes;
				
				if((parseFloat(to_hours) - parseFloat(from_hours) > 2) || (parseFloat(to_hours) - parseFloat(from_hours) < 0)){
					from_hours = from_minutes = from_merdian = to_hours = to_minutes = to_merdian = '';
					
					$("#popup_4").trigger("click");
					return false
				}
				
				from_hours = from_minutes = from_merdian = to_hours = to_minutes = to_merdian = '';
			<?php } ?>
			
			$("#popup_2").trigger("click");
			return false;
		}
	});
}
</script>

    <div class="dialog b_popup_4"  id="b_popup_4" style="display: none;" title="Notification">
        <p align="center">More Than 2 Hours Permissions considered as Half a day Leave <br /><b> This is not a Permission , You can't send leave request here </b><br /> Are sure to Redirect to leave Form and <span style="color:red">Re-Enter the Leave ?</span></p>                
    </div>

    <div class="dialog b_popup_3" id="b_popup_3" style="display: none;" title="Notification">
        <p align="left"><b><span id="permission_details"></span></b></p>                
        <p>You can not send permissions request !!<br /> Are sure to Redirect to leave Form ?</p>
    </div>
    
  <div class="dialog b_popup_2" id="b_popup_2" style="display: none;" title="Confirmation">                                
    <div class="block">
    <p>Requisition : <span id="request"></span></p>
    <p>Permission Date : <b><span id="date"></span></b></p>
    <p>From : <b><span id="from_time"></span></b></p>
    <p>To : <b><span id="to_time"></span></b></p>
    <p>Reason : <span id="reason"></span></p>
    <p align="center"><button class="btn" type="button" id="popup_submit">Submit</button></p>
    </div>
  </div>                                        
  
<div class="workplace">
<?php echo $this->Form->create('Permission', array('id' => 'validation', 'name'=>"permission")); ?>
<?php echo $this->Form->input('permission_leave', array('type'=>'hidden', 'value'=>1)); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-list"></div>
        <h1>Permission Request</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
      
        <div class="row-form">
          <div class="span3">Employee Name:</div>
          <div class="span9">
           <?php echo $users['User']['employee_name'];
          ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Employee Id:</div>
          <div class="span9">
           <?php echo $users['User']['employee_id']; ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Requisition*:</div>
          <div class="span3">
           <?php echo $this->Form->input('request', array('type'=>'select', 'options'=>array('past'=>'Past Permission Requisition', 'current'=>'Current Permission Requisition'), 'class' => 'validate[required]','label'=>false, 'empty'=>'Select Requisition')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Date*:</div>
          <div class="span2">
           <?php echo $this->Form->input('date', array('type'=>'text','class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <?php
		$hours = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12');
		$minutes = array('00'=>'00', '05'=>'05', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30', '35'=>'35', '40'=>'40', '45'=>'45', '50'=>'50', '55'=>'55');
		$mer = array('am'=>'am','pm'=>'pm');
		?>
        
        <div class="row-form">
          <div class="span3">From Time*:</div>
            <?php echo $this->Form->input('from_time', array('class' => 'validate[required]','type'=>'hidden')); ?>
          <div class="span1">
            <?php echo $this->Form->input('from_hours', array('class' => 'validate[required]','type'=>'select', 'options'=>$hours, 'label'=>false, 'empty'=>'hours', 'name'=>'data[Permission][from][hours]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('from_minutes', array('class' => 'validate[required]','type'=>'select', 'options'=>$minutes, 'label'=>false, 'empty'=>'minutes', 'name'=>'data[Permission][from][minutes]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('from_mer', array('class' => 'validate[required]','type'=>'select', 'options'=>$mer, 'label'=>false, 'empty'=>'meridian', 'name'=>'data[Permission][from][meridian]')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">To Time*:</div>
            <?php echo $this->Form->input('to_time', array('class' => 'validate[required]','type'=>'hidden')); ?>
          <div class="span1">
            <?php echo $this->Form->input('to_hours', array('class' => 'validate[required]','type'=>'select', 'options'=>$hours, 'label'=>false, 'empty'=>'hours', 'name'=>'data[Permission][to][hours]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('to_minutes', array('class' => 'validate[required]','type'=>'select', 'options'=>$minutes, 'label'=>false, 'empty'=>'minutes', 'name'=>'data[Permission][to][minutes]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('to_mer', array('class' => 'validate[required]','type'=>'select', 'options'=>$mer, 'label'=>false, 'empty'=>'meridian', 'name'=>'data[Permission][to][meridian]')); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Reason for Permission*:</div>
          <div class="span9">
           <?php echo $this->Form->input('reason', array('class' => 'validate[required]','label'=>false)); ?>
           <span style="color:red; font-size:14px">*note: just type the message only</span>
          </div>
          <div class="clear"></div>
        </div>

      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
  <div class="row-fluid">                
    <div align="center" class="span12">
        <button class="btn" type="button" id="popup_4" style="display:none">Model</button>
        <button class="btn" type="button" id="popup_3" style="display:none;">Submit</button>
        <button class="btn" type="button" id="popup_2" style="display:none;">Submit</button>
      <input type="submit" name="save" id="save" value="Submit" class="btn" onclick='validSubmit()'/>
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/permission'" />          
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

