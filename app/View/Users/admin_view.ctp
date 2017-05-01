<div class="workplace">
    <div class="row-fluid">                
        <div align="right" class="span12">
          <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/admin/users/employee/1'" />          
        </div>
    </div>

  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-users"></div>
        <h1>View Employee</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
     
        <div class="row-form">
           <a class="fancybox" href="<?php echo $this->base?>/img/users/original/<?php echo $this->data['User']['photo']?>"><?php echo $this->Html->image('users/small/'.$this->data['User']['photo'], array('alt' => 'image', 'height'=>'180',  'width'=>'180'));?></a>
        </div>

        <div class="row-form">
          <div class="span3">Name:</div>
          <div class="span9">
           <b><?php echo $this->data['User']['employee_name']; ?></b>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Sex:</div>
          <div class="span9">
           <?php if($this->data['User']['sex']=='M'){echo 'Male';}else{echo 'Female';} ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">D.O.B:</div>
          <div class="span9">
            <?php echo $this->data['User']['date_of_birth']; ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Father's Name:</div>
          <div class="span9">
           <?php echo $this->data['User']['fathername']; ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Mother's Name :</div>
          <div class="span9">
           <?php echo $this->data['User']['mothername']; ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Spouse Name:</div>
          <div class="span9">
           <?php echo $this->data['User']['spousename']; ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Address :</div>
          <div class="span9">
           <?php echo $this->data['User']['address']; ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Country :</div>
          <div class="span9">
           <?php 
		   foreach($countries as $key => $country){
			   if($key == $this->data['User']['country_id']){
				echo $country;
			   }
		   }
		   ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">State :</div>
          <div class="span9">
          <?php
		   foreach($states as $key => $state){
			   if($key == $this->data['User']['state_id']){
				echo $state;
			   }
		   }
		   ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">City :</div>
          <div class="span9" id="cityid">
          <?php
		   foreach($cities as $key => $city){
			   if($key == $this->data['User']['city_id']){
				echo $city;
			   }
		   }
		   ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Phone :</div>
          <div class="span9">
           <?php echo $this->data['User']['phone']; ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">E-mail:</div>
          <div class="span9">
           <?php echo $this->data['User']['email']; ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Skype :</div>
          <div class="span9">
           <?php echo $this->data['User']['skype']; ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Passport :</div>
          <div class="span9">
           <?php echo $this->data['User']['passport']; ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Designation :</div>
          <div class="span9">
            <?php echo $this->data['User']['designation']; ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Joined on :</div>
          <div class="span9">
            <?php echo $this->data['User']['joined_on']; ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Active:</div>
          <div class="span9">
            <?php echo $this->Html->image('icon_' . $this->data['User']['active'] . '.png'); ?>
          </div>
          <div class="clear"></div>
        </div>
        
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
        
      </div>
    </div>
  </div>
</div>
