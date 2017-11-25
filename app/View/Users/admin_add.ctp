<script>
$(function() {
        $("#UserJoinedOn").datepicker({
			dateFormat: 'dd-mm-yy',
      		altFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
	  });
        $("#UserDateOfBirth").datepicker({
			dateFormat: 'dd-mm-yy',
      		altFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange: "1950:(new Date).getFullYear()"
	  });
});

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
	
	AJAX.open("get",  "/cities/get_all_cities/" + id, true);
//	AJAX.open("get",  BaseURL+"cities/get_all_cities/" + id, true);
	AJAX.send(null);
}

</script>
<div class="workplace">
<?php echo $this->Form->create('User', array('id' => 'validation', 'type'=>'file')); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-users"></div>
        <h1>Add Employee</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
        <div class="row-form">
          <div class="span3">Name*:</div>
          <div class="span9">
           <?php echo $this->Form->input('employee_name', array('class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
          <div class="row-form">
              <div class="span3">Employee type*:</div>
              <div class="span9">
                  <?php echo $this->Form->input('employee_type', array('type'=>'select', 'options'=> array('P'=>'Permanent', 'T'=>'Trainee'), 'label'=>false, 'empty'=>'Select', 'value' => isset($_GET['employee_type']) ? $_GET['employee_type'] : 'P')); ?>
              </div>
              <div class="clear"></div>
          </div>

        <div class="row-form">
          <div class="span3">Sex:</div>
          <div class="span9">
           <?php echo $this->Form->input('sex', array('type'=>'select', 'options'=> array('M'=>'Male', 'F'=>'Female'),'label'=>false, 'empty'=>'Select')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">D.O.B:</div>
          <div class="span9">
            <?php echo $this->Form->input('date_of_birth', array('type'=>'text', 'label'=>false, 'style'=>'width:80px')); ?>
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
          <div class="span3">Mother's Name:</div>
          <div class="span9">
           <?php echo $this->Form->input('mothername', array('label'=>false)); ?>
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
          <div class="span3">Address:</div>
          <div class="span9">
           <?php echo $this->Form->input('address', array('label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Country*:</div>
          <div class="span9">
           <?php echo $this->Form->input('country_id', array('label'=>false, 'empty'=>'select country', 'class' => 'validate[required]')); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">State*:</div>
          <div class="span9">
           <?php echo $this->Form->input('state_id', array('label'=>false, 'onchange'=>'get_cities(this.value)', 'empty'=>'select state', 'class' => 'validate[required]')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <?php 
/*		$cities = $this->requestAction('Cities/get_cities', array('pass'=>array('City.State_id'=>$this->data['User']['state_id']))); 
		
		$all_city = array();
		foreach($cities as $city){
			$all_city[$city['City']['id']] = $city['City']['city'];
		}
*/		?>
        <div class="row-form">
          <div class="span3">City*:</div>
          <div class="span9" id="cityid">
           <?php echo $this->Form->input('city_id', array('label'=>false, 'empty'=>'select city', 'class' => 'validate[required]')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Phone:</div>
          <div class="span9">
           <?php echo $this->Form->input('phone', array('label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">E-mail*:</div>
          <div class="span9">
           <?php echo $this->Form->input('email', array('type'=>'text', 'class' => 'validate[required,custom[email]]', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Skype:</div>
          <div class="span9">
           <?php echo $this->Form->input('skype', array('type'=>'text', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Passport :</div>
          <div class="span9">
           <?php echo $this->Form->input('passport', array('type'=>'text', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Designation*:</div>
          <div class="span9">
            <?php echo $this->Form->input('designation', array('label'=>false, 'class' => 'validate[required]')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Joined on*:</div>
          <div class="span9">
            <?php echo $this->Form->input('joined_on', array('type'=>'text', 'label'=>false, 'class' => 'validate[required]', 'style'=>'width:80px')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Photo:</div>
          <div class="span9">
           <?php echo $this->Form->input('upload', array('type' => 'file', 'label'=>false));?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Active:</div>
          <div class="span9">
            <?php echo $this->Form->input('active', array('type' => 'checkbox','label'=>false, 'checked'=>'checked')); ?>
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
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/admin/users/employee'" />          
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

