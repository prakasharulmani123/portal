<?php /*?><style>
.ui-datepicker-calendar{
	display:block;
}
</style>
<?php */?><?php 
$marray= array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17);//define number of main menus
$open = '';

if($cpage=='dailystatus')
$marray[0]='active';
if($cpage=='myreport')
$marray[1]='active';
if($cpage=='leave')
$marray[2]='active';
if($cpage=='entry')
$marray[3]='active';
if($cpage=='dashboard')
$marray[4]='active';
if($cpage=='permission')
$marray[5]='active';
if($cpage=='month_report'){
$marray[6]='active';
$open = 'active';}
if($cpage=='leave_month_report'){
$marray[7]='active';
$open = 'active';}
if($cpage=='pendingreport')
$marray[8]='active';
if($cpage=='holiday')
$marray[9]='active';
if($cpage=='late_entry')
$marray[10]='active';
if($cpage=='meeting')
$marray[11]='active';
if($cpage=='meeting_su')
$marray[12]='active';
if($cpage=='mycomplaints')
$marray[13]='active';
if($cpage=='theircomplaints')
$marray[14]='active';
if($cpage=='import_attendance')
$marray[15]='active';
if($cpage=='my_attendance')
$marray[16]='active';
if($cpage=='compensation')
$marray[17]='active';
?>
<div class="breadLine">
  <div class="arrow"></div>
  <div class="adminControl active"> Hi, <?php echo $this->Session->read('User.name'); ?></div>
</div>

<div class="admin" style="display:block;">
  <div class="image"> 
	
  <?php 	
  if($this->Session->read('User.photo')==''){ ?>
<a class="fancybox" href="<?php echo $this->base?>/img/logo.png">
 <?php 	echo $this->Html->image('logo.png',array('class'=>'img-polaroid'));  ?></a>
<?php  }
  else{ ?>
<a class="fancybox" href="<?php echo $this->base?>/img/users/original/<?php echo $this->Session->read('User.photo')?>">
<?php echo $this->Html->image('/img/users/small/'.$this->Session->read('User.photo'),array('class'=>'img-polaroid', 'alt'=>'profile-image')); ?></a>
<?php  }
  ?></div>
  <ul class="control">
  <?php 
  	$check_on_off = 0;
	$id = 0;
  	$check_time = $this->requestAction('entries/check_time_in_out'); 
	
	if(!empty($check_time)){
		if($check_time['Entry']['on_off'] == 0){
			$check_on_off = $check_time['Entry']['on_off'];
			$id = 0;
		}
		else{
			$check_on_off = $check_time['Entry']['on_off'];
			$id = $check_time['Entry']['id'];
		}
	}
	else{
		$check = 0;	
		$id = 0;
	}
  ?>
  <?php 
  $is_mobile = $this->requestAction(array('controller' => 'entries', 'action' => 'isMobile'));
  if($is_mobile == 0){
  ?>
    <li><span class="icon-time"></span><a href="<?php echo $this->base?>/entries/entry/<?php echo $id?>">
		<?php if($check_on_off == 0){echo 'Timer On';}elseif($check_on_off != 0){echo "Timer Off";}?></a></li>
  <?php }?>
    <li><span class="icon-cog"></span><a href="<?php echo $this->base?>/users/profile"> Profile</a></li>
    <li><span class="icon-lock"></span><a href="<?php echo $this->base?>/users/password"> Change Password</a></li>
   <?php 
      if($this->Session->read('role') || $this->Session->read('super_user') ){
   ?>
    <li><span class="icon-share-alt"></span> <a href="<?php echo $this->base?>/users/adminback"> Back to Admin</a></li>
    <?php }else{
     ?>
    <li><span class="icon-share-alt"></span> <a href="<?php echo $this->base?>/users/logout"> Logout</a></li>
    <?php } ?>
  </ul>
</div>

<ul class="navigation">
    <?php if($this->Session->read('User.employee_type') == 'P'){ ?>
    <li class="<?php echo $marray[4];?>"> <a href="<?php  echo  $this->base?>/users/dashboard"> <span class="isw-grid"></span><span class="text">DashBoard</span> </a> </li>
    <li class="<?php echo $marray[0];?>"> <a href="<?php  echo  $this->base?>/dailystatus"> <span class="isw-empty_document"></span><span class="text">Daily Status Report</span> </a> </li>
    <li class="<?php echo $marray[1];?>"> <a href="<?php  echo  $this->base?>/dailystatus/reports"> <span class="isw-text_document"></span><span class="text">My Reports</span> </a> </li>
    
    <?php $check_pending_reports = $this->requestAction('pending_reports/check_user_pending_reports');?>
    
    <?php if(!empty($check_pending_reports)){?>
    <li class="<?php echo $marray[8];?>"> <a href="<?php  echo  $this->base?>/pending_reports"> <span class="isw-plus"></span><span class="text">Pending Reports</span> </a> </li>
    <?php } ?>
    
    <li class="<?php echo $marray[2];?>"> <a href="<?php  echo  $this->base?>/leave"> <span class="isw-target"></span><span class="text">Leave Request</span> </a> </li>
     <li class="<?php echo $marray[17];?>"> <a href="<?php  echo  $this->base?>/compensations"> <span class="isw-target"></span><span class="text">Compensation Leave</span> </a> </li>
    <li class="<?php echo $marray[5];?>"> <a href="<?php  echo  $this->base?>/permission"> <span class="isw-list"></span><span class="text">Permission Request</span> </a> 
</li>
    <?php } ?>
    <li class="<?php echo $marray[3];?>"> <a href="<?php  echo  $this->base?>/entries"> <span class="isw-time"></span><span class="text">Time In / Time Out</span> </a> </li>
    <?php if($this->Session->read('User.employee_type') == 'P'){ ?>
    <li class="openable<?php echo " ".$open;?>">
        <a href="#"><span class="isw-calendar"></span><span class="text">Monthly Reports</span></a>
        <ul>
	        <li class="<?php echo $marray[6];?>"> <a href="<?php  echo  $this->base?>/dailystatus/monthly_report"><span class="icon-calendar"></span><span class="text">Daily Reports</span> </a> </li>
            <li class="<?php echo $marray[7];?>"><a href="<?php  echo  $this->base?>/leave/monthly_report"><span class="icon-calendar"></span><span class="text">Leave Reports</span></a></li>
        </ul>                
    </li>

    <li class="<?php echo $marray[9];?>"> <a href="<?php  echo  $this->base?>/holidays"> <span class="isw-bookmark"></span><span class="text">Offical Holidays <?php echo date('Y')?></span> </a> </li>

    <li class="<?php echo $marray[10];?>"> <a href="<?php  echo  $this->base?>/late_entries"> <span class="isw-archive"></span><span class="text">Late Entries</span> </a> </li>
    
    <li class="<?php echo $marray[11];?>"> <a href="<?php  echo  $this->base?>/meetings"> <span class="isw-chats"></span><span class="text">Meetings</span> </a> </li>
    <?php if($this->Session->read('User.super_user') == 1){ ?>
    <li class="<?php echo $marray[12];?>"> <a href="<?php  echo  $this->base?>/meetings/meetings_su"> <span class="isw-chats"></span><span class="text">View Employee Meetings</span> </a> </li>
    <?php } ?>
    <li class="<?php echo $marray[13];?>"> <a href="<?php  echo  $this->base?>/user_complaints"> <span class="isw-right"></span><span class="text">My Complaints</span> </a> </li>
    <li class="<?php echo $marray[14];?>"> <a href="<?php  echo  $this->base?>/user_complaints/theirs"> <span class="isw-left"></span><span class="text">Their Complaints</span> </a> </li>
    <li class="<?php echo $marray[16];?>"> <a href="<?php  echo  $this->base?>/user_attendances"> <span class="isw-time"></span><span class="text">My Attendance</span> </a> </li>
    <?php if($this->Session->read('User.super_user') == 1 || in_array($this->Session->read('User.id'), array(26))){ ?>
    <li class="<?php echo $marray[15];?>"> <a href="<?php  echo  $this->base?>/user_attendances/import_attendance"> <span class="isw-cloud"></span><span class="text">Import Attendance</span> </a> </li>
    <?php } ?>
    <?php } ?>
</ul>

<div class="dr"><span></span></div>

<div class="widget-fluid">
  <div id="menuDatepicker"></div>
</div>

<div class="dr"><span></span></div>
