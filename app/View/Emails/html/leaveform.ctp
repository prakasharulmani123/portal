<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Daily Status Report</title>
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
                <td width="80%" align="center" valign="top"><font style="font-family: Georgia, 'Times New Roman', Times, serif; color:#010101; font-size:24px"><strong><em>Leave Request</em></strong></font><br /><br />

        <table cellpadding="0" cellspacing="0" width="100%" class="table" border="0">
        <tbody>
        <tr>
            <td width="80%" align="right" valign="top"><font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
            Date :
			<?php 
			$leave_date = '';
			$leave_days = 0;
			foreach($leave['SubLeave'] as $value):
				$leave_date .= date('d-m-Y', strtotime($value['date'])).' & ';
				$leave_days += $value['day'];
			endforeach;
			
			echo rtrim($leave_date,' & ')
			?>
            <br />
            Days : <?php echo $leave_days?> 
            </td>
        </tr>
        <tr>
            <td width="80%" align="left" valign="top"><font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
            Dear Sir/Madam
    <br /><br />
            </td>
        </tr>
        <tr>
            <td width="20%" align="left"><font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px"><?php echo $leave['Leave']['reason']?></td>
        </tr>
        <tr>
            <td><font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
            <br /><br />
            Thank You.<br />
            -- <br />
            Regards,<br />
            <?php echo $user['User']['employee_name']?>,<br />
            <?php echo $user['User']['designation']?>,<br />
            ARK Infotec || <a href= "http://arkinfotec.com/" target="_blank">www.arkinfotec.com</a><br />
            Email:<a href= "mailto:<?php echo $user['User']['email']?>" target="_blank"><?php echo $user['User']['email']?></a><br />
            Skype:<a href= "<?php echo $user['User']['skype']?>" target="_blank"><?php echo $user['User']['skype']?></a><br />
            </td>
        </tr>
        </tbody>
        
    </table>

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
      </table></td>
  </tr>
</table>
</body>
</html>
