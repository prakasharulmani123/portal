<script type="text/javascript">
$(document).ready(function(){
	$("#settings_index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": false,
		"bFilter": true,
		"bInfo": false,
		"bPaginate": true});
});
</script>

<div class="workplace">
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-settings"></div>
                <h1>Settings</h1>
                <div class="clear"></div>
            </div>
            <div class="block-fluid table-sorting">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="settings_index">
                    <thead>
                        <tr>
                            <th width="2%">No</th>
                            <th width="40%">Name</th>
                            <th width="6%">Active</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php $i=1; foreach ($settings as $setting): ?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td><?php echo h($setting['Setting']['description']); ?></td>
              <td><?php echo $this->Html->link($this->Html->image('icon_' . $setting['Setting']['value'] . '.png'), array('controller' => 'settings', 'action' => 'switch', 'value', $setting['Setting']['id']), array('class' => 'status', 'escape' => false)); ?></td>
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