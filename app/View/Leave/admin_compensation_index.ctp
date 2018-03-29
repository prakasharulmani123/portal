

<div class="workplace">
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-target"></div>
                <h1>Compensation Leave Requests</h1>
                <div class="clear"></div>
            </div>
            <div class="block-fluid table-sorting">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="leave-index">
                    <thead>
                        <tr>
                            <th width="6%">No</th>
                            <th width="15%">Name</th>
                            <th width="15%">Requisition</th>
                            <th width="10%">Date</th>
                            <th width="10%">Days</th>
                            <th width="10%">Compensation Days</th>
                            <th width="10%">Reason</th>
                            <th width="10%">Status</th>
                            <th width="20%">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($compensations as $compensation):

                            $user = $this->requestAction('users/get_user', array('pass' => array('User.id' => $compensation['Compensation']['user_id'])));
                            ?>

                            <tr>
                                <td><?php echo h($i); ?></td>
                                <td><?php echo h($user['User']['employee_name']); ?></td>
                               
                               
                               
                                
                               	  <td><span id="remarks_<?php echo $leave['Leave']['id'] ?>"><?php echo h($leave['Leave']['remarks']) ?></span></td>

                            </tr>
                            <?php
                            $i++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="dr"><span></span></div>
</div>