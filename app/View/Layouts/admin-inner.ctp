<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $title_for_layout; ?></title>
        <?php
        ////echo $this->Html->image('favicon.ico');
        //echo $this->Html->meta('icon');

        echo $this->Html->meta('favicon.ico', 'http://www.arkinfotec.com/wp-content/themes/ark/images/favicon.ico', array(
            'type' => 'icon'
        ));
        echo $this->Html->css(array('stylesheets'));
        echo $this->Html->css(array('fullcalendar.print'), 'stylesheet', array('media' => 'print'));
        ?>
        <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'></script>
        <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js'></script>
        <?php
        echo $this->Html->script(array('plugins/jquery/jquery.mousewheel.min', 'plugins/cookie/jquery.cookies.2.2.0.min', 'plugins/bootstrap.min', 'plugins/charts/excanvas.min', 'plugins/charts/jquery.flot', 'plugins/charts/jquery.flot.stack', 'plugins/charts/jquery.flot.pie', 'plugins/charts/jquery.flot.resize', 'plugins/sparklines/jquery.sparkline.min', 'plugins/fullcalendar/fullcalendar.min', 'plugins/select2/select2.min', 'plugins/uniform/uniform', 'plugins/maskedinput/jquery.maskedinput-1.3.min', 'plugins/validation/languages/jquery.validationEngine-en', 'plugins/validation/jquery.validationEngine', 'plugins/mcustomscrollbar/jquery.mCustomScrollbar.min', 'plugins/animatedprogressbar/animated_progressbar', 'plugins/qtip/jquery.qtip-1.0.0-rc3.min', 'plugins/cleditor/jquery.cleditor', 'plugins/dataTables/jquery.dataTables.min', /* 'plugins/dataTables/jquery.dataTables.columnFilter', */ 'plugins/fancybox/jquery.fancybox.pack'));
        echo $this->Html->script(array('cookies', 'actions', 'charts', 'plugins'/* , 'sliding.form' */));
        ?>
        <?php echo $this->App->js(); ?>
        <?php echo $this->fetch('css'); ?>
        <?php echo $this->fetch('script'); ?>
        <script>
            var BaseURL = "<?php echo $this->base ?>";
        </script>
    </head>

    <body>
        <div class="header"> <?php echo $this->element('admin/header'); ?> </div>
        <div class="menu"> <?php echo $this->element('admin/left_sidebar'); ?> </div>
        <div class="content">
            <?php
            //////////////////////////////////Hiding unnecessary links in super_user side:
            if ($this->Session->read('User.super_user') == 1 && $this->Session->read('User.role') == 'user') {
                App::import('Model', 'Module');
                $this->Module = new Module();
                $demodule = json_decode($this->Session->read('User.access'));
                $urlarraylist = [];
                foreach ($demodule as $key => $module):
                    $modules = $this->Module->find('first', array('conditions' => array('Module.id' => $module)));
                    if ($modules) {
                        $urlarray[] = $modules['Module']['url'];
                    }
                endforeach;
                $phparray = array_filter($urlarray);
                $urlarraylist = array_values($phparray);
                $encodelist = json_encode($urlarraylist, JSON_UNESCAPED_SLASHES);
            }
            ////////////////////////////////////////////////////////////////////////////
            //echo $this->element('admin/bread_line');
            echo $this->Session->flash();
            echo $this->fetch('content');
            ?>
        </div>
        <?php
        $modules = ['users', 'users/dashboard'];
        ?>
        <script>
            $(document).ready(function () {
                $("a.status").unbind("change");
                $("a.status").click(function () {
                    var p = this.firstChild;
                    if (p.src.match('icon_1.png')) {
                        $(p).attr({src: Shop.basePath + "img/icon_0.png", alt: "Activate"});

                        if ($(this).data('superuser')) {
                            $(this).closest('tr').find('.access_module').addClass('hidden');
//                            var abc = $("tr a.access_module").attr("href");
//                              var array = abc.split('/access/');
//                              var aid=array[1]
//                            console.log(aid);
                        }
                    } else {
                        $(p).attr("src", Shop.basePath + "img/icon_1.png");
                        $(p).attr("alt", "Deactivate");

                        if ($(this).data('superuser')) {
                            $(this).closest('tr').find('.access_module').removeClass('hidden');
                        }
                    }
                    ;
                    $.get(this.href);
                    return false;
                });
<?php if ($this->Session->read('User.super_user') == 1 && $this->Session->read('User.role') == 'user') { ?>
                    $('a.check-access').each(function () {
                        if ($(this).data('href')) {
                            var href = $(this).data('href');
                        } else {
                            var href = $(this).attr('href');
                        }
                        var array = href.split('/admin/');
                        var id = <?php echo $encodelist; ?>;
//                              console.log(id);
                        var list = "";
                        if (typeof (array[1]) !== "undefined" && array[1] !== null) {
                            var array_2 = array[1].split('/');
                            a2 = array_2[0].split('?');
                            var list = a2[0];
                            if (typeof (array_2[1]) !== "undefined" && array_2[1] !== null) {
                                var list = list + '/' + array_2[1];
                            }
                        }
                        console.log(list);
    //                    console.log(list);
    //                    console.log(id);
    //                    console.log($.inArray(list, id) === -1);
    //                    console.log('-------');
                        if ($.inArray(list, id) === -1) {
                            $(this).addClass('hidden');
                        }

                    });

                    $('.li_check_access').each(function () {
                        var numhideItems = $(this).find('.check-access.hidden').length;
                        var numItems = $(this).find('.check-access').length;
                        if (numItems == numhideItems)
                        {
                            $(this).hide();
                        }
                    });
<?php } ?>

            });

        </script>
    </body>
</html>
