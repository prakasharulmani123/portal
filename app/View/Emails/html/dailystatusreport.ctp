<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Daily Status Report</title>
</head>

<body bgcolor="#8d8e90">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#8d8e90">
  <tr>
    <td><table width="700" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="31"><a href= "<?php $this->base;?>/admin/users" target="">
                </a></td>
                <td width="144"><a href= "http://arkinfotec.com/" target="_blank">
                <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/portal/app/webroot/img/logo.png" height="76" width="144" alt="ARK Infotec"/>
                <td width="393"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="46" align="right" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="4%">&nbsp;</td>
                          </tr>
                        </table></td>
                    </tr>
                    <tr>
                      <td height="30" width="800">
	                <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/portal/app/webroot/img/images/PROMO-GREEN2_01_04.jpg" height="30" width="393" alt=""/>
                      </td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="5%">&nbsp;</td>
                <td width="80%" align="left" valign="top"><font style="font-family: Georgia, 'Times New Roman', Times, serif; color:#010101; font-size:24px"><strong><em>Dear Sir/Madam,</em></strong></font><br /><br />
                  <font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
                  Here is my Daily Status Report on  <?php echo date('d-m-Y')?>
<br />
<?php
			$worked_hours = 0;
			$hours = 0;
			$break_hours = 0;
			
			foreach($reports as $report){
				$datetime1 = new DateTime($report['DailyStatus']['start_time']);
				$datetime2 = new DateTime($report['DailyStatus']['end_time']);
				$interval = $datetime1->diff($datetime2);
				$hours += ($interval->format('%h')*60)+($interval->format('%i'));
				
				if($report['DailyStatus']['category_id'] != 23 && $report['DailyStatus']['category_id'] != 22  && $report['DailyStatus']['category_id'] != 24){
					$worked_hours += ($interval->format('%h')*60)+($interval->format('%i'));
				}
				elseif($report['DailyStatus']['category_id'] != 24){
					$break_hours += ($interval->format('%h')*60)+($interval->format('%i'));
				}
			}
?>
                  Worked Hours : <?php echo '<b>'.gmdate("H:i", ($worked_hours* 60)).'</b>';?>
<br />
<?php if(!empty($late_entry)){?>
Late Entry Time : <?php echo '<b>'.date('h:i A', strtotime($late_entry['LateEntry']['created'])).'</b>';?>

<br />
<?php }?>
                  Break Hours : <?php echo gmdate("H:i", ($break_hours* 60));?>
<br /><br />
        <table cellpadding="0" cellspacing="0" width="100%" class="table" border="1" style="font-size:12px">
        <thead>
            <th width="4%" align="right">No.</th>
            <th width="12%" align="center">Project Name</th>
            <th width="12%" align="center">Category</th>
            <th width="12%" align="center">Work</th>
            <th width="10%" align="center">Start Time</th>
            <th width="10%" align="center">End Time</th>
            <th width="9%" align="center">Status</th>
            <th width="20%" align="center">Comments</th>
        </thead>
        <tbody>
        <?php 
        $i=1;
		$user = $this->requestAction('users/get_user',array('pass' => array($this->Session->read('User.id')))); 
        foreach($reports as $report){
        $ctgy = $this->requestAction('Categories/get_category_by_id', array('pass'=>array('Category.id'=>$report['DailyStatus']['category_id']))) ;
        $work = $this->requestAction('Works/get_work_by_id', array('pass'=>array('Work.id'=>$report['DailyStatus']['work_id']))) ;
		$sts = "";
		if($report['DailyStatus']['status'] == 1){
			$sts = 'progress';
		}
		elseif($report['DailyStatus']['status'] == 2){
			$sts = 'completed';
		}
		elseif($report['DailyStatus']['status'] == 3){
			$sts = 'in-completed';
		}
		elseif($report['DailyStatus']['status'] == 4){
			$sts = 'cancelled';
		}
        ?>
            <tr>
                <td align="right" valign="top"><?php echo $i++?></td>
                <td align="center" valign="top"><?php echo $report['DailyStatus']['projectname']?></td>
                <td align="center" valign="top"><?php echo $ctgy['Category']['category']?></td>
                <td align="center" valign="top"><?php echo $report['DailyStatus']['work_id']//echo $work['Work']['work']?></td>
                <td align="center" valign="top"><?php echo date('g:i A', strtotime(strval(str_replace('.',':',$report['DailyStatus']['start_time'])))) ?></td>
                <td align="center" valign="top"><?php echo date('g:i A', strtotime(strval(str_replace('.',':',$report['DailyStatus']['end_time']))))?></td>
                <td align="center" valign="top"><?php echo $sts?></td>
                <td align="left" valign="top"><?php echo $report['DailyStatus']['comments']?></td>
            </tr>
            <?php }?>
        </tbody>
    </table>

<br /><br />
Thank You.<br />
-- <br />
Regards,<br />
<?php echo $user['User']['employee_name']?>,<br />
<?php echo $user['User']['designation']?>,<br />
ARK Infotec || <a href= "http://arkinfotec.com/" target="_blank">www.arkinfotec.com</a><br />
Email:<a href= "mailto:<?php echo $user['User']['email']?>" target="_blank"><?php echo $user['User']['email']?></a><br />
Skype:<a href= "<?php echo $user['User']['skype']?>" target="_blank"><?php echo $user['User']['skype']?></a><br />
</font></td>
                <td width="5%">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td align="right" valign="top"><table width="108" border="0" cellspacing="0" cellpadding="0">
                </table></td>
                <td>&nbsp;</td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
	        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
