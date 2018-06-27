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
<?php echo $this->Form->create('Setting', array('id' => 'validation')); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-documents"></div>
        <h1><?php echo $setting['Setting']['description'] ?></h1>
        <div class="clear"></div>
      </div>
      <?php 
	  echo $this->Form->input('id', array('type'=>'hidden', 'value'=> $setting['Setting']['id']));
	  ?>
      <div class="block-fluid">
        <div class="row-form">
          <div class="span3">Description :</div>
          <div class="span9">
            <div class="block-fluid" id="wysiwyg_container">
	            <textarea id="wysiwyg" name="data[Setting][value]" style="height: 300px;"><?php echo $setting['Setting']['value'];?></textarea>
            </div>
            <?php
            if($setting['Setting']['key_value'] == 'birthday_mail_notification'){ ?>
                 <div class="text-block">You can use these variables: <code>{{name}}</code> <code>{{age}}</code> <code>{{tomorrow}}</code></div>
            <?php }
            ?>
            
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

