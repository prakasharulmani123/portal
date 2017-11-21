<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Daily Status Report</title>
</head>

<body bgcolor="#8d8e90">

<img src="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/app/webroot/img/logo.png" height="76" width="144" alt="Sumanas Tech"/>
<img src="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/portal/app/webroot/img/images/PROMO-GREEN2_01_04.jpg" height="30" width="393" alt=""/>
<!--<img src="http://demo.arkinfotec.in/app/webroot/img/logo.png" height="76" width="144" alt="Sumanas Tech"/>
<img src="http://demo.arkinfotec.in/portal/app/webroot/img/images/PROMO-GREEN2_01_04.jpg" height="30" width="393" alt="-"/>-->

<?php 
echo 'WWW ROOT'.WWW_ROOT.'<br>';
echo 'this base'.$this->base.'<br>';
echo 'web root'.$this->webroot.'<br>';
?>
<?php echo $this->Html->image('images/PROMO-GREEN2_01_04.jpg', array('alt'=>'', 'height'=>30, 'width'=>393))?>
<?php //echo $this->Html->image('logo.png', array('alt'=>'Sumanas Tech', 'height'=>76, 'width'=>144))?>
</body>
<?php //exit;?>
