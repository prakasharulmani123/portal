<!DOCTYPE html>
<html lang="en">
<head>
<?php echo $this->Html->charset(); ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title><?php echo $title_for_layout; ?></title>

<?php 
echo $this->Html->meta('favicon.ico', '/app/webroot/favicon.ico', array (
    'type' => 'icon' 
));
echo $this->Html->css(array('stylesheets'));
echo $this->Html->css(array('fullcalendar.print'),'stylesheet',array('media' => 'print'));
?>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'></script>
<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js'></script>
<script>BaseURL = "<?php echo $this->base?>";</script>
<?php
echo $this->Html->script(array('plugins/jquery/jquery.mousewheel.min','plugins/cookie/jquery.cookies.2.2.0.min','plugins/bootstrap.min','plugins/charts/excanvas.min','plugins/charts/jquery.flot','plugins/charts/jquery.flot.stack','plugins/charts/jquery.flot.pie','plugins/charts/jquery.flot.resize','plugins/sparklines/jquery.sparkline.min','plugins/fullcalendar/fullcalendar.min','plugins/select2/select2.min','plugins/uniform/uniform','plugins/maskedinput/jquery.maskedinput-1.3.min','plugins/validation/languages/jquery.validationEngine-en','plugins/validation/jquery.validationEngine','plugins/mcustomscrollbar/jquery.mCustomScrollbar.min','plugins/animatedprogressbar/animated_progressbar','plugins/qtip/jquery.qtip-1.0.0-rc3.min','plugins/cleditor/jquery.cleditor','plugins/dataTables/jquery.dataTables.min',/*'plugins/dataTables/jquery.dataTables.columnFilter',*/'plugins/fancybox/jquery.fancybox.pack', 'jquery/moment'));

echo $this->Html->script(array('cookies','actions','charts','plugins', 'clipboard.min.js', 'jquery.nicescroll.js'/*, 'sliding.form'*/));
?>
<?php 
//echo $this->Html->image('favicon.ico');
//echo $this->Html->meta('icon');
echo $this->Html->meta('icon');
echo $this->fetch('meta'); 
?>
<?php echo $this->App->js(); ?>
<?php echo $this->fetch('css'); ?>
<?php echo $this->fetch('script'); ?>

</head>

<body>
<div class="header"> <?php echo $this->element('user/header'); ?> </div>
<div class="menu"> <?php echo $this->element('user/left_sidebar'); ?> </div>
<div class="content">
<?php 
//echo $this->element('admin/bread_line');
echo $this->Session->flash();
echo $this->fetch('content');
?>
<script type="text/javascript">
    $(document).ready(function () {
//        $("html").niceScroll({
//            spacebarenabled: false,
//            enablekeyboard: false,
//            emulatetouch: true,
//            cursordragontouch: true,
//            cursorcolor: '#365B85',
//        });
    });
</script>
</div>
<style type="text/css">
.back-to-top {
    cursor: pointer;
    position: fixed;
    bottom: 20px;
    right: 20px;
    display:none;
    padding: 0px 10px 0px 0px;
}
</style>
<a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button" title="Click to return on the top page" data-toggle="tooltip" data-placement="left"><span class="isw-up_circle"></span></a>
<script type="text/javascript">
$(document).ready(function(){
     $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#back-to-top').click(function () {
            $('#back-to-top').tooltip('hide');
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });

        $('#back-to-top').tooltip('show');

});
</script>
</body>
</html>
