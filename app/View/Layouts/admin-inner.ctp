<!DOCTYPE html>
<html lang="en">
<head>
<title><?php echo $title_for_layout; ?></title>

<?php 
//echo $this->Html->image('favicon.ico');
//echo $this->Html->meta('icon');

echo $this->Html->meta('favicon.ico', 'http://www.arkinfotec.com/wp-content/themes/ark/images/favicon.ico', array (
    'type' => 'icon' 
));
echo $this->Html->css(array('stylesheets'));
echo $this->Html->css(array('fullcalendar.print'),'stylesheet',array('media' => 'print'));
?>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'></script>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js'></script>

<?php 

echo $this->Html->script(array('plugins/jquery/jquery.mousewheel.min','plugins/cookie/jquery.cookies.2.2.0.min','plugins/bootstrap.min','plugins/charts/excanvas.min','plugins/charts/jquery.flot','plugins/charts/jquery.flot.stack','plugins/charts/jquery.flot.pie','plugins/charts/jquery.flot.resize','plugins/sparklines/jquery.sparkline.min','plugins/fullcalendar/fullcalendar.min','plugins/select2/select2.min','plugins/uniform/uniform','plugins/maskedinput/jquery.maskedinput-1.3.min','plugins/validation/languages/jquery.validationEngine-en','plugins/validation/jquery.validationEngine','plugins/mcustomscrollbar/jquery.mCustomScrollbar.min','plugins/animatedprogressbar/animated_progressbar','plugins/qtip/jquery.qtip-1.0.0-rc3.min','plugins/cleditor/jquery.cleditor','plugins/dataTables/jquery.dataTables.min',/*'plugins/dataTables/jquery.dataTables.columnFilter',*/'plugins/fancybox/jquery.fancybox.pack'));

echo $this->Html->script(array('cookies','actions','charts','plugins'/*, 'sliding.form'*/));

?>
<?php echo $this->App->js(); ?>
<?php echo $this->fetch('css'); ?>
<?php echo $this->fetch('script'); ?>
<script>
var BaseURL = "<?php echo $this->base?>";

$(document).ready(function() {

	$("a.status").unbind("change");
	$("a.status").click(function(){
		var p = this.firstChild;
		if (p.src.match('icon_1.png')) {
			$(p).attr({ src: Shop.basePath + "img/icon_0.png", alt: "Activate" });
		} else {
			$(p).attr("src", Shop.basePath + "img/icon_1.png");
			$(p).attr("alt","Deactivate");
		};
		$.get(this.href);
		return false;
	});

});

</script>
</head>

<body>
<div class="header"> <?php echo $this->element('admin/header'); ?> </div>
<div class="menu"> <?php echo $this->element('admin/left_sidebar'); ?> </div>
<div class="content">
<?php 
//echo $this->element('admin/bread_line');
echo $this->Session->flash();
echo $this->fetch('content');
?>
</div>
</body>
</html>
