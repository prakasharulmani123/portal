<?php echo $this->Html->css(array('bootstrap-editable.css'), 'stylesheet', array('inline' => false)); ?>
<?php echo $this->Html->script(array('bootstrap-editable.js'), array('inline' => false)); ?>

<script>
    $(function () {
        $("#UserAttendanceFromDate").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
        $("#UserAttendanceToDate").datepicker({
            dateFormat: 'dd-mm-yy',
            altFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });
    });

    $(document).ready(function () {

//        $("#entryreport-index").dataTable({
//            "iDisplayLength": 5,
//            "sPaginationType": "full_numbers",
//            "bLengthChange": true,
//            "bFilter": true,
//            "bInfo": true,
//            "bPaginate": true
//        });

    });
</script>

<?php if (!$this->request->is('ajax')) { ?>
<div style="margin:10px 50px 10px 50px;">
<?php echo $this->Form->create('UserAttendance'); ?>
<?php
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
        <b><?php echo " From : " ?></b>
<?php echo $this->Form->input('from_date', array('label' => false, 'div' => false, 'class' => 'form-control', 'value' => $from_date, 'style' => 'width:100px; margin-top:6px;')); ?>
        <b><?php echo " To : " ?></b>
<?php echo $this->Form->input('to_date', array('label' => false, 'type' => 'text', 'div' => false, 'class' => 'form-control', 'value' => $to_date, 'style' => 'width:100px; margin-top:6px;')); ?>
<?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?></td>
<?php echo $this->Html->link('Reset', array('controller' => 'user_attendances', 'action' => 'reset', 'admin' => false), array('class' => 'btn btn-danger')); ?>
<?php echo $this->Form->end(); ?>
  <div class="clear"></div>
</div>
<?php } ?>

<?php if (!$this->request->is('ajax')) { ?>
<div class="workplace">
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-time"></div>
                <h1>Attendance</h1>
                <div class="clear"></div>
            </div>
<?php } ?>
            <div class="block-fluid table-sorting" id="content">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="entryreport-index">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Date</th>
                            <th width="10%">From</th>
                            <th width="10%">To</th>
                            <th width="10%">Total In</th>
                            <th width="10%">Total Out</th>
                            <th width="10%">Total Hours</th>
                            <th width="55%">Attendance Logs</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $page = $this->params['paging']['UserAttendance']['page'];
                        $limit = $this->params['paging']['UserAttendance']['limit'];
                        $counter = ($page * $limit) - $limit + 1;
                        foreach ($attendances as $attendance):
                            ?>
                            <tr>
                                <td><?php echo h($counter); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($attendance['UserAttendance']['date'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($attendance['UserAttendance']['start_time'])); ?></td>
                                <td><?php echo date('h:i A', strtotime($attendance['UserAttendance']['end_time'])); ?></td>
                                <td><?php echo date('H:i', strtotime($attendance['UserAttendance']['in_elapsed'])); ?></td>
                                <td><?php echo date('H:i', strtotime($attendance['UserAttendance']['out_elapsed'])); ?></td>
                                <td><?php echo date('H:i', strtotime($attendance['UserAttendance']['total_elapsed'])); ?></td>
                                <td>
                                    <?php $logs = json_decode($attendance['UserAttendance']['log']); ?>
                                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="entryreport-index">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="10%">In/Out</th>
                                                <th width="10%">From</th>
                                                <th width="10%">To</th>
                                                <th width="10%">Elapsed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $g = 1;
                                            foreach ($logs as $log):
                                                ?>
                                                <tr>
                                                    <td><?php echo $g; ?></td>
                                                    <td><?php echo $log->d == 'i' ? 'In' : 'Out'; ?></td>
                                                    <td><?php echo date('h:i A', strtotime($log->t1)); ?></td>
                                                    <td><?php echo $log->t2 ? date('h:i A', strtotime($log->t2)) : '-'; ?></td>
                                                    <td><?php echo $log->e ? date('H:i', strtotime($log->e)) : '-'; ?></td>
                                                </tr>
                                                <?php $g++;
                                            endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <?php $counter++;
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
        if($this->paginator->hasPrev()){
            echo $this->paginator->prev("Prev", array('class' => 'paginate_button', 'tag' => false));
        }
        echo $this->paginator->numbers(array('modulus' => 2, 'class' => 'paginate_button','tag' => false));
        if($this->paginator->hasNext()){
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