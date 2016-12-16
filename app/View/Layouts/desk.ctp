<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo $title_for_layout; ?></title>

<?php 
//echo $this->Html->image('favicon.ico');
//echo $this->Html->meta('icon');

echo $this->Html->css(array('stylesheets'));
echo $this->Html->css(array('fullcalendar.print'),'stylesheet',array('media' => 'print'));
?>
<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'></script>
<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js'></script>
<script>BaseURL = "<?php echo $this->base?>";</script>
<?php 

echo $this->Html->script(array('plugins/jquery/jquery.mousewheel.min','plugins/cookie/jquery.cookies.2.2.0.min','plugins/bootstrap.min','plugins/charts/excanvas.min','plugins/charts/jquery.flot','plugins/charts/jquery.flot.stack','plugins/charts/jquery.flot.pie','plugins/charts/jquery.flot.resize','plugins/sparklines/jquery.sparkline.min','plugins/fullcalendar/fullcalendar.min','plugins/select2/select2.min','plugins/uniform/uniform','plugins/maskedinput/jquery.maskedinput-1.3.min','plugins/validation/languages/jquery.validationEngine-en','plugins/validation/jquery.validationEngine','plugins/mcustomscrollbar/jquery.mCustomScrollbar.min','plugins/animatedprogressbar/animated_progressbar','plugins/qtip/jquery.qtip-1.0.0-rc3.min','plugins/cleditor/jquery.cleditor','plugins/dataTables/jquery.dataTables.min',/*'plugins/dataTables/jquery.dataTables.columnFilter',*/'plugins/fancybox/jquery.fancybox.pack'));

echo $this->Html->script(array('cookies','actions','charts','plugins'/*, 'sliding.form'*/));
?>
<?php echo $this->App->js(); ?>
<?php echo $this->fetch('css'); ?>
<?php echo $this->fetch('script'); ?>

</head>

<body>
<div class="header"> <?php echo $this->Html->image('logo.png', array('alt'=>'ARK', 'title'=>'ARK - Admin Panel')); ?>
<?php //echo $this->element('user/header'); ?> </div>
<div class="menu"> 
<div class="breadLine">
  <div class="arrow"></div>
  <div class="adminControl active"> Hi, <?php //echo $this->Session->read('User.name'); ?></div>
</div>

<div class="admin" style="display:block;">
  <div class="image"> 
	
  <?php 	
  //if($this->Session->read('User.photo')==''){ ?>
<a class="fancybox" href="<?php echo $this->base?>/img/logo.png">
 <?php 	echo $this->Html->image('logo.png',array('class'=>'img-polaroid'));  ?></a>
<?php  /*}
  else{ ?>
<a class="fancybox" href="<?php echo $this->base?>/img/users/original/<?php echo $this->Session->read('User.photo')?>">
<?php echo $this->Html->image('/img/users/small/'.$this->Session->read('User.photo'),array('class'=>'img-polaroid', 'alt'=>'profile-image')); ?></a>
<?php  }*/
  ?></div>
  <ul class="control">
  <?php /*
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
	*/
  ?>
    <?php /*?><li><span class="icon-time"></span><a href="<?php echo $this->base?>/entries/entry/<?php echo $id?>">
		<?php if($check_on_off == 0){echo 'Timer On';}elseif($check_on_off != 0){echo "Timer Off";}?></a></li><?php */?>
  </ul>
<!--<ul class="navigation">
    <li class="<?php //echo $marray[4];?>"> <a href="<?php  echo  $this->base?>/users/dashboard"> <span class="isw-grid"></span><span class="text">DashBoard</span> </a> </li>
    </ul>-->

<div class="dr"><span></span></div>

    </div>

</div>
<div class="content">
<?php 
//echo $this->element('admin/bread_line');
echo $this->Session->flash();
echo $this->fetch('content');
?>
</div>
</body>
</html>
