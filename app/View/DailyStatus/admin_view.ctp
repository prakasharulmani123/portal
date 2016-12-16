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
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/admin/dailystatus'" />          
    </div>
  </div>

  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-documents"></div>
        <h1>View Report</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
      <?php 
		$employee = $this->requestAction('users/get_user',array('pass' => array($user_id))); 
?>
      
        <div class="row-form">
          <div class="span3">Employee Name:</div>
          <div class="span9"><?php echo $employee['User']['employee_name']?></div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Date:</div>
          <div class="span9"><?php echo date('d-m-Y', strtotime($date)); ?></div>
          <div class="clear"></div>
        </div>
        
        <?php
			$worked_hours = 0;
			$break_hours = 0;
			$hours = 0;
			foreach($reports as $report){
				$datetime1 = new DateTime($report['DailyStatus']['start_time']);
				$datetime2 = new DateTime($report['DailyStatus']['end_time']);
				$interval = $datetime1->diff($datetime2);
				$elapsed = $interval->format('%h hour %i minute');
				
				$hours += ($interval->format('%h')*60)+($interval->format('%i'));
				
				if($report['DailyStatus']['category_id'] != 23 && $report['DailyStatus']['category_id'] != 22 && $report['DailyStatus']['category_id'] != 24){
					$worked_hours += ($interval->format('%h')*60)+($interval->format('%i'));
				}
				elseif($report['DailyStatus']['category_id'] != 24){
					$break_hours += ($interval->format('%h')*60)+($interval->format('%i'));
				}
			}
		?>
        <div class="row-form">
          <div class="span3">Total Hours:</div>
          <div class="span9">
		  <?php echo gmdate("H:i", ($hours* 60)).'<br>'?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Braek Hours:</div>
          <div class="span9">
		  <?php echo gmdate("H:i", ($break_hours* 60)).'<br>'?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Worked Hours:</div>
          <div class="span9">
		  <?php echo '<b>'.gmdate("H:i", ($worked_hours* 60)).'</b>'?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="dailyreport-index">
          <thead>
            <tr>
              <th width="5%">No</th>
              <th width="15%">Project</th>
              <th width="15%">Category</th>
              <th width="15%">Work</th>
              <th width="8%">Start Time</th>
              <th width="7%">End Time</th>
              <th width="7%">Elapsed</th>
              <th width="8%">Status</th>
              <th width="20%">Comments</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($reports as $report): 
				$ctgy = $this->requestAction('Categories/get_category_by_id', array('pass'=>array('Category.id'=>$report['DailyStatus']['category_id']))) ;
				$work = $this->requestAction('Works/get_work_by_id', array('pass'=>array('Work.id'=>$report['DailyStatus']['work_id']))) ;
				$sts = "";
				if($report['DailyStatus']['status'] == 1){
					$sts = 'progress';
				}
				elseif($report['DailyStatus']['status'] == 2){
					$sts = 'completed';
				}
				elseif($report['DailyStatus']['status'] == 3){
					$sts = 'in-completed';
				}
				elseif($report['DailyStatus']['status'] == 4){
					$sts = 'cancelled';
				}
			?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td><?php echo h($report['DailyStatus']['projectname']); ?></td>
              <td><?php echo h($ctgy['Category']['category']); ?></td>
              <td><?php echo h($report['DailyStatus']['work_id']);//h($work['Work']['work']); ?></td>
              <td><?php echo h(date('g:i A', strtotime(strval(str_replace('.',':',$report['DailyStatus']['start_time']))))); ?></td>
              <td><?php echo h(date('g:i A', strtotime(strval(str_replace('.',':',$report['DailyStatus']['end_time']))))); ?></td>
              <td>
			  <?php 
				$datetime1 = new DateTime($report['DailyStatus']['start_time']);
				$datetime2 = new DateTime($report['DailyStatus']['end_time']);
				$interval = $datetime1->diff($datetime2);
				$hours = ($interval->format('%h')*60)+($interval->format('%i'));
				echo gmdate("H:i", ($hours* 60));
			  ?>
              </td>
              <td><?php echo h($sts); ?></td>
              <td><?php echo h($report['DailyStatus']['comments']); ?></td>
              </td>
            </tr>
            <?php $i++; endforeach; ?>
          </tbody>
        </table>
        <div class="clear"></div>
      </div>
      
        <div class="row-form">
          <div class="clear"></div>
        </div>

      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
</div>

