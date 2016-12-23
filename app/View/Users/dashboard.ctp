<script>
$(document).ready(function() {
	<?php if(isset($late_entry)){?>
		var message = '<?php echo $late_entry?>';
		document.getElementById('late_entry_details').innerHTML = message;
		$("#popup_3").trigger("click");
	<?php } ?>
});
</script>

<style>
.time_light {
    color: #999999;
    font-size: 11px;
}

.time_blue {
    color: #4E7096;
    font-size: 11px;
}
</style>
<div class="workplace">

    <div class="dialog b_popup_3"  id="b_popup_3" style="display: none;" title="Notification">
        <p align="center" id="late_entry_details"></p>                
    </div>
	<a id="popup_3" style="display:none;">pop up</a>
    
    <div class="row-fluid">
        <div class="span12">
        <?php //echo $this->request->clientIp();?>
<!---------------------- Buttons --------------------------------->
            <div class="widgetButtons">                        
                <div class="bb"><a href="<?php echo $this->base?>/dailystatus" title="Daily Status Report"><span class="ibw-empty_document"></span></a></div>
                <div class="bb"><a href="<?php echo $this->base?>/dailystatus/reports" title="My Report"><span class="ibw-text_document"></span></a></div>
                <div class="bb">
                	<a href="<?php echo $this->base?>/leave" title="Leave Requests"><span class="ibw-target"></span></a>
                    <?php $leave_count = $this->requestAction('leave/user_get_leave_requests_count'); ?>
                    <?php if($leave_count > 0){ ?>
	                    <div class="caption red">
						<?php echo $leave_count; ?>
                    </div>
                    <?php }?>
                </div>
                <div class="bb"><a href="<?php echo $this->base?>/users/profile" title="Profile"><span class="ibw-settings"></span></a></div>
                <div class="bb"><a href="<?php echo $this->base?>/entries" title="Time In / Time Out"><span class="ibw-time"></span></a></div>
                <div class="bb">
                	<a href="<?php echo $this->base?>/permission" title="Permission Requests"><span class="ibw-list"></span></a>
                    <?php $per_count = $this->requestAction('permission/user_get_permission_requests_count'); ?>
                    <?php if($per_count > 0){ ?>
	                    <div class="caption red">
						<?php echo $per_count; ?>
                    </div>
                    <?php }?>
                </div>
                
                <div class="bb">
                	<a href="<?php echo $this->base?>/user_complaints/theirs" title="Their Complaints"><span class="ibw-left"></span></a>
                    <?php $per_count = $this->requestAction('user_complaints/user_get_their_complaint_count'); ?>
                    <?php if($per_count > 0){ ?>
	                    <div class="caption red">
						<?php echo $per_count; ?>
                    </div>
                    <?php }?>
                </div>
                
                <?php $pending_reports = $this->requestAction('pending_reports/check_user_pending_reports_active/'.$this->Session->read('User.id'));?>
				
                <?php if(!empty($pending_reports)):?>
                <div class="bb red">
                    <a href="<?php echo $this->base?>/pending_reports" title="Pending Report"><span class="ibw-plus"></span></a>
                    <?php $per_count = count($this->requestAction('pending_reports/check_user_pending_reports_approved/'.$this->Session->read('User.id'))); ?>
                    <?php if($per_count > 0){ ?>
                        <div class="caption green">
                        <?php echo $per_count; ?>
                    </div>
                    <?php }?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="dr"><span></span></div> 

<!---------------------- Latest Report --------------------------------->
	<?php $company_rules = $this->requestAction('static_pages/get_static_page_by_id/1');?>
	<?php if(!empty($company_rules)){?> 
    <div class="row-fluid">
        <div class="span12">
            <div class="head">
                <div class="isw-brush"></div>
                <h1>Company Rules</h1>
                <div class="clear"></div>
            </div>
            <div class="block">
            <?php echo $company_rules['StaticPage']['description']?>
            </div>
        </div>
    </div>

    <div class="dr"><span></span></div> 
    <?php } ?>
    
<!---------------------- Latest Report --------------------------------->
<div class="row-fluid">
    <div class="span6">
        <div class="head">
            <div class="isw-edit"></div>
            <h1>Latest Reports</h1>
            <div class="clear"></div>
        </div>
        <div class="block news scrollBox">
            <div class="scroll" style="height: 190px;">
                <?php foreach($reports as $report){?>
                <div class="item">
                <?php 
				$emp = $this->requestAction('users/get_user',array('pass'=>array('DailyStatus.id'=>$report['DailyStatus']['user_id']))); 
                $ctgy = $this->requestAction('Categories/get_category_by_id', array('pass'=>array('Category.id'=>$report['DailyStatus']['category_id']))) ;
                $work = $this->requestAction('Works/get_work_by_id', array('pass'=>array('Work.id'=>$report['DailyStatus']['work_id']))) ;
				$daily_report = $this->requestAction('dailystatus/get_reports_by_id_and_date/'.$report['DailyStatus']['user_id'].'/'.$report['DailyStatus']['date']); 
				
				$worked_hours = 0;
				$project = "";
			
				foreach($daily_report as $key => $report){
					$datetime1 = new DateTime($report['DailyStatus']['start_time']);
					$datetime2 = new DateTime($report['DailyStatus']['end_time']);
					$interval = $datetime1->diff($datetime2);
					
					if($report['DailyStatus']['category_id'] != 23 && $report['DailyStatus']['category_id'] != 22){
						$worked_hours += ($interval->format('%h')*60)+($interval->format('%i'));
						$project .= $report['DailyStatus']['projectname'].' , ';
					}
				}
				?>
					<p><?php echo '<b>'.'Projects'.'</b>'.' : '. rtrim($project,' , ') ?></p>
					<p><?php echo 'Worked Hours : '. '<b style="color:#53759A;">'.gmdate("H:i", ($worked_hours* 60)).'</b>' ?></p>
<!--                    <p><?php //echo $work['Work']['work']?></p>
-->                    <span class="date"><?php echo date('d-m-Y g:i A', strtotime($report['DailyStatus']['modified']))?></span>
                    <div class="controls">                                    
                        <a href="<?php echo $this->base?>/dailystatus/reports_view/<?php echo $report['DailyStatus']['id']?>" class="icon-list-alt" title="View Report"></a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <!---------------------- Time in / out --------------------------------->
    <div class="span6">
        <div class="head">
            <div class="isw-time"></div>
            <h1>Latest Time in / out</h1>
            <div class="clear"></div>
        </div>
        <div class="block users scrollBox">
            
            <div class="scroll" style="height: 190px;">
            
            <?php  foreach($entries as $entry) {
				$user = $this->requestAction('users/get_user/'.$entry['Entry']['user_id']);
				?>
                <div class="item">
                    <div class="date"><b><?php echo date('d-m-Y', strtotime($entry['Entry']['date']))?></b></div>
                    <span class="time_light">&nbsp; &nbsp; Time In :</span>
                    <span class="time_blue">
						<?php
							echo date('g:i A', strtotime($entry['Entry']['time_in'])); 
						?>
                    </span>
                    <span class="time_light">&nbsp; &nbsp; IP :</span>
                    <span class="time_blue">
						<?php
							echo $entry['Entry']['time_in_ip'] ?> &nbsp; &nbsp; 
                    </span>
                    <span class="time_light">&nbsp;Time Out :</span>
                    <span class="time_blue">
						<?php
							if($entry['Entry']['time_out'] == "0000-00-00 00:00:00"){ 
								echo "--".'&nbsp';
								for($i=0;$i<=8;$i++){
									echo '&nbsp;';
								}
							}
							else{
								echo date('g:i A', strtotime($entry['Entry']['time_out'])); 
							}
						?>
                    </span>
                    <span class="time_light">&nbsp; &nbsp; IP :</span>
                    <span class="time_blue">
						<?php
							if(($entry['Entry']['time_out_ip'] == NULL) || ($entry['Entry']['time_out_ip'] == '')){ 
								echo "--";
							}
							else{
								echo $entry['Entry']['time_out_ip']; 
							}
						?>
                    </span>
                    <div class="clear"></div>
                </div>
                <?php  } ?>
            </div>
        </div>
    </div>                
                               
</div>

<div class="dr"><span></span></div> 

<div class="row-fluid">
<!---------------------- Leave Request --------------------------------->
    <div class="span4">
        <div class="head">
            <div class="isw-target"></div>
            <h1>Leave Requests ( <?php echo date('F')?> )</h1>
            <div class="clear"></div>
        </div>
        <div class="block news scrollBox">
            
            <div class="scroll" style="height: 219px;">
 	            <?php 
				if($leaves){?>
                <?php foreach($leaves as $leave){?>
                    <div class="item">
                    <p><b><?php echo date('d-m-Y', strtotime($leave['Leave']['date']))?></b></p>
                    <p><?php echo 'Reason : '.$leave['Leave']['reason']?></p>
                       <p>
                      <?php
					  echo 'Status : ';
                      if($leave['Leave']['approved'] == 0){?>
                        <span class="label label-important">Pending</span>
                      <?php }
                      elseif($leave['Leave']['approved'] == 1){?>
                        <span class="label label-success">Approved</span>
                      <?php }
                      if($leave['Leave']['approved'] == 2){?>
                        <span class="label label-inverse">Declined</span>
                      <?php }
                      ?>
                      </p>
                      <?php if(!empty($leave['Leave']['remarks'])) {?>
                    <p><?php echo 'Remarks : '. $leave['Leave']['remarks']?></p>
                    <?php } ?>
                    </div>
                    <?php 
						}
                    } 
				else{ ?>
                    <div class="item">
                    <p align="center">No Leave</p>
                    </div>
				<?php }
				?>
			</div>
        </div>
    </div>                               

<!---------------------- Permission Request --------------------------------->
    <div class="span4">
        <div class="head">
            <div class="isw-list"></div>
            <h1>Permission Requests ( <?php echo date('F')?> )</h1>
            <div class="clear"></div>
        </div>
        <div class="block news scrollBox">
            
            <div class="scroll" style="height: 219px;">
 	            <?php 
				if($permissions){?>
                <?php foreach($permissions as $permission){?>
                    <div class="item">
                    <p><b><?php echo date('d-m-Y', strtotime($permission['Permission']['date']))?></b></p>
                    <p><?php echo 'Reason : '. $permission['Permission']['reason']?></p>
                       <p>
                      <?php
					  echo 'Status : ';
                      if($permission['Permission']['approved'] == 0){?>
                        <span class="label label-important">Pending</span>
                      <?php }
                      elseif($permission['Permission']['approved'] == 1){?>
                        <span class="label label-success">Approved</span>
                      <?php }
                      if($permission['Permission']['approved'] == 2){?>
                        <span class="label label-inverse">Declined</span>
                      <?php }
                      ?>
                      </p>
                      <?php if(!empty($permission['Permission']['remarks'])) {?>
                    <p><?php echo 'Remarks : '. $permission['Permission']['remarks']?></p>
                    <?php } ?>
                    </div>
                    <?php 
						}
                    } 
				else{ ?>
                    <div class="item">
                    <p align="center">No Permission</p>
                    </div>
				<?php }
				?>
			</div>
        </div>
    </div>                               

<!---------------------- Holiday --------------------------------->
    <div class="span4">
        <div class="head">
            <div class="isw-bookmark"></div>
            <h1>Official Holidays ( <?php echo date('F')?> )</h1>
            <div class="clear"></div>
        </div>
        <div class="block news scrollBox">
            
            <div class="scroll" style="height: 219px;">
 	            <?php 
				if($holidays){?>
                <?php foreach($holidays as $holiday){?>
                    <div class="item">
                    <p><b><?php echo date('d-m-Y', strtotime($holiday['Holiday']['date']))?></b> : <?php echo $holiday['Holiday']['name']?></p>
                    </div>
                    <?php 
						}
                    } 
				else{ ?>
                    <div class="item">
                    <p align="center">No Holidays</p>
                    </div>
				<?php }
				?>
			</div>
        </div>
    </div>                               

</div>
    
<div class="dr"><span></span></div> 
</div>