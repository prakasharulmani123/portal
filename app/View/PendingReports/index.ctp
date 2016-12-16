<script type="text/javascript">
$(document).ready(function(){
	$("#pending-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": true,
		"bPaginate": true
	});
	
	$(".b_popup_4").dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		buttons:{                            
			"Ok": function() {
				var time_in_hours = $('#PendingReportStartHours').val();
				var time_in_minutes = $('#PendingReportStartMinutes').val();
				var time_in_mer = $('#PendingReportStartMer').val();
				var reason = $('#PendingReportReason').val();
				
				if(time_in_hours == '')
				{
					alert('Please Enter Hours');
					$('#PendingReportStartHours').focus();
					return false;
				}

				if(time_in_minutes == '')
				{
					alert('Please Enter Minutes');
					$('#PendingReportStartMinutes').focus();
					return false;
				}

				if(time_in_mer == '')
				{
					alert('Please Enter Merdian');
					$('#PendingReportStartMer').focus();
					return false;
				}

				if(reason == '')
				{
					alert('Please Enter Reason');
					$('#PendingReportReason').focus();
					return false;
				}
				
				$("#send-pending-report").trigger("click");
				
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
	
	$('.popup_4').click(function(){
		$(".b_popup_4").dialog('open');
		
		pending_id = $(this).attr('pending-id');
		pending_date = $(this).attr('pending-date');
		
		$('#PendingReportId').val(pending_id);
		$('#PendingReportDate').val(pending_date);
	});

});
</script>

<?php
$hours = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12');
$minutes = array('00'=>'00', '05'=>'05', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30', '35'=>'35', '40'=>'40', '45'=>'45', '50'=>'50', '55'=>'55');
$mer = array('am'=>'am','pm'=>'pm');
?>

<div class="dialog b_popup_4"  id="b_popup_4" style="display: none;" title="Send Request">
	<?php echo $this->Form->create('PendingReport', array('id' => 'pending_validation', 'controller'=>'pending_reports', 'action'=>'send_request_on_timer'));?>
    <div class="block">
        <span>Time In:</span>
        <p>
		<?php 
		echo $this->Form->input('id', array('type'=>'hidden')); 
		echo $this->Form->input('date', array('type'=>'hidden')); 
		echo $this->Form->input('start_time', array('type'=>'hidden')); 
		
		echo $this->Form->input('start_hours', array('type'=>'select', 'options'=>$hours, 'label'=>false, 'empty'=>'hours', 'name'=>'data[PendingReport][start][hours]', 'div'=>false, 'style'=>'width: 123px;')); 
		echo $this->Form->input('start_minutes', array('type'=>'select', 'options'=>$minutes, 'label'=>false, 'empty'=>'minutes', 'name'=>'data[PendingReport][start][minutes]', 'div'=>false, 'style'=>'width: 123px;')); 
		echo $this->Form->input('start_mer', array('type'=>'select', 'options'=>$mer, 'label'=>false, 'empty'=>'meridian', 'name'=>'data[PendingReport][start][meridian]', 'div'=>false, 'style'=>'width: 123px;')); ?>
		</p>
        <span>Reason:</span>
        <p>
        <?php echo $this->Form->input('reason', array('type'=>'textarea', 'label'=>false, 'div'=>false))?>
        </p>
        <div class="dr">
        <span></span>
        </div>
        <p style="color:blue">Your request will sent to admin. You can enter the report after admin approves</p>
    </div>
	<input type="submit" name="save" id="send-pending-report" value="Sent" style="display:none"/>
    <?php echo $this->Form->end(); ?>
</div>

<div class="workplace">
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-plus"></div>
        <h1>Pending Reports</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="pending-index">
          <thead>
            <tr>
              <th width="7%">No</th>
              <th width="12%">Date</th>
              <th width="12%">Start Time</th>
              <th width="22%">Reason</th>
              <th width="12%">Status</th>
              <th width="29%">Remarks</th>
              <th width="6%">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
			$i=1; 
			foreach ($pending_reports as $pending_report): 
			?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td><?php echo date('d-m-Y', strtotime($pending_report['PendingReport']['date'])) ?></td>
              <td><?php echo $pending_report['PendingReport']['start_time'] == '0000-00-00 00:00:00' ? '--' :date('h:i A', strtotime($pending_report['PendingReport']['start_time'])) ?></td>
              <td><?php echo $pending_report['PendingReport']['reason'] ?></td>
              <td>
              <?php if($pending_report['PendingReport']['start_time'] == '0000-00-00 00:00:00'){?>
              <a class="popup_4" href="#" pending-id="<?php echo $pending_report['PendingReport']['id']?>" pending-date="<?php echo $pending_report['PendingReport']['date']?>" style="text-decoration:none;"><p><span class="label label-warning">Send Request to admin</span></p></a>
			  <?php 
				  }
				  else{
				  if($pending_report['PendingReport']['status'] == 0){
					  echo '<p><span class="label label-important">Pending</span></p>';
				  }
				  elseif($pending_report['PendingReport']['status'] == 1){
					  echo '<a href="'.$this->base.'/pending_reports/dailystatus/'.$pending_report['PendingReport']['id'].'" style="text-decoration:none"><p><span class="label label-success">Click to send Report</span></p></a>';
				  }
				  elseif($pending_report['PendingReport']['status'] == 2){
					  echo '<p><span class="label label-inverse">Declined</span></p>';
				  }
			  }
			  ?>
              </td>
			  <td><?php echo $pending_report['PendingReport']['remarks'] ?></td>
              <td><?php echo $this->Html->link('<span class="icon-remove"></span>',array('action'=>'delete', $pending_report['PendingReport']['id']), array('title'=>'Delete Report', 'escape'=>false, 'confirm'=>'Are you sure to delete ?')); ?></td>
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