<script>
function validateForm()
{
	$("#validation").submit(function(){
		var txt = document.getElementById('wysiwyg').value;
		var rex = /(<([^>]+)>)/ig;
		
		if(txt.replace(rex , "") == ''){
			alert('Please Enter Description');
			document.getElementById('wysiwyg').focus();
			return false;
		}
	});
}

</script>

<div class="workplace">
<?php echo $this->Form->create('StaticPage', array('id' => 'validation')); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-documents"></div>
        <h1>Company Rules</h1>
        <div class="clear"></div>
      </div>
      <?php 
	  echo isset($this->data['StaticPage']['id']) ? $this->Form->input('id', array('type'=>'hidden', 'value'=>$this->data['StaticPage']['id'])) : '';
	  ?>
      <div class="block-fluid">
      	<?php  echo $this->Form->input('static_id', array('type'=>'hidden', 'value'=>1));?>
        <div class="row-form">
          <div class="span3">Description :</div>
          <div class="span9">
            <div class="block-fluid" id="wysiwyg_container">
	            <textarea id="wysiwyg" name="data[StaticPage][description]" style="height: 300px;"><?php echo isset($this->data['StaticPage']['description']) ? $this->data['StaticPage']['description'] : '';?></textarea>
            </div>

          </div>
          <div class="clear"></div>
        </div>
        
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
  <div class="row-fluid">                
    <div align="center" class="span12">
      <input type="submit" name="save" id="save" value="Save" class="btn" onclick="validateForm()"/>
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

