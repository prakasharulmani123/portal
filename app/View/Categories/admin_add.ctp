<div class="workplace">
<?php echo $this->Form->create('Category', array('id' => 'validation')); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-grid"></div>
        <h1>Add Category</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
      
        <div class="row-form">
          <div class="span3">Category *:</div>
          <div class="span9">
           <?php echo $this->Form->input('category', array('class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
  <div class="row-fluid">                
    <div align="center" class="span12">
      <input type="submit" name="save" id="save" value="Add" class="btn" />
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/admin/categories'" />          
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

