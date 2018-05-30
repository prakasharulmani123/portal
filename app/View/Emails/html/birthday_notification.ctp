<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Birthday Notification</title>
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
                <img src="<?php echo 'http://'.$_SERVER['HTTP_HOST']?>/app/webroot/img/logo.png" height="76" width="144" alt="Sumanas Technologies"/>
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
                <td width="80%" align="left" valign="top"><font style="font-family: Georgia, 'Times New Roman', Times, serif; color:#010101; font-size:24px"><strong><em>Birthday Reminder</em></strong></font><br /><br />
                <font style="font-family: Verdana, Geneva, sans-serif; color:#666766; font-size:13px; line-height:21px">
                <?php
				$pos = 0;
				
				$pos = strpos($birthday_user,",");
				$age = substr($birthday_user,($pos+1),3);
				
//				if($user['User']['sex'] == 'M'){
//					$ref = 'Mr.';
//				}
//				elseif($user['User']['sex'] == 'F'){
//					if($user['User']['spousename'] == ''){
//						$ref = 'Miss.';
//					}
//					else{
//						$ref = 'Mrs.';
//					}
//				}
				?>
                <span style="text-align:left"><?php echo $user['User']['employee_name']?> is <?php echo $age?> from tomorrow (<?php echo date('d-m-Y', strtotime('+1 days'))?>)<br />Let we ready to celebrate the birthday !!!!!</span>
                <br /><br />
                
                -- <br />
                Regards,<br />
                admin@arkinfotec.com, <br />
                Sumanas Technologies || <a href= "http://arkinfotec.com/" target="_blank">www.arkinfotec.com</a><br />
                </font>
                </td>
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
