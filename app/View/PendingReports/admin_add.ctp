<script type="text/javascript">
$(document).ready(function(){
	$( "#PendingReportDate" ).datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		}
	);

	$("#validation").submit(function(){
		checked = $('input.checkbox[type=checkbox]:checked').size();
		
		if(checked == 0){
			alert('Please Select Atlease one Employee');
			return false;
		}
	});
});
</script>

<div class="workplace">
<?php echo $this->Form->create('PendingReport', array('id' => 'validation')); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-archive"></div>
        <h1>Add Pending Report</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
        
        <div class="row-form">
          <div class="span3">Employee:</div>
          <div class="span9">
             <?php /*?><?php echo $this->Form->input('category_id', array('empty' => true,'label'=>false)); ?><?php */?>
                 <div class="row-fluid">
                       <div class="span6">
		                    <div class="head">
		                        <h1>Please Select Employee</h1>
	                        <div class="clear"></div>
	                    </div>
                    
                    <div class="block messages scrollBox">                        
                        <div class="scroll" style="height: 390px;">
						<table cellpadding="0" cellspacing="0" width="100%" class="table">
                            <thead>
                                <tr>
									<th width="74%">Name</th>
                                    <th><input type="checkbox" name="checkall" checked="checked"/>CheckAll</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $users = $this->requestAction('users/get_all_users'); $i = 0;?>
                            <?php foreach($users as $user):?>
                             <tr>
                                <td><?php echo $user['User']['employee_name']?></td>
                                <td><input type="checkbox" value="<?php  echo $user['User']['id']?>" name="data[PendingReport][user][<?php echo $i?>]" checked="checked" class="checkbox"/></td>
                             </tr>
                             <?php $i++; endforeach;?>
                         </tbody></table>
                        </div>
                    </div>                
                </div>                                
           </div>
          </div>
        <div class="clear"></div>
        </div>
    
		<div class="row-form">
          <div class="span3">Start Date*:</div>
          <div class="span2">
            <?php echo $this->Form->input('date', array('class' => 'validate[required]', 'type'=>'text', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
		<?php
        $hours = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12');
        $minutes = array('00'=>'00', '05'=>'05', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30', '35'=>'35', '40'=>'40', '45'=>'45', '50'=>'50', '55'=>'55');
        $mer = array('am'=>'am','pm'=>'pm');
        ?>

		<div class="row-form">
          <div class="span3">Start Time*:</div>
            <?php echo $this->Form->input('start_time', array('class' => 'validate[required]','type'=>'hidden')); ?>
          <div class="span1">
            <?php echo $this->Form->input('start_hours', array('class' => 'validate[required]','type'=>'select', 'options'=>$hours, 'label'=>false, 'empty'=>'hours', 'name'=>'data[PendingReport][start][hours]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('start_minutes', array('class' => 'validate[required]','type'=>'select', 'options'=>$minutes, 'label'=>false, 'empty'=>'minutes', 'name'=>'data[PendingReport][start][minutes]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('start_mer', array('class' => 'validate[required]','type'=>'select', 'options'=>$mer, 'label'=>false, 'empty'=>'meridian', 'name'=>'data[PendingReport][start][meridian]')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
		<div class="row-form">
          <div class="span3">Reason :</div>
          <div class="span9">
            <?php echo $this->Form->input('reason', array('type'=>'textarea', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
		<div class="row-form">
          <div class="span3">Remarks :</div>
          <div class="span9">
            <?php echo $this->Form->input('remarks', array('type'=>'textarea', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
  <div class="row-fluid">                
    <div align="center" class="span12">
      <input type="submit" name="save" id="save" value="Add" class="btn" />
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

