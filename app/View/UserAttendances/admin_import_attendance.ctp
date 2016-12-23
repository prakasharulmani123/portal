<script>
$(function() {
	$("#UserImportDate").datepicker({
		dateFormat: 'dd-mm-yy',
		altFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: "1950:(new Date).getFullYear()"
  });
});
</script>
<div class="workplace">
<?php echo $this->Form->create('User', array('id' => 'validation', 'type'=>'file')); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-cloud"></div>
        <h1>Import Attendance</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
        <div class="row-form">
          <div class="span3">Date*:</div>
          <div class="span9">
            <?php echo $this->Form->input('import_date', array('type'=>'text', 'label'=>false,'style'=>'width:80px', 'value'=>date('d-m-Y', strtotime('-1 days')))); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">CSV*:</div>
          <div class="span9">
           <?php echo $this->Form->input('upload', array('type' => 'file', 'label'=>false));?>
          </div>
          <div class="clear"></div>
        </div>
        
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
  <div class="row-fluid">                
    <div align="center" class="span12">
      <input type="submit" name="save" id="save" value="Import" class="btn" />
    </div>
  </div>
<?php echo $this->Form->end(); ?>
</div>

