<script type="text/javascript">
$(function() {
	$("#PermissionFromDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		});
	$("#PermissionToDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		});
});

$(document).ready(function(){
	$("#leave-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": false,
		"bInfo": true,
		"bPaginate": true});
});
</script>

<div style="margin:10px 50px 10px 50px;">
<?php echo $this->Form->create('Permission'); ?>
    <?php 
		$from_date = $all['from_date'];
		$to_date = $all['to_date'];
		
		if(empty($from_date)){
			$from_date = "";
		}else{
			$from_date = date('d-m-Y', strtotime($from_date));
		}
		
		if(empty($to_date)){
			$to_date = "";
		}
		else{
			$to_date = date('d-m-Y', strtotime($to_date));
		} ?>
        <b><?php echo "From : "?></b>
      <?php echo $this->Form->input('from_date', array('label' => false, 'div' => false, 'class'=>'form-control', 'value'=> $from_date, 'style'=>'width:100px; margin-top:6px;')); ?>
      
        <b><?php echo "To : "?></b>
      <?php echo $this->Form->input('to_date', array('label' => false, 'type'=>'text','div' => false, 'class' => 'form-control', 'value'=> $to_date, 'style'=>'width:100px; margin-top:6px;')); ?>
      
      	<div style="display:none">
        <b><?php echo " Type : "?></b>
      <?php echo $this->Form->input('permission_leave', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array('1'=>'Permission','2'=>'Half Day Leave'), 'selected'=>$all['permission_leave'], 'style'=>'width:150px; margin-top:6px;')); ?>
      </div>
      
        <b><?php echo " Status : "?></b>
      <?php echo $this->Form->input('approved', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array('0'=>'Pending', '1'=>'Approved','2'=>'Declined'), 'selected'=>$all['approved'], 'style'=>'width:100px; margin-top:6px;')); ?>
      
      <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?>
      <?php echo $this->Html->link('Reset', array('controller' => 'Permission', 'action' => 'permission_reset'), array('class' => 'btn btn-danger')); ?>
  <?php echo $this->Form->end(); ?>
  <div class="clear"></div>
</div>

<div class="workplace">
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-list"></div>
        <h1>Permission</h1>
        <ul class="buttons">
          <li><a href="<?php echo $this->base?>/permission/permission_add" title="Add Permission"><span class="isw-plus"></span></a></li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="leave-index">
          <thead>
            <tr>
              <th width="5%">No</th>
              <th width="15%">Requisition</th>
              <th width="10%">Date</th>
              <th width="10%">Compensation Date</th>
              <th width="10%">From Time</th>
              <th width="10%">To Time</th>
              <th width="10%">Request Hours</th>
              <th width="20%">Reason</th>
              <th width="5%">Status</th>
              <th width="15%">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <?php 
			$i=1; 
			foreach ($leaves as $leave): 
				$datetime1 = new DateTime($leave['Permission']['from_time']);
				$datetime2 = new DateTime($leave['Permission']['to_time']);
				$interval = $datetime1->diff($datetime2);
				$hours = ($interval->format('%h')*60)+($interval->format('%i'));
			?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td>
			  <?php 
			  	if($leave['Permission']['request']=='past'){
					echo h('Past Requisition'); 
				}
				elseif($leave['Permission']['request']=='current'){
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
              <td><?php echo h(gmdate("H:i", ($hours* 60))) ?></td>
			  <td><?php echo h($leave['Permission']['reason'])?></td>
              <td><p>
			  <?php
			  if($leave['Permission']['approved'] == 0){?>
				<span class="label label-important">Pending</span>
			  <?php }
			  elseif($leave['Permission']['approved'] == 1){?>
				<span class="label label-success">Approved</span>
			  <?php }
			  if($leave['Permission']['approved'] == 2){?>
				<span class="label label-inverse">Declined</span>
			  <?php }
			  ?></p>
              </td>
			  <td><?php echo h($leave['Permission']['remarks'])?></td>
            </tr>
            <?php $i++; endforeach; ?>
          </tbody>
        </table>
        <div class="clear"></div>
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
</div>