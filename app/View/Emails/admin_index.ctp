<script type="text/javascript">
$(document).ready(function(){
	$("#email-index").dataTable({
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
        <div class="isw-mail"></div>
        <h1>Email</h1>
        <ul class="buttons">
          <li><a class="check-access" href="<?php echo $this->base?>/admin/emails/add" title="Add Email"><span class="isw-plus"></span></a></li>
        </ul>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="email-index">
          <thead>
            <tr>
              <th width="6%">No</th>
              <th width="20%">Name</th>
              <th width="25%">Email</th>
              <th width="8%">To/Cc/Bcc</th>
              <th width="28%">Options</th>
              <th width="5%">Active</th>
              <th width="12%">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 		
				$options = $this->requestAction('emails/get_email_options');
				$i=1; foreach ($emails as $email): ?>
            <tr>
              <td><?php echo h($i); ?></td>
              <td><?php echo h($email['Email']['name']); ?></td>
              <td><?php echo h($email['Email']['email']); ?></td>
              <td><?php echo h($email['Email']['to_cc']); ?></td>
              <td>
			  <?php
			  	if($email['Email']['options']){
					$array = explode(',',$email['Email']['options']);
					if(!empty($array)){
						$opt = '';
						foreach($array as $key=>$value){
							$opt .= $options[$value].' ,';
						}
						echo rtrim($opt,',');
					}
				}
			  ?>
              </td>
              <td><?php echo $this->Html->link($this->Html->image('icon_' . $email['Email']['active'] . '.png'), array('controller' => 'emails', 'action' => 'switch', 'active', $email['Email']['id']), array('class' => 'status', 'escape' => false)); ?></td>
			  </td>
              <td>
              <a class="check-access" href="<?php echo $this->base?>/admin/emails/edit/<?php echo $email['Email']['id'];?>" title="Edit Email"><span class="icon-pencil"></span></a> 
              <?php echo $this->Html->link('<span class="icon-remove"></span>',array('controller'=>'emails', 'action'=>'admin_email_delete', $email['Email']['id']), array('title'=>'Delete Email', 'escape'=>false, 'confirm'=>'Are you sure to delete '.$email['Email']['name'].'?')); ?>
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