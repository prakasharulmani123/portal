<script type="text/javascript">
    $(document).ready(function () {
        $("#user_complaint-index").dataTable({
            "iDisplayLength": 10,
            "sPaginationType": "full_numbers",
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bPaginate": true});
    });
</script>

<div class="workplace">
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-left"></div>
                <h1>Their Complaints</h1>
                <div class="clear"></div>
            </div>
            <div class="block-fluid table-sorting">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="user_complaint-index">
                    <thead>
                        <tr>
                            <th width="6%">No</th>
                            <th width="10%">Date</th>
                            <!--<th width="20%">Person Name</th>-->
                            <th width="25%">Complaint</th>
                            <th width="5%">Fine Amount</th>
                            <th width="5%">Status</th>
                            <!--<th width="12%">Actions</th>-->
                            <th width="15%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($user_complaints as $user_complaint): ?>
                            <tr>
                                <td><?php echo h($i); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user_complaint['UserComplaint']['created'])) ?></td>
                                <!--<td><?php echo h($user_complaint['Sender']['employee_name']); ?></td>-->
                                <td><?php 
                                echo h($user_complaint['UserComplaint']['reason']);
                                if($user_complaint['UserComplaint']['file']){
                                    echo '<br />';                                    
                                    echo $this->Html->link('(file attached)', Router::url('/'.$user_complaint['UserComplaint']['file'], true), array('title' => 'View File', 'escape' => false, 'target' => '_blank')).' &nbsp;';
                                }
                                ?>
                                </td>
                                <td><?php echo h($user_complaint['UserComplaint']['fine_amount']); ?></td>
                                <td>
                                    <p>
                                    <?php
                                    if($user_complaint['UserComplaint']['approved'] == 0){?>
                                          <span class="label label-important">Pending</span>
                                    <?php }
                                    elseif($user_complaint['UserComplaint']['approved'] == 1){?>
                                          <span class="label label-success">Approved</span>
                                    <?php }
                                    if($user_complaint['UserComplaint']['approved'] == 2){?>
                                          <span class="label label-inverse">Declined</span>
                                    <?php }
                                    ?></p>
                                </td>
<!--                                <td>
                                    <?php
                                    if($user_complaint['UserComplaint']['approved'] == 0){
                                        echo $this->Html->link('<span class="icon-pencil"></span>', array('controller' => 'user_complaints', 'action' => 'edit', $user_complaint['UserComplaint']['id']), array('title' => 'Edit Complaint', 'escape' => false)).' &nbsp;';                                    
                                        echo $this->Html->link('<span class="icon-remove"></span>', array('controller' => 'user_complaints', 'action' => 'delete', $user_complaint['UserComplaint']['id']), array('title' => 'Delete Complaint', 'escape' => false, 'confirm' => 'Are you sure to delete ?'));                                    
                                    }
                                    ?>
                                </td>-->
                                <td><span id="remarks_<?php echo $user_complaint['UserComplaint']['id'] ?>"><?php echo h($user_complaint['UserComplaint']['remarks']) ?></span></td>
                            </tr>
                            <?php $i++;
                        endforeach; ?>
                    </tbody>
                </table>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="dr"><span></span></div>
</div>