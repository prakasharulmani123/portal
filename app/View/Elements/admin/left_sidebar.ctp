<?php 
$marray= array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19);//define number of main menus
$open = $open1 = $open2 = $open3 = $open4 = $open5 = "";

if($cpage=='dashboard')
$marray[0]='active';
if($cpage=='employee'){
$open5 = 'active';
$marray[1]='active';
}
if($cpage=='dailyreports')
$marray[2]='active';
if($cpage=='email')
$marray[3]='active';
if($cpage=='category'){
$marray[4]='active';
$open = "active";}
if($cpage=='work'){
$marray[5]='active';
$open = "active";}
if($cpage=='leave'){
$marray[6]='active';
$open3 = 'active';}
if($cpage=='entry')
$marray[7]='active';
if($cpage=='permission'){
$open4 = 'active';
$marray[8]='active';}
if($cpage=='month_report'){
$marray[9]='active';
$open1 = "active";}
if($cpage=='holiday')
$marray[10]='active';
if($cpage=='leave_month_report'){
$marray[11]='active';
$open1 = "active";}
if($cpage=='staticpage'){
$marray[12]='active';}
if($cpage=='pendingreport'){
$marray[13]='active';
$open2 = "active";}
if($cpage=='late_entry')
$marray[14]='active';
if($cpage=='meeting')
$marray[15]='active';
if($cpage=='timings')
$marray[16]='active';
if($cpage=='my_attendance')
$marray[17]='active';
if($cpage=='import_attendance')
$marray[18]='active';
if($cpage=='mycomplaints')
$marray[19]='active';
?>

<div class="breadLine">
  <div class="arrow"></div>
  <div class="adminControl active"> Hi, <?php echo $this->Session->read('User.name'); ?></div>
</div>

<div class="admin" style="display:block;">
  <div class="image"> 
  <?php
  if($this->Session->check('User.photo')==false){ 
  	echo $this->Html->image('admin/users/logo.png',array('class'=>'img-polaroid')); 
  }
  else{
	echo $this->Html->image('/img/users/'.$this->Session->read('User.photo'),array('class'=>'img-polaroid', 'alt'=>'profile-image'));
  }
	?></div>
  <ul class="control">
    <li><span class="icon-cog"></span><a href="<?php echo $this->base?>/admin/users/profile">Profile</a></li>
    <li><span class="icon-share-alt"></span> <a href="<?php echo $this->base?>/users/logout">Logout</a></li>
  </ul>
</div>

<ul class="navigation">
  <li class="<?php echo $marray[0]; ?>"> <a href="<?php echo  $this->base?>/admin/users"> <span class="isw-grid"></span><span class="text">Dashboard</span> </a> </li>

    <li class="openable<?php echo " ".$open5?>">
        <a href="#">
            <span class="isw-users"></span><span class="text">Employees</span>
        </a>
        <ul>
            <li class="<?php echo $marray[1];?>">
                <a href="<?php echo  $this->base?>/admin/users/employee/1">
                    <span class="icon-user"></span><span class="text" style="padding-left:1px;">Active</span>
                </a>                  
            </li>          
            <li class="<?php echo $marray[1];?>">
                <a href="<?php echo  $this->base?>/admin/users/employee/0">
                    <span class="icon-user"></span><span class="text" style="padding-left:1px;">In-active</span>
                </a>                  
            </li>                     
        </ul>                
   </li>

  <li class="<?php echo $marray[2]; ?>"> <a href="<?php echo  $this->base?>/admin/dailystatus"> <span class="isw-text_document"></span><span class="text">Daily Reports</span> </a> </li>
  
    <li class="openable<?php echo " ".$open2?>">
        <a href="#">
            <span class="isw-plus"></span><span class="text">Pending Reports</span>
        </a>
        <ul>
            <li class="<?php echo $marray[13];?>">
                <a href="<?php echo  $this->base?>/admin/pending_reports">
                    <span class="icon-tasks"></span><span class="text">View</span>
                </a>                  
            </li>          
            <li class="<?php echo $marray[13];?>">
                <a href="<?php echo  $this->base?>/admin/pending_reports/add">
                    <span class="icon-tasks"></span><span class="text" style="padding-left:1px;">Add</span>
                </a>                  
            </li>                     
        </ul>                
   </li>
   
  <li class="<?php echo $marray[3]; ?>"> <a href="<?php echo  $this->base?>/admin/emails"> <span class="isw-mail"></span><span class="text">Email</span> </a> </li>
    <li class="openable<?php echo " ".$open;?>">
        <a href="#">
            <span class="isw-graph"></span><span class="text">Project</span>
        </a>
        <ul>
            <li class="<?php echo $marray[4];?>">
                <a href="<?php echo  $this->base?>/admin/categories">
                    <span class="icon-th-large"></span><span class="text">Category</span>
                </a>                  
            </li>          
            <li class="<?php echo $marray[5];?>">
                <a href="<?php echo  $this->base?>/admin/works">
                    <span class=" icon-briefcase"></span><span class="text" style="padding-left:1px;">Work</span>
                </a>                  
            </li>                     
        </ul>                
   </li>
   
   <li class="openable<?php echo " ".$open3?>">
        <a href="#">
            <span class="isw-target"></span><span class="text">Leave</span>
        </a>
        <ul>
            <li class="<?php echo $marray[6];?>">
                <a href="<?php echo  $this->base?>/admin/leave">
                    <span class="icon-tasks"></span><span class="text">Leave Requests</span>
                </a>                  
            </li>          
            <li class="<?php echo $marray[6];?>">
                <a href="<?php echo  $this->base?>/admin/leave/customize_leave">
                    <span class="icon-tasks"></span><span class="text" style="padding-left:1px;">Casual / Paid Toggle</span>
                </a>                  
            </li>                     
            <li class="<?php echo $marray[6];?>">
                <a href="<?php echo  $this->base?>/admin/leave/add">
                    <span class="icon-tasks"></span><span class="text" style="padding-left:1px;">Add Leave</span>
                </a>                  
            </li>                     
        </ul>                
   </li>

	<li class="openable<?php echo " ".$open4?>">
        <a href="#">
            <span class="isw-list"></span><span class="text">Permission</span>
        </a>
        <ul>
            <li class="<?php echo $marray[8];?>">
                <a href="<?php echo  $this->base?>/admin/permission">
                    <span class="icon-tasks"></span><span class="text">Permission Requests</span>
                </a>                  
            </li>          
            <li class="<?php echo $marray[8];?>">
                <a href="<?php echo  $this->base?>/admin/permission/add">
                    <span class="icon-tasks"></span><span class="text">Add Permission</span>
                </a>                  
            </li>          
        </ul>                
   </li>             
  <?php /*?><li class="<?php echo $marray[6]; ?>"> <a href="<?php echo  $this->base?>/admin/leave"> <span class="isw-target"></span><span class="text">Leave Requests</span> </a> </li><?php */?>
  <?php /*?><li class="<?php echo $marray[8]; ?>"> <a href="<?php echo  $this->base?>/admin/permission"> <span class="isw-list"></span><span class="text">Permission Requests</span> </a> </li><?php */?>
  
  <li class="<?php echo $marray[7]; ?>"> <a href="<?php echo  $this->base?>/admin/entries"> <span class="isw-time"></span><span class="text">Time In / Out</span> </a> </li>
    <li class="openable<?php echo " ".$open1;?>">
        <a href="#"><span class="isw-calendar"></span><span class="text">Monthly Reports</span></a>
        <ul>
	        <li class="<?php echo $marray[9];?>"> <a href="<?php  echo  $this->base?>/admin/dailystatus/monthly_report"><span class="icon-calendar"></span><span class="text">Daily Reports</span> </a> </li>
            <li class="<?php echo $marray[11];?>"><a href="<?php  echo  $this->base?>/admin/leave/monthly_report"><span class="icon-calendar"></span><span class="text">Leave Reports</span></a></li>
        </ul>                
    </li>
  <li class="<?php echo $marray[10]; ?>"> <a href="<?php echo  $this->base?>/admin/holidays"> <span class="isw-bookmark"></span><span class="text">Holidays</span> </a>	  <li class="<?php echo $marray[12]; ?>"> <a href="<?php echo  $this->base?>/admin/static_pages"> <span class="isw-documents"></span><span class="text">Company Rules</span> </a> </li>
  
    <li class="<?php echo $marray[14]; ?>"> <a href="<?php echo  $this->base?>/admin/late_entries"> <span class="isw-archive"></span><span class="text">Late Entries</span> </a> </li>

    <li class="<?php echo $marray[16]; ?>"> <a href="<?php echo  $this->base?>/admin/users/timings"> <span class="isw-time"></span><span class="text">Office Timings</span> </a> </li>
    
    <li class="<?php echo $marray[15]; ?>"> <a href="<?php echo  $this->base?>/admin/meetings"> <span class="isw-chat"></span><span class="text">Meetings</span> </a> </li>

    <li class="<?php echo $marray[17]; ?>"> <a href="<?php echo  $this->base?>/admin/user_attendances"> <span class="isw-time"></span><span class="text">Attendance</span> </a> </li>

    <li class="<?php echo $marray[18];?>"> <a href="<?php  echo  $this->base?>/admin/user_attendances/import_attendance"> <span class="isw-cloud"></span><span class="text">Import Attendance</span> </a> </li>

    <li class="<?php echo $marray[19];?>"> <a href="<?php  echo  $this->base?>/admin/user_complaints"> <span class="isw-right"></span><span class="text">Complaints</span> </a> </li>
</ul>

<div class="dr"><span></span></div>

<div class="widget-fluid">
  <div id="menuDatepicker"></div>
</div>

<div class="dr"><span></span></div>

