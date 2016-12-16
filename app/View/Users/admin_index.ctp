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
    <div class="row-fluid">
        <div class="span12">
<!---------------------- Buttons --------------------------------->
            <div class="widgetButtons">                        
                <div class="bb"><a href="<?php echo $this->base?>/admin/users/employee/1" title="Empoyee"><span class="ibw-users"></span></a></div>
                <div class="bb"><a href="<?php echo $this->base?>/admin/dailystatus" title="Daily Status Report"><span class="ibw-text_document"></span></a></div>
                <div class="bb"><a href="<?php echo $this->base?>/admin/emails" title="Email"><span class="ibw-mail"></span></a>
                </div>
                <div class="bb red">
                	<a href="<?php echo $this->base?>/admin/leave" title="Leave Requests"><span class="ibw-target"></span></a>
                    <?php $leave_count = $this->requestAction('admin/leave/get_leave_requests_count'); ?>
                    <?php if($leave_count > 0){ ?>
	                    <div class="caption red">
						<?php echo $leave_count; ?>
                    </div>
                    <?php }?>
                </div>
                <div class="bb"><a href="<?php echo $this->base?>/admin/users/profile" title="Profile"><span class="ibw-settings"></span></a></div>
                <div class="bb yellow">
                	<a href="<?php echo $this->base?>/admin/permission" title="Permission Requests"><span class="ibw-list"></span></a>
                    <?php $per_count = $this->requestAction('admin/permission/get_permission_requests_count'); ?>
                    <?php if($per_count > 0){ ?>
	                    <div class="caption red">
						<?php echo $per_count; ?>
                    </div>
                    <?php }?>
                </div>
                
                <?php $pending_reports = $this->requestAction('pending_reports/check_all_user_pending_reports_active');?>
				
                <?php if(!empty($pending_reports)):?>
                <div class="bb blue">
                    <a href="<?php echo $this->base?>/admin/pending_reports" title="Pending Report"><span class="ibw-plus"></span></a>
                    <?php $per_count = count($this->requestAction('pending_reports/check_all_user_pending_reports_by_status/0')); ?>
                    <?php if($per_count > 0){ ?>
                        <div class="caption red">
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
<div class="row-fluid">
    <div class="span4">
        <div class="head">
            <div class="isw-edit"></div>
            <h1>Latest Report</h1>
            <div class="clear"></div>
        </div>
        <div class="block news scrollBox">
            
            <div class="scroll" style="height: 308px;">
                
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
						$project .= $report['DailyStatus']['projectname'].',';
					}
				}
				?>
                    <b><?php echo $emp['User']['employee_name']?></b>
					<p><?php echo '<b>'.'Projects'.'</b>'.' : '. rtrim($project,',') ?></p>
					<p><?php echo 'Worked Hours : '. '<b style="color:#53759A;">'.gmdate("H:i", ($worked_hours* 60)).'</b>' ?></p>
                    <span class="date"><?php echo date('d-m-Y g:i A', strtotime($report['DailyStatus']['modified']))?></span>
                    <div class="controls">                                    
                        <a href="<?php echo $this->base?>/admin/dailystatus/view/<?php echo $report['DailyStatus']['id']?>" class="icon-list-alt" title="View Report"></a>
                    </div>
                </div>
                <?php }?>
            </div>
        </div>
    </div>                               

<!---------------------- Time in / out --------------------------------->
    <div class="span4">
        <div class="head">
            <div class="isw-time"></div>
            <h1>Time in / out <?php echo ' ( '.date('d-m-Y'). ' )'?></h1>
            <div class="clear"></div>
        </div>
        <div class="block users scrollBox">
            
            <div class="scroll" style="height: 308px;">
            
            <?php  foreach($entries as $entry) {
				$user = $this->requestAction('users/get_user/'.$entry['Entry']['user_id']);
				?>
                <div class="item">
                    <div class="image">
                    <a href="#"><?php echo $user['User']['photo'] == '' ? $this->Html->image('logo.png',array('class'=>'img-polaroid', 'width'=>'25', 'height'=>'30')) : $this->Html->image('/img/users/small/'.$user['User']['photo'], array('width'=>'35', 'height'=>'30'))?></a></div>
                    <div class="info">
                        <a href="<?php echo $this->base?>/admin/users/view/<?php echo $user['User']['id']?>" class="name" title="View Employee"><?php echo $user['User']['employee_name']?></a>
                    </div>
                    <span class="time_light">&nbsp; &nbsp; Time In :</span>
                    <span class="time_blue">
						<?php
							echo date('g:i A', strtotime($entry['Entry']['time_in'])); 
						?>
                    </span>
                    <span class="time_light">&nbsp; &nbsp; IP :</span>
                    <span class="time_blue">
						<?php
							echo $entry['Entry']['time_in_ip'].'<br>'; 
						?>
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

<!---------------------- Email --------------------------------->
	<div class="span4">
	  <div class="row-fluid">
        <div class="head">
            <div class="isw-mail"></div>
            <h1>E-mail</h1>
            <ul class="buttons">                            
                <li>
                    <a href="#" class="isw-settings"></a>
                    <ul class="dd-list">
                        <li><a href="<?php echo $this->base?>/admin/emails/add"><span class="isw-plus"></span> Add E-Mail</a></li>
                        <li><a href="<?php echo $this->base?>/admin/emails"><span class="isw-documents"></span> View</a></li>
                    </ul>
                </li>
            </ul>                         
            <div class="clear"></div>
        </div>
        <div class="block-fluid accordion">
            <h3>All Emails</h3>
            <div>
            <div style="height:200px;">
                <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
                    <thead>
                        <tr>
                            <th width="60">Name</th><th width="60">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
						foreach($emails as $email) { ?> 
                        <tr>
                            <td><b><?php echo $email['Email']['name']?></b></td>
                            <td><?php echo $email['Email']['email']?></td>
                        </tr>
                            <?php }?>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
                </div>
            </div>                        
            <h3>To Emails</h3>
            <div>
            <div style="height:180px;">
                <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
                    <thead>
                        <tr>
                            <th width="60">Name</th><th width="60">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($emails as $email) {
							if($email['Email']['to_cc']=='to'){?>
                        <tr>
                            <td><b><?php echo $email['Email']['name']?></b></td>
                            <td><?php echo $email['Email']['email']?></td>
                        </tr>
                            <?php }}?>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
            </div>
            <h3>CC Emails</h3>
            <div>
            <div style="height:180px;">
                <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
                    <thead>
                        <tr>
                            <th width="60">Name</th><th width="60">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($emails as $email) {
							if($email['Email']['to_cc']=='cc'){?>
                        <tr>
                            <td><b><?php echo $email['Email']['name']?></b></td>
                            <td><?php echo $email['Email']['email']?></td>
                        </tr>
                            <?php }}?>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>                        
            </div>
            <h3>Bcc Emails</h3>
            <div>
            <div style="height:180px;">
                <table cellpadding="0" cellspacing="0" width="100%" class="sOrders">
                    <thead>
                        <tr>
                            <th width="60">Name</th><th width="60">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($emails as $email) {
							if($email['Email']['to_cc']=='bcc'){?>
                        <tr>
                            <td><b><?php echo $email['Email']['name']?></b></td>
                            <td><?php echo $email['Email']['email']?></td>
                        </tr>
                            <?php }}?>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
            </div>
           </div> 
        </div>
    </div>
</div>

<div class="dr"><span></span></div> 

<!---------------------- Birthday --------------------------------->

<div class="row-fluid">
    <div class="span12">                    
        <div class="head">
            <div class="isw-birthday"></div>
            <h1>Upcoming Birthday</h1>      
            <div class="clear"></div>
        </div>
        <div class="block-fluid">
            <table cellpadding="0" cellspacing="0" width="100%" class="table">
                <thead>
                    <tr>                                    
                        <th width="50%">Name</th>
                        <th width="25%">Date of Birth</th>
                        <th width="25%">Age</th>                                    
                    </tr>
                </thead>
                <tbody>
                <?php foreach($birthdays as $birthday){
					$birthDate = date('d/m/Y', strtotime($birthday['User']['date_of_birth']));
					$birthDate = explode("/", $birthDate);
					
					$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
				?>
                    <tr>                                    
                        <td><?php echo $birthday['User']['employee_name']?></td>
                        <td><?php echo $birthday['User']['date_of_birth']?></td>
                        <td><?php echo $age;?></td>                                    
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>                                
</div>

<div class="dr"><span></span></div> 
</div>
