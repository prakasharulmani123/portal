<script>
$(document).ready(function() {
	$("#MeetingMeetingDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth:true,
		changeYear:true
	}).datepicker("setDate", new Date());
	
	$.ajax({
		url: BaseURL+"/projects/get_meeting_projects/",
		beforeSend: function(){
		},
		dataType: "JSON",
		success: function(data){
			var projectlist = new Array();
			$.each( data, function(i, obj) {
			  projectlist.push( obj)
			});
			
			$( "#MeetingProject" ).autocomplete({
				source: projectlist
			});


		}
	});
});
</script>

<style>
.ui-autocomplete {
position: absolute;
top: 100%;
left: 0;
z-index: 1000;
float: left;
display: none;
min-width: 160px;
_width: 160px;
padding: 4px 0;
margin: 2px 0 0 0;
list-style: none;
background-color: #ffffff;
border-color: #ccc;
border-color: rgba(0, 0, 0, 0.2);
border-style: solid;
border-width: 1px;
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px;
-webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
-moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
-webkit-background-clip: padding-box;
-moz-background-clip: padding;
background-clip: padding-box;
*border-right-width: 2px;
*border-bottom-width: 2px;
 
.ui-menu-item > a.ui-corner-all {
display: block;
padding: 3px 15px;
clear: both;
font-weight: normal;
line-height: 18px;
color: #555555;
white-space: nowrap;
 
&.ui-state-hover, &.ui-state-active {
color: #fff;
text-decoration: none;
background-color: #395E88;
border-radius: 0px;
-webkit-border-radius: 0px;
-moz-border-radius: 0px;
background-image: none;
}
}
}

.ui-autocomplete .ui-state-hover {
	padding:0px !important;
	text-decoration:none;
	cursor:pointer;
}
</style>

<div class="workplace">
<?php 
echo $this->Form->create('Meeting', array('id' => 'validation'));
echo $this->Form->hidden('project_id');
echo $this->Form->hidden('user_id', array('value' => $this->Session->read('User.id')));
 ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-chats"></div>
        <h1>Add Meeting</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
      
		<?php
			$hours = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12');
			$minutes = array('00'=>'00', '05'=>'05', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30', '35'=>'35', '40'=>'40', '45'=>'45', '50'=>'50', '55'=>'55');
			$mer = array('am'=>'am','pm'=>'pm');
        ?>

        <div class="row-form">
          <div class="span3">Meeting Date *:</div>
          <div class="span2">
           <?php echo $this->Form->input('meeting_date', array('type' =>'text', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Meeting Location *:</div>
          <div class="span9">
           <?php echo $this->Form->input('meeting_location', array('type' =>'text', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Project *:</div>
          <div class="span9">
           <?php echo $this->Form->input('project', array('type' =>'text', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Building *:</div>
          <div class="span9">
           <?php echo $this->Form->input('building', array('type' =>'text', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Web Address *:</div>
          <div class="span9">
           <?php echo $this->Form->input('web_address', array('type' =>'text', 'class' => 'validate[required, custom[url]]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Meeting Scribe *:</div>
          <div class="span9">
           <?php echo $this->Form->input('meeting_scribe', array('type' =>'text', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Meeting Schedule Start Time *:</div>
            <?php echo $this->Form->input('meeting_schedule_start', array('type'=>'hidden')); ?>
          <div class="span1">
            <?php echo $this->Form->input('meeting_schedule_start_hours', array('class' => 'validate[required]','type'=>'select', 'options'=>$hours, 'label'=>false, 'empty'=>'hours', 'name'=>'data[Meeting][meeting_schedule_start][hours]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('meeting_schedule_start_minutes', array('class' => 'validate[required]','type'=>'select', 'options'=>$minutes, 'label'=>false, 'empty'=>'minutes', 'name'=>'data[Meeting][meeting_schedule_start][minutes]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('meeting_schedule_start_mer', array('class' => 'validate[required]','type'=>'select', 'options'=>$mer, 'label'=>false, 'empty'=>'meridian', 'name'=>'data[Meeting][meeting_schedule_start][meridian]')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Meeting Schedule End Time *:</div>
            <?php echo $this->Form->input('meeting_schedule_end', array('type'=>'hidden')); ?>
          <div class="span1">
            <?php echo $this->Form->input('meeting_schedule_end_hours', array('class' => 'validate[required]','type'=>'select', 'options'=>$hours, 'label'=>false, 'empty'=>'hours', 'name'=>'data[Meeting][meeting_schedule_end][hours]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('meeting_schedule_end_minutes', array('class' => 'validate[required]','type'=>'select', 'options'=>$minutes, 'label'=>false, 'empty'=>'minutes', 'name'=>'data[Meeting][meeting_schedule_end][minutes]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('meeting_schedule_end_mer', array('class' => 'validate[required]','type'=>'select', 'options'=>$mer, 'label'=>false, 'empty'=>'meridian', 'name'=>'data[Meeting][meeting_schedule_end][meridian]')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Agenda *:</div>
          <div class="span9">
           <?php echo $this->Form->input('agenda', array('type' =>'textarea', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
        <div class="row-form">
          <div class="span3">Meeting Actual Start Time*:</div>
            <?php echo $this->Form->input('meeting_actual_start', array('type'=>'hidden')); ?>
          <div class="span1">
            <?php echo $this->Form->input('meeting_actual_start_hours', array('class' => 'validate[required]','type'=>'select', 'options'=>$hours, 'label'=>false, 'empty'=>'hours', 'name'=>'data[Meeting][meeting_actual_start][hours]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('meeting_actual_start_minutes', array('class' => 'validate[required]','type'=>'select', 'options'=>$minutes, 'label'=>false, 'empty'=>'minutes', 'name'=>'data[Meeting][meeting_actual_start][minutes]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('meeting_actual_start_mer', array('class' => 'validate[required]','type'=>'select', 'options'=>$mer, 'label'=>false, 'empty'=>'meridian', 'name'=>'data[Meeting][meeting_actual_start][meridian]')); ?>
          </div>
          <div class="clear"></div>
        </div>


        <div class="row-form">
          <div class="span3">Meeting Actual End Time*:</div>
            <?php echo $this->Form->input('meeting_actual_end', array('type'=>'hidden')); ?>
          <div class="span1">
            <?php echo $this->Form->input('meeting_actual_end_hours', array('class' => 'validate[required]','type'=>'select', 'options'=>$hours, 'label'=>false, 'empty'=>'hours', 'name'=>'data[Meeting][meeting_actual_end][hours]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('meeting_actual_end_minutes', array('class' => 'validate[required]','type'=>'select', 'options'=>$minutes, 'label'=>false, 'empty'=>'minutes', 'name'=>'data[Meeting][meeting_actual_end][minutes]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('meeting_actual_end_mer', array('class' => 'validate[required]','type'=>'select', 'options'=>$mer, 'label'=>false, 'empty'=>'meridian', 'name'=>'data[Meeting][meeting_actual_end][meridian]')); ?>
          </div>
          <div class="clear"></div>
        </div>

        

        <div class="row-form">
          <div class="span3">Action *:</div>
          <div class="span9">
           <?php echo $this->Form->input('action', array('type' =>'textarea', 'class' => 'validate[required]','label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>

        <div class="row-form">
          <div class="span3">Next Meeting *:</div>
          <div class="span9">
           <?php echo $this->Form->input('next_meeting', array('type' =>'text', 'class' => 'validate[required]','label'=>false)); ?>
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
      <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href='<?php echo $this->base; ?>/meetings'" />          
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

