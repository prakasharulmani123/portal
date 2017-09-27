<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Pending Report Summary</title>
</head>

<body bgcolor="#8d8e90">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#8d8e90">
  <tr>
    <td><table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="61"><a href= "<?php $this->base;?>/admin/users" target="">
                </a></td>
                <td width="144"><a href= "http://arkinfotec.com/" target="_blank">
                <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/app/webroot/img/logo.png" height="76" width="144" alt="ARK Infotec"/>
                <td width="393"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="46" align="right" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="4%">&nbsp;</td>
                          </tr>
                        </table></td>
                    </tr>
                    <tr>
                      <td height="30">
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
                <td width="10%">&nbsp;</td>
                <td width="80%" align="left" valign="top"><font style="font-family: Georgia, 'Times New Roman', Times, serif; color:#010101; font-size:24px"><strong><em>Dear Admin,</em></strong></font><br /><br />
                  <font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
                  You have below pending approvals. Please make approvals as soon as possile.<br />
                  Login : <a href="<?php echo $this->html->url('/', true).'admin'?>" target="_blank"><?php echo $this->html->url('/', true).'admin'?></a>
                  
				<?php if(!empty($pending_leaves)){ ?>
                <p>You have <span style="color:#F00;"><?php echo count($pending_leaves)?> Pending leave </span>approval</p>
                <p>Leave Sent by :</p>
                <p>
                <?php $i =1; foreach($pending_leaves as $pending_leave):
					$user = $this->requestAction('users/get_user/'.$pending_leave['Leave']['user_id']);
					echo $i++.' . '.$user['User']['employee_name'].' (Date: '.date('d-m-Y', strtotime($pending_leave['Leave']['date'])).' , Days: '.floatval($pending_leave['Leave']['days']).')';               
					echo '<br>';
                endforeach ?>
                </p>
                <hr />
                <?php } ?>

				<?php if(!empty($pending_permissions)){ ?>
                <p>You have <span style="color:#F00;"><?php echo count($pending_permissions)?> Pending Permission </span>approval</p>
                <p>Permission Sent by :</p>
                <p>
                <?php $i =1; foreach($pending_permissions as $pending_permission): 
					$user = $this->requestAction('users/get_user/'.$pending_permission['Permission']['user_id']);
					echo $i++.' . '.$user['User']['employee_name'].' (Date: '.date('d-m-Y', strtotime($pending_permission['Permission']['date'])).' , From: '.date('h:i a', strtotime($pending_permission['Permission']['from_time'])).' To: '.date('h:i a', strtotime($pending_permission['Permission']['to_time'])).')';               
					echo '<br>';
                endforeach ?>
                </p>
                <hr />
                <?php } ?>

				<?php if(!empty($pending_reports)){ ?>
                <p>You have <span style="color:#F00;"><?php echo count($pending_reports)?> Pending Report </span>approval</p>
                <p>Report Sent by :</p>
                <p>
                <?php 
				$i =1; 
				foreach($pending_report_users as $pending_report_user): //pr($pending_report);
					$user = $this->requestAction('users/get_user/'.$pending_report_user['PendingReport']['user_id']);
					echo $i++.' . '.$user['User']['employee_name'].' (Date : ';
					
					$date = '';
					foreach($pending_reports as $pending_report):
						if($pending_report['PendingReport']['user_id'] == $pending_report_user['PendingReport']['user_id']){
							$date .= $pending_report['PendingReport']['date'].', ';
						}
					endforeach;
					echo rtrim($date,', ').')<br>';
                endforeach ?>
                </p>
                <?php } ?>

-- <br />
Regards,<br />
ARK Infotec || <a href= "http://arkinfotec.com/" target="_blank">www.arkinfotec.com</a><br />
</font></td>
                <td width="10%">&nbsp;</td>
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
