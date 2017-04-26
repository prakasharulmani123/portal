<script>
    $(function () {
        $("#DailyStatusFromDate").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
        $("#DailyStatusToDate").datepicker({
            dateFormat: 'dd-mm-yy',
            altFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    });

    $(document).ready(function () {

//	$("#dailyreport-index").dataTable({
//		"iDisplayLength": 10, 
//		"sPaginationType": "full_numbers",
//		"bLengthChange": true,
//		"bFilter": true,
//		"bInfo": true,
//		"bPaginate": true
//	});

    });
</script>

<?php if (!$this->request->is('ajax')) { ?>
    <div style="margin:10px 50px 10px 50px;">
        <?php echo $this->Form->create('DailyStatus'); ?>
        <?php
        $all_user = array();

        foreach ($users as $user) {
            $all_user[$user['User']['id']] = $user['User']['employee_name'];
        }

        $from_date = $all['from_date'];
        $to_date = $all['to_date'];

        if (empty($from_date)) {
            $from_date = "";
        } else {
            $from_date = date('d-m-Y', strtotime($from_date));
        }

        if (empty($to_date)) {
            $to_date = "";
        } else {
            $to_date = date('d-m-Y', strtotime($to_date));
        }
        ?>
        <b><?php echo " Employee : " ?></b>
        <?php echo $this->Form->input('user_id', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array($all_user), 'selected' => $all['user_id'], 'style' => 'width:200px; margin-top:6px;')); ?>
        <b><?php echo " From : " ?></b>
        <?php echo $this->Form->input('from_date', array('label' => false, 'div' => false, 'class' => 'form-control', 'value' => $from_date, 'style' => 'width:100px; margin-top:6px;')); ?>
        <b><?php echo " To : " ?></b>
        <?php echo $this->Form->input('to_date', array('label' => false, 'type' => 'text', 'div' => false, 'class' => 'form-control', 'value' => $to_date, 'style' => 'width:100px; margin-top:6px;')); ?>
        <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?></td>
    <?php echo $this->Html->link('Reset', array('controller' => 'dailystatus', 'action' => 'reset', 'admin' => true), array('class' => 'btn btn-danger')); ?>
    <?php echo $this->Form->end(); ?>
    <div class="clear"></div>
    </div>
<?php } ?>

<?php if (!$this->request->is('ajax')) { ?>
    <div class="workplace">
        <div class="row-fluid">
            <div class="span12">
                <div class="head">
                    <div class="isw-grid"></div>
                    <h1>Daily Reports</h1>
                    <div class="clear"></div>
                </div>
            <?php } ?>
            <div class="block-fluid table-sorting" id="content">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="dailyreport-index">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Name</th>
                            <th width="20%">Date</th>
                            <th width="40%">Project Name</th>
                            <th width="10%">Worked Hours</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($dailyreports as $dailyreport):
                            $ctgy = $this->requestAction('Categories/get_category_by_id', array('pass' => array('Category.id' => $dailyreport['DailyStatus']['category_id'])));
                            $work = $this->requestAction('Works/get_work_by_id', array('pass' => array('Work.id' => $dailyreport['DailyStatus']['work_id'])));

                            $reports = $this->requestAction('daily_status/get_reports_by_id_and_date/' . $dailyreport['DailyStatus']['user_id'] . '/' . $dailyreport['DailyStatus']['date']);

                            $worked_hours = 0;
                            $projects = "";
                            $hours = 0;
                            $break_hours = 0;

                            foreach ($reports as $report) {
                                $start_time = strtotime($report['DailyStatus']['start_time']);
                                $end_time = strtotime($report['DailyStatus']['end_time']);

                                $datetime1 = new DateTime($report['DailyStatus']['start_time']);
                                $datetime2 = new DateTime($report['DailyStatus']['end_time']);
                                $interval = $datetime1->diff($datetime2);
                                $elapsed = $interval->format('%h hour %i minute');
                                $hours += ($interval->format('%h') * 60) + ($interval->format('%i'));

                                if ($report['DailyStatus']['projectname']) {
                                    $projects .= $report['DailyStatus']['projectname'] . ',';
                                }

                                if ($report['DailyStatus']['category_id'] != 23 && $report['DailyStatus']['category_id'] != 22 && $report['DailyStatus']['category_id'] != 24) {
                                    $worked_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
                                } elseif ($report['DailyStatus']['category_id'] != 24) {
                                    $break_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
                                }

                                if ($report['DailyStatus']['projectname']) {
                                    $projects .= $report['DailyStatus']['projectname'] . ' , ';
                                }
                            }
                            ?>
                            <tr>
                                <td><?php echo h($i); ?></td>
                                <?php $employee = $this->requestAction('users/get_user', array('pass' => array($dailyreport['DailyStatus']['user_id']))); ?>
                                <td><?php echo $employee['User']['employee_name']; ?></td>
                                <td><?php echo date('d-m-Y', strtotime($dailyreport['DailyStatus']['date'])); ?></td>
                                <td><?php echo rtrim($projects, ' , '); ?></td>
                  <!--              <td><?php //echo gmdate("H:i", ($hours* 60)); ?></td>
                                <td><?php //echo gmdate("H:i", ($break_hours* 60)); ?></td>
                                -->              <td><b><?php echo gmdate("H:i", ($worked_hours * 60)); ?></b></td>
                                <td><a class="check-access" href="<?php echo $this->base ?>/admin/dailystatus/view/<?php echo $dailyreport['DailyStatus']['id'] ?>" title="View Report"><span class="isb-text_document"></span></a> 
                                </td>
                            </tr>
                            <?php $i++;
                        endforeach; ?>
                    </tbody>
                </table>
                <?php
                $this->paginator->options(array('update' => '#content'));
//        $paginator = $this->Paginator;
                $summary = $this->Paginator->counter('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}');
                echo "<div class='dataTables_info' id='leave-index_info'>{$summary}</div>";
                echo "<div class='dataTables_paginate paging_full_numbers'>";
                echo $this->paginator->first("<< First", array('class' => 'paginate_button', 'tag' => false));
                if ($this->paginator->hasPrev()) {
                    echo $this->paginator->prev("Prev", array('class' => 'paginate_button', 'tag' => false));
                }
                echo $this->paginator->numbers(array('modulus' => 2, 'class' => 'paginate_button', 'tag' => false));
                if ($this->paginator->hasNext()) {
                    echo $this->paginator->next("Next", array('class' => 'paginate_button', 'tag' => false));
                }
                echo $this->paginator->last(" Last >>", array('class' => 'paginate_button', 'tag' => false));
                echo "</div>";
                ?>
                <?php echo $this->Js->writeBuffer(); ?> <!– This is mandotary –>
                <div class="clear"></div>
            </div>
            <?php if (!$this->request->is('ajax')) { ?>
            </div>
        </div>
        <div class="dr"><span></span></div>
    </div>
<?php } ?>