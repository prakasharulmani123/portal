<div class="loginBox">        
    <div class="loginHead" style="
    padding-top: 4px !important;
">
<!--    <div style="margin:-1px 15px 2px 0px;"><font style="font-family: 'Times New Roman', Times, serif; color:#010101; font-size:21px; color:white;">SUMANAS TECHNOLOGIES-->
<!--	--><?php //echo $this->Html->image('logo1.png', array('width' => '500', 'height' => '70', 'alt'=>'Sumanas Technologies', 'title'=>'Sumanas Technologies')); ?>
<!--    </font>-->
<!--    </div>-->
        <?php echo $this->Html->image('logo1.png', array('width' => '120', 'height' => '30', 'alt'=>'Sumanas Technologies', 'title'=>'Sumanas Technologies')); ?>
    </div>
  <div style="color:#F00; text-align:center;"><?php echo $this->Session->flash(); ?></div>
	<?php echo $this->Form->create('User',array('class'=>'form-horizontal', 'id' => 'validation', 'novalidate'=>true));?>
        <div class="control-group">
            <label for="inputEmail">Email *</label>                
            <?php echo $this->Form->input('email', array('class'=>'validate[required,custom[email]]', 'type'=>'text', 'label'=>false)); ?>
        </div>
        <div class="control-group">
            <label for="inputPassword">Password *</label>                
            <?php echo $this->Form->input('password', array('label'=>false, 'class'=>'validate[required]')); ?>
        </div>
        <div class="control-group">
            <?php echo $this->Html->link('Forgot Password ?', array('controller'=>'users','action'=>'forgot_password','label'=>false)); ?>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-block">Sign in</button>
        </div>
    </form>        
</div> 