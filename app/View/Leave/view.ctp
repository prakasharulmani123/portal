<script>
$(function() {
	$("#LeaveDate").datepicker({dateFormat: 'dd-mm-yy'});
});
</script>

<div class="workplace">
    <div class="row-fluid">               
    <div align="right" class="span12">
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/leave'" />          
    </div>
    </div>

  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-target"></div>
        <h1>View Leave Request</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
      
        <div class="row-form">
          <div class="span3">Employee Name:</div>
          <div class="span9">
           <b><?php echo $users['User']['employee_name']; ?></b>
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
          <div class="span3">Requisition:</div>
          <div class="span9">
           <?php if($leave['Leave']['request']=='past'){echo 'Past Leave Requisition';}elseif($leave['Leave']['request']=='current'){echo 'Current Leave Requisition';} ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Date:</div>
          <div class="span2">
           <?php echo date('d-m-Y', strtotime($leave['Leave']['date'])); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Reason for Leave:</div>
          <div class="span9">
           <?php echo $leave['Leave']['reason']; ?>
          </div>
          <div class="clear"></div>
        </div>

      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
</div>

