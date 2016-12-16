<script type="text/javascript">
$(function() {
	$("#LeaveFromDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		});
	$("#LeaveToDate").datepicker({
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

<div class="workplace">
<?php 
$leave_count = $this->requestAction('leave/user_get_all_leave_count');
$casual_leave_per_year = $this->requestAction('leave/user_get_all_leave_count_by_user_id_and_status/'.$this->Session->read('User.id').'/C/'.date('Y'));
$paid_leave_per_year = $this->requestAction('leave/user_get_all_leave_count_by_user_id_and_status/'.$this->Session->read('User.id').'/P/'.date('Y'));

$user_casual_leave = $this->Session->read('User.casual_leave');
?>

    <div class="wBlock auto">
        <div class="dSpace">
            <h3>Remaining Casual <br />Leave Days</h3>
            <span class="number"><?php echo ($user_casual_leave - $casual_leave_per_year) <= 0 ? 0 : $user_casual_leave - $casual_leave_per_year; ?></span>                                                
        </div>
    </div>
    
    <div class="wBlock green auto">
        <div class="dSpace">
            <h3>Current <br />Leave Days</h3>
            <span class="number"><?php echo $leave_count;?></span>                                                  
        </div>
    </div>                    

	<div class="wBlock red auto">
        <div class="dSpace">
            <h3>Paid <br />Leave Days</h3>
            <span class="number"><?php echo $paid_leave_per_year?></span>                                                  
        </div>
    </div>                    

<div style="margin:10px 50px 10px 50px;">
<?php echo $this->Form->create('Leave'); ?>
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
        <b><?php echo " Status : "?></b>
      <?php echo $this->Form->input('approved', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array('0'=>'Pending', '1'=>'Approved','2'=>'Declined'), 'selected'=>$all['approved'], 'style'=>'width:100px; margin-top:6px;')); ?>
      <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?>
      <?php echo $this->Html->link('Reset', array('controller' => 'Leave', 'action' => 'leave_reset'), array('class' => 'btn btn-danger')); ?>
  <?php echo $this->Form->end(); ?>
   <div class="clear"></div>
</div>

  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-target"></div>
        <h1>Leave Requests</h1>
        <ul class="buttons">
          <li><a href="<?php echo $this->base?>/leave/leaveform" title="Add Leave"><span class="isw-plus"></span></a></li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="leave-index">
          <thead>
            <tr>
              <th width="6%">No</th>
              <th width="10%">Requisition</th>
              <th width="10%">Date</th>
              <th width="10%">Days</th>
              <th width="15%">Reason</th>
              <th width="5%">Status</th>
              <th width="10%">Remarks</th>
              <th width="15%">Casual / Paid</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($leaves as $leave): ?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td>
			  <?php 
			  	if($leave['Leave']['request']=='past'){
					echo h('Past Requisition'); 
				}
				elseif($leave['Leave']['request']=='current'){
					echo h('Current Requisition'); 
				}
				?>
                </td>
				<?php
                $leave_date = '';
                foreach($leave['SubLeave'] as $subleave){
                    $leave_date .= date('d-m-Y', strtotime($subleave['date'])).' & '; 
                }
                $leave_date = rtrim($leave_date,' & ');
                ?>
              <td><?php echo h($leave_date) ?></td>
              <td>
			  <?php 
				switch ($leave['Leave']['days'])
				{
				case 0.5:
					echo 'Half a day';
					break;
				case 1:
					echo 'One day';
					break;
				case 1.5:
					echo 'One & Half a days';
					break;
				case 2:
					echo 'Two days';
					break;
				case 2.5:
					echo 'Two & Half a days';
					break;
				case 3:
					echo 'Three days';
					break;
				}
			  ?></td>
			  <td><?php echo h($leave['Leave']['reason'])?></td>
              <td><p>
			  <?php
			  if($leave['Leave']['approved'] == 0){?>
				<span class="label label-important">Pending</span>
			  <?php }
			  elseif($leave['Leave']['approved'] == 1){?>
				<span class="label label-success">Approved</span>
			  <?php }
			  if($leave['Leave']['approved'] == 2){?>
				<span class="label label-inverse">Declined</span>
			  <?php }
			  ?></p>
              </td>
			  <td><?php echo h($leave['Leave']['remarks'])?></td>
              <td><?php if($leave['Leave']['approved'] == 1){
					foreach($leave['SubLeave'] as $subleave){ ?>
                    	<span id="td_span_<?php echo $subleave['id']?>">
							<?php echo date('d-m-Y', strtotime($subleave['date']))?> : <?php echo $subleave['status'] == 'C' ? '<b class="text-info">Casual</b>' : '<b class="text-error">Paid</b>';?>
                        </span><br />
					<?php }
					}
					?></td>
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