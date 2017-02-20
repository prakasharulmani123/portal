<script type="text/javascript">
$(document).ready(function(){
});
</script>

<div class="workplace">
<?php echo $this->Form->create('UserComplaint', array('id' => 'validation', 'novalidate'=>true)); ?>
<?php echo $this->Form->hidden('sender_id', array('type'=>'hidden', 'value' => $this->Session->read('User.id'))); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-mail"></div>
        <h1>Add UserComplaint</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
        <div class="row-form">
          <div class="span3">Person Name*:</div>
          <div class="span9">
           <?php echo $this->Form->input('receiver_id', array('type'=>'select', 'options'=>$complaint_users,'class' => 'validate[required]','label'=>false, 'empty'=>'select')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Complaint*:</div>
          <div class="span9">
           <?php echo $this->Form->textarea('reason', array('type'=>'text', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
  <div class="row-fluid">                
    <div align="center" class="span12">
      <input type="submit" name="save" id="save" value="Update" class="btn" />
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/user_complaints'" />          
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

