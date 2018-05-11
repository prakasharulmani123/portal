<?php if (!empty($reports)) { ?>
    <div><h4 align="center">Your Daily Status Report</h4>
    </div>
    <?php
    $worked_hours = $total_hours = 0;

    foreach ($reports as $key => $report) {
        $datetime1 = new DateTime($report['TempReport']['start_time']);
        $datetime2 = new DateTime($report['TempReport']['end_time']);
        $interval = $datetime1->diff($datetime2);
        $total_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));

        if ($report['TempReport']['category_id'] != 23 && $report['TempReport']['category_id'] != 22 && $report['TempReport']['category_id'] != 24) {
            $worked_hours += ($interval->format('%h') * 60) + ($interval->format('%i'));
        }
    }
    $time_ob_1 = new DateTime($reports[0]['TempReport']['start_time']);
    $tot_h = date('H', strtotime($timings['total_hours_in_office']));
    $tot_m = date('i', strtotime($timings['total_hours_in_office']));
    $time_ob_1->add(new DateInterval('PT' . $tot_h . 'H' . $tot_m . 'M'));
    $end_time = $time_ob_1->format('h:i A');
    ?>
    <div style="margin-left:20px; color:#00C;"><h6 align="left">Worked Hours : <?php echo gmdate("H:i", ($worked_hours * 60)); ?></h6></div>
    <div style="margin-left:20px; color:#00C;"><h6 align="left">Total Hours : <?php echo gmdate("H:i", ($total_hours * 60)); ?></h6></div>
    <?php
    $check_permission = $this->requestAction('entries/check_permission_saturday');
    if($check_permission){
        $end_time = date('h:i A', strtotime($end_time.' -2 hours'));
    }
    ?>
    <div style="margin-right:20px; color:#00C; float: right"><h6 align="left">Your Office End Time:  : <?php echo $end_time; ?></h6></div>
    <div class="row-form">
        <table border="1" bordercolor="#52759B" style="box-shadow:5px 5px #52759B;" cellpadding="0" cellspacing="0" width="100%" class="table">
            <thead>
            <th width="6%">No.</th>
            <th width="12%">Project Name</th>
            <th width="12%">Category</th>
            <th width="14%">Work</th>
            <th width="10%">Start Time</th>
            <th width="10%">End Time</th>
            <th width="7%">Elapsed</th>
            <th width="5%">Status</th>
            <th width="15%">Comments</th>
            <th width="9%">Action</th>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($reports as $key => $report) {
                    $ctgy = $this->requestAction('Categories/get_category_by_id', array('pass' => array('Category.id' => $report['TempReport']['category_id'])));
                    $work = $this->requestAction('Works/get_work_by_id', array('pass' => array('Work.id' => $report['TempReport']['work_id'])));

                    $sts = "";
                    if ($report['TempReport']['status'] == 1) {
                        $sts = 'progress';
                    } elseif ($report['TempReport']['status'] == 2) {
                        $sts = 'completed';
                    } elseif ($report['TempReport']['status'] == 3) {
                        $sts = 'in-completed';
                    } elseif ($report['TempReport']['status'] == 4) {
                        $sts = 'cancelled';
                    }
                    ?>
                    <tr>
                        <!--edit purpose --->
                <span id="td_category_<?php echo $report['TempReport']['id'] ?>" style="display:none"><?php echo $report['TempReport']['category_id'] ?></span>
                <span style="display:none" id="td_status_<?php echo $report['TempReport']['id'] ?>"><?php echo $report['TempReport']['status'] ?></span>
                <span style="display:none" id="td_start_time_<?php echo $report['TempReport']['id'] ?>"><?php echo date('h:i:a', strtotime($report['TempReport']['start_time'])) ?></span>
                <span style="display:none" id="td_end_time_<?php echo $report['TempReport']['id'] ?>"><?php echo date('h:i:a', strtotime($report['TempReport']['end_time'])) ?></span>
        <?php if ($i == 1) { ?>
                    <span style="display:none" id="td_disabled_<?php echo $report['TempReport']['id'] ?>">disabled</span>
                <?php } else { ?>
                    <span style="display:none" id="td_disabled_<?php echo $report['TempReport']['id'] ?>"> </span>
                <?php } ?>
                <!--end-->

                <td><?php echo $i++ ?></td>

                <td id="td_projectname_<?php echo $report['TempReport']['id'] ?>"><?php echo $report['TempReport']['projectname'] ?></td>

                <td><?php echo $ctgy['Category']['category'] ?></td>

                <td id="td_work_id_<?php echo $report['TempReport']['id'] ?>"><?php echo $report['TempReport']['work_id']//echo $work['Work']['work'] ?></td>

                <td><?php echo date('g:i A', strtotime(strval(str_replace('.', ':', $report['TempReport']['start_time'])))) ?></td>

                <td><?php echo date('g:i A', strtotime(strval(str_replace('.', ':', $report['TempReport']['end_time'])))) ?></td>

                <td>
                    <?php
                    $datetime1 = new DateTime($report['TempReport']['start_time']);
                    $datetime2 = new DateTime($report['TempReport']['end_time']);
                    $interval = $datetime1->diff($datetime2);
                    $hours = ($interval->format('%h')*60)+($interval->format('%i'));
                    echo gmdate("H:i", ($hours* 60));
                    ?>
                </td>

                <td><?php echo $sts ?></td>

                <td id="td_comments_<?php echo $report['TempReport']['id'] ?>"><?php echo $report['TempReport']['comments'] ?></td>

                <td>
                    <a href="javascript:edit_row(<?php echo $report['TempReport']['id'] ?>)" title="edit"><span class="icon-edit"></span></a>
                    <a href="javascript:copy_row(<?php echo $report['TempReport']['id'] ?>)" title="copy"><span class="icon-file"></span></a>
        <?php //echo $this->Html->link('<span class="icon-edit"></span>',array('controller'=>'dailystatus', 'action'=>'edit_row', $report['TempReport']['id']), array('title'=>'Edit Row', 'escape'=>false));  ?>
        <?php
        if ($i == 2) {
            if (count($reports) == 1) {
                echo $this->Html->link('<span class="icon-remove"></span>', array('controller' => 'dailystatus', 'action' => 'delete_row', $report['TempReport']['id']), array('title' => 'Delete Row', 'escape' => false, 'onclick' => "return confirm('Are you sure to delete ?')"));
            }
        } else {
            ?>
                        <a href="javascript:delete_row(<?php echo $report['TempReport']['id'] ?>)" onclick="return confirm('Are you sure to delete ?')" title="delete row"><span class="icon-remove"></span></a>
                        <?php
                    }
                    ?>

                    <?php /*
                      if($i == 2)
                      {
                      echo  count($reports) == 1 ? $this->Html->link('<span class="icon-remove"></span>',array('controller'=>'dailystatus', 'action'=>'delete_row', $report['TempReport']['id']), array('title'=>'Delete Row', 'escape'=>false, 'onclick'=>"return confirm('Are you sure to delete ?')")) : '';
                      }
                      else{
                      echo $this->Html->link('<span class="icon-remove"></span>',array('controller'=>'dailystatus', 'action'=>'delete_row', $report['TempReport']['id']), array('title'=>'Delete Row', 'escape'=>false, 'onclick'=>"return confirm('Are you sure to delete ?')"));
                      } */
                    ?>
                </td>
                </tr>				
                <?php } ?>
            </tbody>
        </table>
    </div>

            <?php $last_row = end($reports); ?>
    <span style="display:none" id="last_row_end_time"><?php echo date('h:i:a', strtotime($last_row['TempReport']['end_time'])) ?></span>
<?php } ?>
  