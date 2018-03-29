<?php echo $this->Html->css(array('bootstrap-editable.css'), 'stylesheet', array('inline' => false)); ?>
<?php echo $this->Html->script(array('bootstrap-editable.js'), array('inline' => false)); ?>
<?php if (!$this->request->is('ajax')) {
    ?>
<script type="text/javascript">
$(document).ready(function(){
	$("#entryreport-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": true,
		"bPaginate": true});
});
</script>
    <div class="workplace">
        <div class="row-fluid">
            <div class="span12">
                <div class="head">
                    <div class="isw-time"></div>
                    <h1>Employees Entry / Exit Timings</h1>
                    <div class="clear"></div>
                </div>
            <?php } ?>
            <div class="block-fluid table-sorting" id="content">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="entryreport-index">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="30%">Name</th>
                            <th width="10%">Entry Time</th>
                            <th width="10%">Exit Time</th>
                        </tr>
                    </thead>
                   
                    <tbody>
                        <?php
			 foreach ($entries as $entry):
		         $user = $this->requestAction('users/get_user',array('pass'=>array('User.id'=>$entry['Entry']['user_id'])));
                         ?>
                         <?php
                        if (empty($reports)) {
                        if ($entry) {
                            $time_in_hour = $time_out_hour = date('H', strtotime($entry['Entry']['time_in']));
                            if ($time_in_hour > 12) {
                                $time_in_hour = $time_out_hour = ($time_in_hour - 12);
                            }

                            $time_in_minute = $time_out_minute = date('i', strtotime($entry['Entry']['time_in']));

                            $minute_part_one = substr(date('i', strtotime($entry['Entry']['time_in'])), 0, 1);
                            $minute_part_two = substr(date('i', strtotime($entry['Entry']['time_in'])), 1, 2);

                            if ($minute_part_two < 5) {
                                if ($minute_part_two < 3) {
                                    $minute_part_two = 0;
                                } else {
                                    $minute_part_two = 5;
                                }
                            } elseif ($minute_part_two > 5) {
                                if ($minute_part_one == '5') {
                                    if ($minute_part_two < 7) {
                                        $minute_part_two = 5;
                                    } else {
                                        if ($time_in_hour == '12') {
                                            $time_in_hour = $time_out_hour = 1;

                                            $minute_part_one = 0;
                                            $minute_part_two = 0;
                                        } else {
                                            $time_in_hour = $time_in_hour + 1;
                                            $time_out_hour = $time_out_hour + 1;

                                            $minute_part_one = 0;
                                            $minute_part_two = 0;
                                        }
                                    }
                                } else {
                                    if ($minute_part_two < 7) {
                                        $minute_part_two = 5;
                                    } else {
                                        $minute_part_two = 0;
                                        $minute_part_one = $minute_part_one + 1;
                                    }
                                }
                            }

                            $time_in_minute = $time_out_minute = $minute_part_one . $minute_part_two;
                            $time_in_merdian = $time_out_merdian = strtolower(date('A', strtotime($entry['Entry']['time_in'])));
                        }
                    } else {
                        $end = end($reports);

                        $time_in_hour = $time_out_hour = date('h', strtotime($end['TempReport']['end_time']));
                        $time_in_minute = $time_out_minute = date('i', strtotime($end['TempReport']['end_time']));
                        $time_in_merdian = $time_out_merdian = date('a', strtotime($end['TempReport']['end_time']));
                    }
               
                ?>
                        <?php
                            if (($entry['Entry']['time_in'] == '0000-00-00 00:00:00') || ($entry['Entry']['time_in'] == NULL)) {
                                $time_in = '--';
                            } else {
                                $time_in = date('g:i A', strtotime($entry['Entry']['time_in']));
                            }
                            ?>
                        <?php
                       
                        $timings = json_decode($user['User']['timings'], true);
                        $start_date = date('Y-m-d').' '.$time_in_hour. ':'. $time_in_minute.':00';
                        $time_ob_1 = new DateTime($start_date);
                        $tot_h = date('H', strtotime($timings['total_hours_in_office']));
                        $tot_m = date('i', strtotime($timings['total_hours_in_office']));
                        $time_ob_1->add(new DateInterval('PT' . $tot_h . 'H' . $tot_m . 'M'));
                        $end_time = $time_ob_1->format('h:i A');
                        

                        if(in_array($entry['Entry']['user_id'], $late_entry)) {
                                                
                            $end_time = date('h:i A', strtotime($timings['late_entry_end_time']));
                        }
                        if(in_array($entry['Entry']['user_id'],$permission_exists))
                        {
                           
                            $end_time = date('h:i A', strtotime($timings['office_end_time']));
                        }
                        ?>
                     <tr id="<?php echo $entry['Entry']['id']?>">
                                <td><?php echo $entry['Entry']['user_id']  ?></td>
                                <td><?php echo $user['User']['employee_name']?></td>
                                <td><?php echo $time_in;  ?> </td>
                                <td> 
                                    <?php 
                                    if (empty(in_array($entry['Entry']['user_id'],$leave))) {
                            $check_permission = $this->requestAction('entries/check_permission_saturday');
                            if($check_permission){
                                $end_time = date('h:i A', strtotime($end_time.' -2 hours'));
                            }
                            echo $end_time;
                            
                            }else {
                                   echo  '00:00:00';
                               } ?>
                               </td>
                            </tr>
                            
                            <?php
                          
                        endforeach;
                        ?>

                    </tbody>
                </table>
               
<?php echo $this->Js->writeBuffer(); ?> <!– This is mandatory –>
                <div class="clear"></div>
            </div>
<?php if (!$this->request->is('ajax')) { ?>
            </div>
        </div>
        <div class="dr"><span></span></div>
    </div>
<?php } ?>