<script type="text/javascript">
$(document).ready(function(){
	$("#holiday-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": true,
		"bPaginate": true});
});
</script>

<div class="workplace">
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-bookmark"></div>
        <h1>Holidays</h1>
        <ul class="buttons">
          <li><a href="<?php echo $this->base?>/admin/holidays/add" title="Add Holiday"><span class="isw-plus"></span></a></li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="holiday-index">
          <thead>
            <tr>
              <th width="10%">No</th>
              <th width="40%">Date</th>
              <th width="40%">Name</th>
              <th width="10%">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($holidays as $holiday): ?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td><?php echo date('d-m-Y', strtotime($holiday['Holiday']['date'])); ?></td>
              <td><?php echo h($holiday['Holiday']['name']); ?></td>
              <td>
              <a href="<?php echo $this->base?>/admin/holidays/edit/<?php echo $holiday['Holiday']['id'];?>" title="Edit Holiday"><span class="icon-pencil"></span></a> 
              <?php echo $this->Html->link('<span class="icon-remove"></span>',array('controller'=>'holidays', 'action'=>'admin_holiday_delete', $holiday['Holiday']['id']), array('title'=>'Delete Holiday', 'escape'=>false, 'confirm'=>'Are you sure to delete '.$holiday['Holiday']['name'].'?')); ?>
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