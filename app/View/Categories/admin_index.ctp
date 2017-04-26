<script type="text/javascript">
$(document).ready(function(){
	$("#category-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": false,
		"bPaginate": false});
});
</script>

<div class="workplace">
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-grid"></div>
        <h1>Category</h1>
        <ul class="buttons">
          <li><a class="check-access" href="<?php echo $this->base?>/admin/categories/add" title="Add Category"><span class="isw-plus"></span></a></li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="category-index">
          <thead>
            <tr>
              <th width="2%">No</th>
              <th width="40%">Category</th>
              <th width="6%">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($categories as $category): ?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td><?php echo h($category['Category']['category']); ?></td>
              <td>
              <a class="check-access" href="<?php echo $this->base?>/admin/categories/edit/<?php echo $category['Category']['id'];?>" title="Edit Category"><span class="icon-pencil"></span></a> 
              <?php echo $this->Html->link('<span class="icon-remove"></span>',array('controller'=>'categories', 'action'=>'admin_category_delete', $category['Category']['id']), array('title'=>'Delete Category', 'escape'=>false, 'confirm'=>'Are you sure to delete '.$category['Category']['category'].'?')); ?>
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