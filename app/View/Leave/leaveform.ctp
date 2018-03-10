<script>
$( document ).ready(function() {
	$( "#popup_3" ).click(function() {
		var request = document.getElementById('LeaveRequest').value;
		var date = document.getElementById('LeaveDate').value;
		var reason = document.getElementById('LeaveReason').value;
		var days = document.getElementById('LeaveDays').value;
		
		document.getElementById('request').innerHTML = request;
		document.getElementById('date').innerHTML = date;
		document.getElementById('reason').innerHTML = reason;		
		document.getElementById('days').innerHTML = days;		
	});

	$( "#popup_submit" ).click(function() {
		document.leaveform.submit();
	});
});

$(function() {
	$("#LeaveDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth:true,
		changeYear:true
	}).datepicker("setDate", new Date());;
});

function validSubmit(){
	var request = document.getElementById('LeaveRequest').value;
	var date = document.getElementById('LeaveDate').value;
	var reason = document.getElementById('LeaveReason').value;
	
	$("#validation").submit(function(){
		if(request != '' && date != '' && reason != '')
		{
			$("#popup_3").trigger("click");
			return false;
		}
	});
}
</script>

  <div class="dialog" id="b_popup_3" style="display: none;" title="Confirmation">                                
    <div class="block">
    <p>Requistion : <span id="request"></span></p>
    <p>Leave Date : <span id="date"></span></p>
    <p>Days : <span id="days"></span></p>
    <p>Reason : <span id="reason"></span></p>
    <p align="center"><button class="btn" type="button" id="popup_submit">Submit</button></p>
    </div>
  </div>                                        
  
<div class="workplace">
<?php echo $this->Form->create('Leave', array('id' => 'validation', 'name'=>"leaveform")); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-target"></div>
        <h1>Leave Form</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
      
        <div class="row-form">
          <div class="span3">Employee Name:</div>
          <div class="span9">
           <?php echo $users['User']['employee_name']; ?>
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
           <?php echo $this->Form->input('request', array('type'=>'select', 'options'=>array('past'=>'Past Leave Requisition', 'current'=>'Current Leave Requisition'), 'class' => 'validate[required]','label'=>false, 'empty'=>'Select Requisition')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Leave Date*:</div>
          <div class="span2">
           <?php echo $this->Form->input('date', array('type'=>'text','class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Leave Days*:</div>
          <div class="span2">
          <?php $days = array('0.5'=>'Half day', '1'=>'One day', '1.5'=>'One and half days', '2'=>'Two days', '2.5'=>'Two and half days', '3'=>'Three days');?>
           <?php echo $this->Form->input('days', array('type'=>'select', 'options'=>$days, 'class' => 'validate[required]','label'=>false, 'empty'=>'Select days')); ?>
          </div>
          <div class="clear"></div>
        </div>


<?php if($lists>0){
    ?>
        <div class="row-form" id="checkbox_disable">

          <div class="span3">Do you want to take compensation leave:</div>
          <div class="span2">
            <?php 
            $request_path = $_SERVER['REQUEST_URI'];
            $exp = explode('/', $request_path);
             $ex=$exp[2];
               if($ex=="leaveform")
               {
                    $checked= '';
               }else{
                    $checked= 'checked';
               }
            echo $this->Form->input('status', array('type' => 'checkbox', 'label'=>false,$checked)); ?>
          </div>
          <div class="clear"></div>
        </div>
      <?php }?>


        
        <div class="row-form">
          <div class="span3">Reason for Leave*:</div>
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
        <button class="btn" type="button" id="popup_3" style="display:none;">Submit</button>
      <input type="submit" name="save" id="save" value="Submit" class="btn"  onclick='validSubmit()'/>
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/leave'" />          
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

