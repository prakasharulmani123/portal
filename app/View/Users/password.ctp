
<div class="workplace">
<?php echo $this->Form->create('User', array('id' => 'validation', 'novalidate'=>true)); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-lock"></div>
        <h1>Change Password</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
      
        <div class="row-form">
          <div class="span3">Old Password*:</div>
          <div class="span9">
           <?php echo $this->Form->input('old_password', array('type'=>'password', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">New Password*:</div>
          <div class="span9">
           <?php echo $this->Form->input('new_password', array('type'=>'password', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Confirm Password*:</div>
          <div class="span9">
           <?php echo $this->Form->input('con_password', array('type'=>'password', 'class' => 'validate[required,equals[UserNewPassword]]','label'=>false)); ?>
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
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/dailystatus'" />          
    </div>
  </div>
<?php echo $this->Form->end(); ?>
</div>

