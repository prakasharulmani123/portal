<?php echo $this->Html->css(array('jquery.tree.min.css')); ?>

<!-- include jQuery and jQueryUI libraries for tree -->
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css"/>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<?php echo $this->Html->script(array('minified/jquery.tree.min.js')); ?>

<!-- initialize checkboxTree plugin -->
<script type="text/javascript">
    //<!--
    $(document).ready(function () {
        $('#tree').tree({
            /* specify here your options */
        });
    });
    //-->
</script>
<div class="workplace">
    <?php echo $this->Form->create('User', array('id' => 'validation', 'type' => 'file')); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-users"></div>
                <h1>Access Details</h1>
                <div class="clear"></div>
            </div>
            <div class="block-fluid">

                <div class="row-form">
                    <div class="span2" style="font-size:17px;">Module:</div>
                    <div class="span8">
                        <div id="tree" style="font-size:11px;">
                            <?php
                            App::import('Model', 'User');
                            $this->User = new User();
                            $useraccess = $this->User->find('first', array('conditions' => array('User.id' => $id)));
                            $accessid = $useraccess['User']['access'];
                            foreach ($roles as $role) :
                                $array = json_decode($findaccesses);
//                                print_r($array);print_r($accessid);exit;
                                if (is_array($array) && in_array($role['Module']['id'], $array) && $findaccesses == $accessid) {
                                    $checked = 'checked';
                                } else {
                                    $checked = '';
                                }
                                ?>
                                <ul>
                                    <li>
                                        <?php
                                        echo $this->Form->input($role['Module']['module_name'], array('type' => 'checkbox', 'name' => "modules[]", 'value' => $role['Module']['id'], $checked));
                                        foreach ($childs as $child) :
                                            if (is_array($array) && in_array($child['Module']['id'], $array) && $findaccesses == $accessid) {
                                                $checked = 'checked';
                                            } else {
                                                $checked = '';
                                            }
                                            if ($role['Module']['id'] == $child['Module']['parent_id']) {
                                                ?>
                                                <ul>
                                                    <li>
                                                        <?php echo $this->Form->input($child['Module']['module_name'], array('type' => 'checkbox', 'name' => "modules[]", 'value' => $child['Module']['id'], $checked)); ?>

                                                    </li>
                                                </ul>   
                                            <?php } ?>
                                            <?php
                                        endforeach;
                                        ?> 
                                    </li>
                                </ul>
                                <?php
                            endforeach;
//                            }
                            ?>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">                
        <div align="center" class="span12">
            <input type="submit" name="save" id="save" value="Add" class="btn" />
            <input type="button" name="back" id="back" value="Back" class="btn" onclick="location.href = '<?php echo $this->base; ?>/admin/users/employee/1'" />          
        </div>
    </div>
    <?php echo $this->Form->end(); ?>
</div>