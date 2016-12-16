<div class="loginBox">        
    <div class="loginHead">
    <div style="margin:-1px 15px 2px 0px;"><font style="font-family: 'Times New Roman', Times, serif; color:#010101; font-size:22px; color:white;">Forgot Password
    </font>
    </div>
    </div>
  <div style="color:#F00; text-align:center;"><?php echo $this->Session->flash(); ?></div>
	<?php echo $this->Form->create('User',array('class'=>'form-horizontal', 'id' => 'validation', 'novalidate'=>true));?>
    <?php echo $this->Form->input('password', array('type'=>'hidden', 'value'=>'')); ?>
        <div class="control-group">
            <label for="inputEmail">Email *</label>                
            <?php echo $this->Form->input('email', array('class'=>'validate[required,custom[email]]', 'type'=>'text', 'label'=>false)); ?>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-block">Get My Password</button>
        </div>
    </form>        
</div> 