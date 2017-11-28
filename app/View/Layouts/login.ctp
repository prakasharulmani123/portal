<?php
$cakeDescription = __d('Admin', 'Admin ');
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>|
		<?php echo $title_for_layout; ?>
	</title>
    <?php 
    echo $this->Html->meta('favicon.ico', '/app/webroot/favicon.ico', array (
    'type' => 'icon' 
));
?>
    
    <?php 
//	echo $this->Html->image('favicon.ico');
//	echo $this->Html->meta('icon');
	echo $this->Html->meta('icon', $this->Html->url('/favicon.ico'));
	
	echo $this->Html->css(array('stylesheets'));
	echo $this->Html->css(array('fullcalendar.print'),'stylesheet',array('media' => 'print'));
	?>
    
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'></script>
    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js'></script>

<?php
	echo $this->Html->script(array('plugins/jquery/jquery.mousewheel.min','plugins/cookie/jquery.cookies.2.2.0.min','plugins/bootstrap.min','plugins/charts/excanvas.min','plugins/charts/jquery.flot','plugins/charts/jquery.flot.stack','plugins/charts/jquery.flot.pie','plugins/charts/jquery.flot.resize','plugins/sparklines/jquery.sparkline.min','plugins/fullcalendar/fullcalendar.min','plugins/select2/select2.min','plugins/uniform/uniform','plugins/maskedinput/jquery.maskedinput-1.3.min','plugins/validation/languages/jquery.validationEngine-en','plugins/validation/jquery.validationEngine','plugins/mcustomscrollbar/jquery.mCustomScrollbar.min','plugins/animatedprogressbar/animated_progressbar','plugins/qtip/jquery.qtip-1.0.0-rc3.min','plugins/cleditor/jquery.cleditor','plugins/dataTables/jquery.dataTables.min',/*'plugins/dataTables/jquery.dataTables.columnFilter',*/'plugins/fancybox/jquery.fancybox.pack'));
	
	echo $this->Html->script(array('cookies','actions','charts','plugins'/*, 'sliding.form'*/));
	
	echo $this->App->js(); 
	echo $this->fetch('meta');
	echo $this->fetch('css');
	?>
</head>
<body>
    <div id="wrapper">
        <?php echo $this->fetch('content'); ?>
    </div>
</body>
</html>
