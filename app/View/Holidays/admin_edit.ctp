<script>
$(document).ready(function() {
	$("#HolidayDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
	});
});
</script>

<div class="workplace">
<?php echo $this->Form->create('Holiday', array('id' => 'validation')); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-bookmark"></div>
        <h1>Edit Holiday</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
      
        <div class="row-form">
          <div class="span3">Date *:</div>
          <div class="span2">
           <?php echo $this->Form->input('date', array('type'=>'text', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Name *:</div>
          <div class="span6">
           <?php echo $this->Form->input('name', array('class' => 'validate[required]','label'=>false)); ?>
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
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/admin/holidays'" />          
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

