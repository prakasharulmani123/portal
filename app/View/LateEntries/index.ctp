<script type="text/javascript">
$(function() {
	$("#LateEntryFromDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		});
	$("#LateEntryToDate").datepicker({
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
		"bFilter": true,
		"bInfo": true,
		"bPaginate": true});
});
</script>

<div class="workplace">
<div style="margin:10px 50px 10px 50px;">
<?php echo $this->Form->create('LateEntry'); ?>
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
      <?php echo $this->Form->input('approved', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array('1'=>'Approved','2'=>'Declined'), 'selected'=>$all['approved'], 'style'=>'width:100px; margin-top:6px;')); ?>
      <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?>
      <?php echo $this->Html->link('Reset', array('controller' => 'late_entries', 'action' => 'reset'), array('class' => 'btn btn-danger')); ?>
  <?php echo $this->Form->end(); ?>
   <div class="clear"></div>
</div>

  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-archive"></div>
        <h1>Late Entries</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="leave-index">
          <thead>
            <tr>
              <th width="5%">No</th>
              <th width="23%">Date</th>
              <th width="22%">Amount</th>
              <th width="16%">Entry Time</th>
              <th width="18%">Late Hours</th>
              <th width="16%">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php 
				$i=1; 
				
				foreach ($late_entries as $late_entry): 
				$user = $this->requestAction('users/get_user',array('pass'=>array('User.id'=>$late_entry['LateEntry']['user_id']))); 
				
				$start_time = date('Y-m-d', strtotime($late_entry['LateEntry']['date'])).' 10:00:00';
				$datetime1 = new DateTime($start_time);
				$datetime2 = new DateTime($late_entry['LateEntry']['created']);
				
				$interval = $datetime1->diff($datetime2);
			?>
				<tr>
				  <td><?php echo h($i); ?></td>
				  <td><?php echo h(date('d-m-Y', strtotime($late_entry['LateEntry']['date']))) ?></td>
				  <td><?php echo $late_entry['LateEntry']['amount'] == 0 ? 'Warning' : $late_entry['LateEntry']['amount']; ?></td>
				  <td><?php echo h(date('H:i:s A', strtotime($late_entry['LateEntry']['created']))) ?></td>
                  <td><?php echo h($interval->format('%h').':'.$interval->format('%I').':'.$interval->format('%S')) ?></td>
	              <td><p>
			  <?php
			  if($late_entry['LateEntry']['approved'] == 0){?>
				<span class="label label-important">Pending</span>
			  <?php }
			  elseif($late_entry['LateEntry']['approved'] == 1){?>
				<span class="label label-success">Approved</span>
			  <?php }
			  if($late_entry['LateEntry']['approved'] == 2){?>
				<span class="label label-inverse">Declined</span>
			  <?php }
			  ?></p></td>
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