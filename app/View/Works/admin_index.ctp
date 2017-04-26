<script type="text/javascript">
$(document).ready(function(){
	$("#work-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bPaginate": false});
});
</script>

<div class="workplace">
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-archive"></div>
        <h1>Work</h1>
        <ul class="buttons">
          <li><a class="check-access" href="<?php echo $this->base?>/admin/works/add" title="Add Works"><span class="isw-plus"></span></a></li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="work-index">
          <thead>
            <tr>
              <th width="6%">No</th>
              <th width="80%">Work</th>
              <th width="6%">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($works as $work): ?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td><?php echo h($work['Work']['work']); ?></td>
              <td>
              <a class="check-access" href="<?php echo $this->base?>/admin/works/edit/<?php echo $work['Work']['id'];?>" title="Edit Work"><span class="icon-pencil"></span></a> 
              <?php echo $this->Html->link('<span class="icon-remove"></span>',array('controller'=>'works', 'action'=>'admin_work_delete', $work['Work']['id']), array('title'=>'Delete Work', 'escape'=>false, 'confirm'=>'Are you sure to delete '.$work['Work']['work'].'?')); ?>
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