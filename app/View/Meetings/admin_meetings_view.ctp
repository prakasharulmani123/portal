<script>
$(document).ready(function() {

	$("#dailyreport-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": false,
		"bInfo": false,
		"bPaginate": false
	});
});
</script>

<div class="workplace">
  <div class="row-fluid">                
    <div align="right" class="span12">
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/admin/meetings'" />          
    </div>
  </div>

  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-chats"></div>
        <h1>View Meeting</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
      
        <div class="row-form">
          <div class="span3">Date:</div>
          <div class="span9"><?php echo date('M d, Y', strtotime($date)); ?></div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Recorded By:</div>
          <div class="span9"><?php echo $meetings[0]['User']['employee_name']; ?></div>
          <div class="clear"></div>
        </div>
    
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-chats"></div>
        <h1>Meeting Details</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="dailyreport-index">
          <thead>
            <tr>
              <th width="6%">No</th>
              <th width="11%">Project</th>
              <th width="14%">Meeting Location</th>
              <th width="15%">Schedule Time</th>
              <th width="13%">Actual Time</th>
              <th width="12%">Meeting Scribe</th>
              <th width="18%">Agenda</th>
              <th width="11%">Next Meeting</th>
            </tr>
          </thead>
          <tbody>
          <?php $i = 1; foreach($meetings as $meeting){ ?>
          <tr>
          	<td><?php echo $i++ ?></td>
          	<td><?php echo $meeting['Project']['projectname']?></td>
          	<td>
			<?php echo '<b>Buliding : </b>'.$meeting['Meeting']['building'].'<br><b>Web address : </b>'.preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', '<a href="$1">$1</a>', $meeting['Meeting']['web_address'])?>
            </td>
          	<td>
			<?php 
			echo 'From : '.date('h:i a', strtotime($meeting['Meeting']['meeting_schedule_start'])).'<br>';
			echo 'To : '.date('h:i a', strtotime($meeting['Meeting']['meeting_schedule_end'])).'<br>';
				$datetime1 = new DateTime($meeting['Meeting']['meeting_schedule_start']);
				$datetime2 = new DateTime($meeting['Meeting']['meeting_schedule_end']);
				$interval = $datetime1->diff($datetime2);
				$hours = ($interval->format('%h')*60)+($interval->format('%i'));
				echo 'Elapsed : <b>'.gmdate("H:i", ($hours* 60)).'</b>';
			?>
            </td>
          	<td>
			<?php 
			echo 'From : '.date('h:i a', strtotime($meeting['Meeting']['meeting_actual_start'])).'<br>';
			echo 'To : '.date('h:i a', strtotime($meeting['Meeting']['meeting_actual_end'])).'<br>';
				$datetime1 = new DateTime($meeting['Meeting']['meeting_actual_start']);
				$datetime2 = new DateTime($meeting['Meeting']['meeting_actual_end']);
				$interval = $datetime1->diff($datetime2);
				$hours = ($interval->format('%h')*60)+($interval->format('%i'));
				echo 'Elapsed : <b>'.gmdate("H:i", ($hours* 60)).'</b>';
			?>
            </td>
          	<td><?php echo $meeting['Meeting']['meeting_scribe']?></td>
          	<td><?php echo $meeting['Meeting']['agenda']?></td>
          	<td><?php echo $meeting['Meeting']['next_meeting']?></td>
          </tr>
          <?php } ?>
          </tbody>
        </table>
        <div class="clear"></div>
      </div>
    </div>
  </div>
  
</div>

