<div class="workplace">
<?php echo $this->Form->create('User', array('id' => 'validation', 'type'=>'file')); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-settings"></div>
        <h1>Edit Profile</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
        <div class="row-form">
          <div class="span3">E-mail:</div>
          <div class="span9">
           <?php echo $this->Form->input('email', array('class' => 'validate[required]','label'=>false, 'readonly'=>true)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Password:</div>
          <div class="span9">
           <?php echo $this->Form->input('password', array('type'=>'password', 'value'=>'', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
  <div class="row-fluid">                
    <div align="center" class="span12">
      <input type="submit" name="save" id="save" value="Save" class="btn" />
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/admin/users/index'" />          
    </div>
  </div>
<?php echo $this->Form->end(); ?>
</div>

