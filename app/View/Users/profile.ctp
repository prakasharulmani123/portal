<script>
$(function() {
	$("#UserDateOfBirth").datepicker({
		dateFormat: 'dd-mm-yy',
		altFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: "1950:(new Date).getFullYear()"
  });
});
</script>
<script type="text/javascript">
function get_cities(id)
{
	var AJAX;
	try
	{  
		AJAX = new XMLHttpRequest(); 
	}
	catch(e)
	{  
		try
		{    
			AJAX = new ActiveXObject("Msxml2.XMLHTTP");    
		}
		catch(e)
		{    
			try
			{
				AJAX = new ActiveXObject("Microsoft.XMLHTTP");      
			}
			catch(e)
			{      
				alert("Your browser does not support AJAX.");      
				return false;      
			}    
		}  
	}
	
	AJAX.onreadystatechange = function()
	{
		if(AJAX.readyState == 4)
		{
			if(AJAX.status == 200)
			{
				var ajax_result = AJAX.responseText;
				document.getElementById("cityid").innerHTML = ajax_result; 
			}
			else
			{
				alert("Error: "+ AJAX.statusText +" "+ AJAX.status);
			}
		}  
	}
	
	AJAX.open("get",  "/portal/cities/get_all_cities/" + id, true);
//	AJAX.open("get",  BaseURL+"cities/get_all_cities/" + id, true);
	AJAX.send(null);
}
</script>

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
           <a class="fancybox" href="<?php echo $this->base?>/img/users/original/<?php echo $this->data['User']['photo']?>"><?php echo $this->Html->image('users/small/'.$this->data['User']['photo'], array('alt' => 'image', 'height'=>'180',  'width'=>'180'));?></a>
        </div>
        
        <div class="row-form">
          <div class="span3">Name*:</div>
          <div class="span9">
           <?php echo $this->Form->input('employee_name', array('type'=>'text', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">D.O.B*:</div>
          <div class="span9">
            <?php echo $this->Form->input('date_of_birth', array('type'=>'text', 'label'=>false,'style'=>'width:80px', 'value'=>date('d-m-Y', strtotime($this->data['User']['date_of_birth'])))); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Father's Name*:</div>
          <div class="span9">
           <?php echo $this->Form->input('fathername', array('class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Mother's Name*:</div>
          <div class="span9">
           <?php echo $this->Form->input('mothername', array('class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Spouse Name:</div>
          <div class="span9">
           <?php echo $this->Form->input('spousename', array('label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Address*:</div>
          <div class="span9">
           <?php echo $this->Form->input('address', array('class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Country*:</div>
          <div class="span9">
           <?php echo $this->Form->input('country_id', array('class' => 'validate[required]','label'=>false, 'empty'=>'select country')); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">State*:</div>
          <div class="span9">
           <?php echo $this->Form->input('state_id', array('class' => 'validate[required]','label'=>false, 'onchange'=>'get_cities(this.value)', 'empty'=>'select state')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <?php 
		$cities = $this->requestAction('Cities/get_cities', array('pass'=>array('City.State_id'=>$this->data['User']['state_id']))); 
		
		$all_city = array();
		foreach($cities as $city){
			$all_city[$city['City']['id']] = $city['City']['city'];
		}
		?>
        <div class="row-form">
          <div class="span3">City*:</div>
          <div class="span9" id="cityid">
           <?php echo $this->Form->input('city_id', array('options'=>$all_city, 'class' => 'validate[required]','label'=>false, 'empty'=>'select city', 'value'=>$this->data['User']['city_id'])); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Phone*:</div>
          <div class="span9">
           <?php echo $this->Form->input('phone', array('class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">E-mail:</div>
          <div class="span9">
           <?php echo $this->Form->input('email', array('type'=>'text','label'=>false, 'readonly')); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Skype*:</div>
          <div class="span9">
           <?php echo $this->Form->input('skype', array('type'=>'text', 'class' => 'validate[required]', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Passport No.:</div>
          <div class="span9">
           <?php echo $this->Form->input('passport', array('type'=>'text', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Pancard No.:</div>
          <div class="span9">
           <?php echo $this->Form->input('pancard', array('type'=>'text', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

          <div class="row-form">
              <div class="span3">Aadhaar Card Number:</div>
              <div class="span9">
                  <?php echo $this->Form->input('aadharid', array('type'=>'text', 'label'=>false)); ?>
              </div>
              <div class="clear"></div>
          </div>

          <div class="row-form">
              <div class="span3">Emergency Contact Number:</div>
              <div class="span9">
                  <?php echo $this->Form->input('emergency_contact', array('type'=>'text', 'label'=>false)); ?>
              </div>
              <div class="clear"></div>
          </div>

<!--        <div class="row-form">
          <div class="span3">Password:</div>
          <div class="span9">
           <?php // echo $this->Form->input('password', array('type'=>'password', 'value'=>'', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
-->        
        <div class="row-form">
          <div class="span3">Photo:</div>
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
      <input type="submit" name="save" id="save" value="Save" class="btn" />
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/dailystatus'" />          
    </div>
  </div>
<?php echo $this->Form->input('pre_file',array('label'=>false, 'size'=>40, 'type'=>'hidden','value'=>$this->data['User']['photo']));?>	
<?php echo $this->Form->end(); ?>
</div>

