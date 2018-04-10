<script type="text/javascript">
    $(document).ready(function () {
        $(".employee_table").dataTable({
            "iDisplayLength": 10,
            "sPaginationType": "full_numbers",
            "bLengthChange": true,
            "bFilter": true,
            "bInfo": false,
            "bPaginate": true,
            "bStateSave" :true});
    });

</script>

<div class="workplace">
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-users"></div>
                <h1><?php echo $status == 1 ? 'Active' : 'In-active' ?> Employees</h1>
                <?php if ($status == 1) { ?>
                    <ul class="buttons">
                        <li><a class="check-access" href="<?php echo $this->base ?>/admin/users/add" title="Add Employee"><span class="isw-plus"></span></a></li>
                    </ul>
                <?php } ?>
                <div class="clear"></div>
            </div>
            <div class="block-fluid table-sorting">
                <table cellpadding="0" cellspacing="0" width="100%" class="table employee_table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="19%">Name</th>
                            <th width="9%">ID No</th>
                            <th width="21%">Designation</th>
                            <th width="14%">Join Date</th>
                            <th width="7%">Active</th>
                            <th width="7%">Actions</th>
                            <th width="6%">Super User</th>
                            <th width="26%">Employee's login</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($users as $user):
                            if($user['User']['employee_type'] == 'P'):
                            ?>
                            <tr>
                                <td><?php echo h($i); ?></td>
                                <td><?php echo h($user['User']['employee_name']); ?></td>
                                <td><?php echo h($user['User']['employee_id']); ?></td>
                                <td><?php echo h($user['User']['designation']); ?></td>
                                <td><?php echo h(date("d-m-Y", strtotime($user['User']['joined_on']))); ?></td>
                                <td><span style="display:none"><?php echo $user['User']['active'] ?></span><?php echo $this->Html->link($this->Html->image('icon_' . $user['User']['active'] . '.png'), array('controller' => 'users', 'action' => 'switch', 'active', $user['User']['id']), array('class' => 'status', 'escape' => false)); ?></td>
                                <td>
                                    <a class="check-access" href="<?php echo $this->base ?>/admin/users/edit/<?php echo $user['User']['id'] ?>" title="Edit Employee"><span class="icon-pencil"></span></a> 
                                    <a class="check-access" href="<?php echo $this->base ?>/admin/users/view/<?php echo $user['User']['id'] ?>" title="View Employee"><span class="isb-text_document"></span></a> 
                                    <?php
                                    $hide_module = 'hidden';
                                    
                                    if($this->Session->read('User.role')== 'admin') {
                                        if($user['User']['super_user']==1  && $user['User']['active']==1){
                                            $hide_module = '';
                                        }
                                    ?>
                                    <a href="<?php echo $this->base ?>/admin/users/access/<?php echo $user['User']['id'] ?>" title="Access Module" class="access_module <?php echo $hide_module?>"><span class="icon-user"></span></a>
                                    <?php }?>
                                </td>
                                <td><span style="display:none"><?php echo $user['User']['super_user'] ?></span><?php echo $this->Html->link($this->Html->image('icon_' . $user['User']['super_user'] . '.png', array()), array('controller' => 'users', 'action' => 'switch', 'super_user', $user['User']['id']), array('class' => 'status', 'escape' => false, 'data-superuser' => true)); ?></td>
                               <td align="center">
                        <?php
                        $user_id = $user['User']['id'];
                        echo $this->Html->link('Login as User', '/users/employeelogin/'.$user_id, array('class' => 'btn btn-info btn-sm')); 
                          ?>
                         </td>
 
                            </tr>


    <?php $i++;
    endif;
endforeach; ?>
                    </tbody>
                </table>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="dr"><span></span></div>
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-users"></div>
                <h1><?php echo $status == 1 ? 'Active' : 'In-active' ?> Trainees</h1>
                <?php if ($status == 1) { ?>
                    <ul class="buttons">
                        <li><a class="check-access" href="<?php echo $this->base ?>/admin/users/add?employee_type=T" title="Add Employee"><span class="isw-plus"></span></a></li>
                    </ul>
                <?php } ?>
                <div class="clear"></div>
            </div>
            <div class="block-fluid table-sorting">
                <table cellpadding="0" cellspacing="0" width="100%" class="table employee_table" id="">
                    <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="19%">Name</th>
                        <th width="9%">ID No</th>
                        <th width="21%">Designation</th>
                        <th width="14%">Join Date</th>
                        <th width="7%">Active</th>
                        <th width="7%">Actions</th>
                        <th width="6%">Super User</th>
                        <th width="26%">Employee's login</th>   
                    </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1;
                    foreach ($users as $user):
                        if($user['User']['employee_type'] == 'T'):
                        ?>
                        <tr>
                            <td><?php echo h($i); ?></td>
                            <td><?php echo h($user['User']['employee_name']); ?></td>
                            <td><?php echo h($user['User']['trainee_id']); ?></td>
                            <td><?php echo h($user['User']['designation']); ?></td>
                            <td><?php echo h(date("d-m-Y", strtotime($user['User']['joined_on']))); ?></td>
                            <td><span style="display:none"><?php echo $user['User']['active'] ?></span><?php echo $this->Html->link($this->Html->image('icon_' . $user['User']['active'] . '.png'), array('controller' => 'users', 'action' => 'switch', 'active', $user['User']['id']), array('class' => 'status', 'escape' => false)); ?></td>
                            <td>
                                <a class="check-access" href="<?php echo $this->base ?>/admin/users/edit/<?php echo $user['User']['id']; ?>" title="Edit Employee"><span class="icon-pencil"></span></a>
                                <a class="check-access" href="<?php echo $this->base ?>/admin/users/view/<?php echo $user['User']['id'] ?>" title="View Employee"><span class="isb-text_document"></span></a>
                                <?php
                                $hide_module = 'hidden';

                                if($this->Session->read('User.role')== 'admin') {
                                    if($user['User']['super_user']==1  && $user['User']['active']==1){
                                        $hide_module = '';
                                    }
                                    ?>
                                    <a href="<?php echo $this->base ?>/admin/users/access/<?php echo $user['User']['id'] ?>" title="Access Module" class="access_module <?php echo $hide_module?>"><span class="icon-user"></span></a>
                                <?php }?>
                            </td>
                            <td><span style="display:none"><?php echo $user['User']['super_user'] ?></span><?php echo $this->Html->link($this->Html->image('icon_' . $user['User']['super_user'] . '.png', array()), array('controller' => 'users', 'action' => 'switch', 'super_user', $user['User']['id']), array('class' => 'status', 'escape' => false, 'data-superuser' => true)); ?></td>
                             <td align="center">
                        <?php
                        $user_id = $user['User']['id'];
                        echo $this->Html->link('Login as User', '/users/employeelogin/'.$user_id, array('class' => 'btn btn-info btn-sm')); 
                         ?>

                         </td>
                        </tr>
                        <?php $i++;
                        endif;
                    endforeach; ?>
                    </tbody>
                </table>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>