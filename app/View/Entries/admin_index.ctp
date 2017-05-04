<?php echo $this->Html->css(array('bootstrap-editable.css'), 'stylesheet', array('inline' => false)); ?>
<?php echo $this->Html->script(array('bootstrap-editable.js'), array('inline' => false)); ?>

<script>
$(function() {
	$("#EntryFromDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true
		});
	$("#EntryToDate").datepicker({
		dateFormat: 'dd-mm-yy',
		altFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
  });
});

$(document).ready(function() {

	$("#entryreport-index").dataTable({
		"iDisplayLength": 25, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": true,
		"bPaginate": true
	});
	
});
</script>
  
<div style="margin:10px 50px 10px 50px;">
<?php echo $this->Form->create('Entry'); ?>
    <?php 
		$all_user = array();
		
		foreach($users as $user){
			$all_user[$user['User']['id']] = $user['User']['employee_name'];
		}
		
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
        <b><?php echo " Employee : "?></b>
      <?php echo $this->Form->input('user_id', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array($all_user), 'selected' => $all['user_id'], 'style'=>'width:200px; margin-top:6px;')); ?>
        <b><?php echo " From : "?></b>
      <?php echo $this->Form->input('from_date', array('label' => false, 'div' => false, 'class'=>'form-control', 'value'=> $from_date, 'style'=>'width:100px; margin-top:6px;')); ?>
        <b><?php echo " To : "?></b>
      <?php echo $this->Form->input('to_date', array('label' => false, 'type'=>'text','div' => false, 'class' => 'form-control', 'value'=> $to_date, 'style'=>'width:100px; margin-top:6px;')); ?>
      <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?></td>
      <?php echo $this->Html->link('Reset', array('controller' => 'entries', 'action' => 'reset', 'admin' => true), array('class' => 'btn btn-danger')); ?>
  <?php echo $this->Form->end(); ?>
  <div class="clear"></div>
</div>

<div class="workplace">
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-time"></div>
        <h1>Time In / Out Reports</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="entryreport-index">
          <thead>
            <tr>
              <th width="5%">No</th>
              <th width="30%">Name</th>
              <th width="10%">Date</th>
              <th width="10%">Time In</th>
              <th width="10%">Time Out</th>
              <th width="10%">Time In IP</th>
              <th width="10%">Time Out IP</th>
              <th width="15%">Worked Hours</th>
            </tr>
          </thead>
          <tbody>
            <?php 
			$i=1; 
			foreach ($entries as $entry):?>
            <?php 
			if(($entry['Entry']['time_in'] == '0000-00-00 00:00:00') || ($entry['Entry']['time_in'] == NULL)){
				$time_in = '--';
			}
			else{
				$time_in = date('g:i A',strtotime($entry['Entry']['time_in']));
			}

			if(($entry['Entry']['time_out'] == '0000-00-00 00:00:00') || ($entry['Entry']['time_out'] == NULL)){
				$time_out = '--';
			}
			else{
				$time_out = date('g:i A',strtotime($entry['Entry']['time_out']));
			}
			?>
            <tr>
              <td><?php echo h($i); ?></td>
              <?php $employee = $this->requestAction('users/get_user',array('pass' => array($entry['Entry']['user_id']))); ?>
              <td><?php echo $employee['User']['employee_name']; ?></td>
              <td><?php echo date('d-m-Y',strtotime($entry['Entry']['date'])); ?></td>
              <td><?php echo $time_in; ?></td>
              <td><?php echo $time_out; ?></td>
              <td><?php echo $entry['Entry']['time_in_ip']; ?></td>
              <td><?php echo $entry['Entry']['time_out_ip']; ?></td>
              <td>
			  	<?php 
				$datetime1 = new DateTime($entry['Entry']['time_in']);
				$datetime2 = new DateTime($entry['Entry']['time_out']);
				$interval = $datetime1->diff($datetime2);
				$hours = ($interval->format('%h')*60)+($interval->format('%i'));

				$diff = (float)str_replace(':','.',date('G:i',strtotime($entry['Entry']['time_out']))) - (float)str_replace(':','.',date('G:i',strtotime($entry['Entry']['time_in'])));
				
				if(($time_in != '--') && ($time_out != '--')){
					echo gmdate("H:i", ($hours* 60)).'<br>';
				}
				else{
					echo '--';
				}
				?>
             </td>
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
