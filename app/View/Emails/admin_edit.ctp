<script type="text/javascript">
$(document).ready(function(){
	$("#validation").submit(function(){
    var checked = $('.required:checked').size();
    if (checked == 0){
        alert("Please check at least one option");
        return false;
	    }
	});
});
</script>

<div class="workplace">
<?php echo $this->Form->create('Email', array('id' => 'validation', 'novalidate'=>true)); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-mail"></div>
        <h1>Add Email</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
        <div class="row-form">
          <div class="span3">Name*:</div>
          <div class="span9">
           <?php echo $this->Form->input('name', array('class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Email*:</div>
          <div class="span9">
           <?php echo $this->Form->input('email', array('type'=>'text','class' => 'validate[required,custom[email]]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">To/Cc*:</div>
          <div class="span9">
           <?php echo $this->Form->input('to_cc', array('type'=>'select', 'options'=>array('to'=>'To','cc'=>'Cc', 'bcc'=>'Bcc'),'class' => 'validate[required]','label'=>false, 'empty'=>'select')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <?php
		$options = $this->requestAction('emails/get_email_options');
		$array = explode(',',$this->data['Email']['options']);
		?>
        <div class="row-form">
          <div class="span3">Options*:</div>
          <div class="span9">
          <?php $i = 0; foreach($options as $key => $value){
                ?>
          <?php
			$check = array_search($key, $array); 
			if($check != ""){$checked = 'checked';}else{$checked = '';}
		  ?>
            <label class="checkbox inline">
            <?php echo $this->Form->input('options', array('id'=>'checkbox-'.$key, 'type'=>'checkbox', 'name'=>'data[Email][options]['.$key.']', 'value'=>$key, 'label'=>false, 'div'=>false, 'class'=>'required', $checked)).$value; ?>
            </label>
           <?php }?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Active:</div>
          <div class="span9">
            <?php echo $this->Form->input('active', array('type' => 'checkbox','label'=>false)); ?>
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
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/admin/emails'" />          
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

