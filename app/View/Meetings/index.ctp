<script type="text/javascript">
$(document).ready(function(){
	$("#meeting-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bPaginate": false});

	$("#MeetingFromDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		 onClose: function( selectedDate ) {
			$( "#MeetingToDate" ).datepicker( "option", "minDate", selectedDate );
		}
	});

	$("#MeetingToDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		 onClose: function( selectedDate ) {
			$( "#MeetingFromDate" ).datepicker( "option", "maxDate", selectedDate );
		}
	});
});
</script>

<div style="margin:10px 50px 10px 50px;">
<?php echo $this->Form->create('Meeting'); ?>
    <?php 
		$from_date = $all['from_date'];
		$to_date = $all['to_date'];
		$project_id = $all['project_id'];
		
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
		}
		
		$all_projects = $this->requestAction('projects/get_meeting_projects_array');
		?>
        <b><?php echo "From : "?></b>
      <?php echo $this->Form->input('from_date', array('label' => false, 'div' => false, 'class'=>'form-control', 'value'=> $from_date, 'style'=>'width:100px; margin-top:6px;')); ?>
        <b><?php echo "To : "?></b>
      <?php echo $this->Form->input('to_date', array('label' => false, 'type'=>'text','div' => false, 'class' => 'form-control', 'value'=> $to_date, 'style'=>'width:100px; margin-top:6px;')); ?>
        <b><?php echo "Project : "?></b>
      <?php echo $this->Form->input('project_id', array('label' => false, 'type'=>'select','div' => false, 'class' => 'form-control', 'empty' => 'All',  'options'=> $all_projects, 'style'=>'margin-top:6px;', 'value' => $project_id)); ?>
      <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?></td>
      <?php echo $this->Html->link('Reset', array('controller' => 'meetings', 'action' => 'report_reset/index'), array('class' => 'btn btn-danger')); ?>
  <?php echo $this->Form->end(); ?>
  <div class="clear"></div>
</div>

<div class="workplace">
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-chats"></div>
        <h1>Meetings</h1>
        <ul class="buttons">
          <li><a href="<?php echo $this->base?>/meetings/add" title="Add Works"><span class="isw-plus"></span></a></li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="meeting-index">
          <thead>
            <tr>
              <th width="6%">No</th>
              <th width="10%">Meeting Date</th>
              <th width="64%">Meeting Detailis</th>
              <th width="10%">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($meetings as $meeting): ?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td><?php echo h($meeting['Meeting']['meeting_date']); ?></td>
              <td>
			  <?php 
			  $projects = $this->requestAction('meetings/get_meeting_by_userid_date/'. $this->Session->read('User.id').'/'.date('Y-m-d', strtotime($meeting['Meeting']['meeting_date'])));
			  
			  $y = 1; $count = count($projects);
			  foreach($projects as $project){
				  echo '<b>'.$y.'. '.$project['Project']['projectname'].'</b>&nbsp;';
				  echo $project['Meeting']['status'] == 0 ? '<a href="'.$this->base.'/meetings/edit/'.$project['Meeting']['id'].'"><span class="icon-pencil"></span></a>'.$this->Html->link('<span class="icon-remove"></span>',array('controller'=>'meetings', 'action'=>'meeting_delete_row', $project['Meeting']['id'], 'index'), array('title'=>'Delete Meeting', 'escape'=>false, 'confirm'=>'Are you sure to delete meeting on '.$project['Project']['projectname'].'?')) : '';
				  echo '<br>';
				  echo 'Schedule From: '.date('h:i a', strtotime($project['Meeting']['meeting_schedule_start'])).' To '.date('h:i a', strtotime($project['Meeting']['meeting_schedule_end'])).'<br>';
				  echo 'Actual Meeting From: '.date('h:i a', strtotime($project['Meeting']['meeting_actual_start'])).' To '.date('h:i a', strtotime($project['Meeting']['meeting_actual_end'])).'<br>';
				  
				  echo $count != $y ? '<br>' : '';
				  $y++;
			  }
			  ?>
              </td>
              <td>
              <?php /*?><a href="<?php echo $this->base?>/admin/meetings/edit/<?php echo $meeting['Meeting']['id'];?>" title="Edit Meeting"><span class="icon-pencil"></span></a><?php */?>
              <a href="<?php echo $this->base?>/meetings/meetings_view/<?php echo $meeting['Meeting']['user_id'];?>/<?php echo date('Y-m-d', strtotime($meeting['Meeting']['meeting_date']));?>" title="View Meeting"><span class="isb-text_document"></span></a>
              <?php echo $meeting['Meeting']['status'] == 0 ? $this->Html->link('<span class="icon-remove"></span>',array('controller'=>'meetings', 'action'=>'meeting_delete', $meeting['Meeting']['meeting_date'], 'index'), array('title'=>'Delete Meeting', 'escape'=>false, 'confirm'=>'Are you sure to delete meeting on '.$meeting['Meeting']['meeting_date'].'?')) : ''; ?>
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