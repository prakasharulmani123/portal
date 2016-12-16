<script type="text/javascript">

$(document).ready(function(){
	$("#LeaveFromDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true
		});
	$("#LeaveToDate").datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true
		});
		
	$("#leave-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": true,
		"bPaginate": true
	});
	
	$(".b_popup_4").dialog({
		autoOpen: false,
		modal: true,
		width: 400,
	});
	
//	$('.sub-leave-select').on("change",function() {
//		alert('in');
	   //Your code here
//	});
	
});

function toggle_leave(status, id)
{
	$.ajax({
		url: BaseURL+"/admin/leave/update_sub_leave_days_ajax/",
		type: "POST",
		dataType: "JSON",
		beforeSend: function(){
			$('#loader_'+id).show();
		},
		data: {'id':id, 'status':status},
		success: function(msg){
			$('#loader_'+id).hide();
			$('#span_'+id).html(msg.result);
			
			status == 'C' ? $('#td_span_'+id).html(msg.date+' : <b class="text-info">Casual</b>') : $('#td_span_'+id).html(msg.date+' : <b class="text-error">Paid</b>');
			setTimeout(function(){$('#span_'+id).html('')}, 2000);
		},
		error: function(err){
			alert(err);
		}
	});
}

function toggle(id)
{
	$(".b_popup_4").dialog('open');
	$('#p_user_name').html('<b>'+$('#toggle_'+id).attr('user-name')+'</b>');;
	$.ajax({
	  url: BaseURL+"/admin/leave/get_sub_leave_days_ajax/"+id,
	  dataType: "JSON",
	  success: function(msg){
		  $('#leave_days_div').html(msg);
	  },
	  error: function(){
		  $('#leave_days_div').html('Failed to load leave days. try again');
	  }
	});
}
</script>

<div class="dialog b_popup_4" style="display: none;" title="Paid / Casual Toggle">                                
  <div class="block">
        <span>Name:</span>
        <p id="p_user_name"></p>
        <p>Leave Date:</p>
        <div id="leave_days_div">
		<img title="c_loader_ge.gif" src="<?php echo $this->base?>/img/admin/loaders/c_loader_ge.gif" style="margin-left:100px;">
        </div>                                    
        <div class="dr"><span></span></div>
    </div>
</div>                                        

<div style="margin:10px 50px 10px 23px;">
<?php echo $this->Form->create('Leave'); ?>
    <?php 
		$all_user = array();
		
		foreach($users as $user){
			$all_user[$user['User']['id']] = $user['User']['employee_name'];
		}
		
		$from_date = $all['from_date'];
		$to_date = $all['to_date'];
		
		if(empty($from_date)){
			$from_date = "";
		}else{
			$from_date = date('d-m-Y', strtotime($from_date));
		}
		
		if(empty($to_date)){
			$to_date = "";
		}
		else{
			$to_date = date('d-m-Y', strtotime($to_date));
		} ?>
        <b><?php echo " Employee : "?></b>
      <?php echo $this->Form->input('user_id', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array($all_user), 'selected' => $all['user_id'], 'style'=>'width:140px; margin-top:6px;')); ?>
        <b><?php echo " From : "?></b>
      <?php echo $this->Form->input('from_date', array('label' => false, 'div' => false, 'class'=>'form-control', 'value'=> $from_date, 'style'=>'width:100px; margin-top:6px;')); ?>
        <b><?php echo " To : "?></b>
      <?php echo $this->Form->input('to_date', array('label' => false, 'type'=>'text','div' => false, 'class' => 'form-control', 'value'=> $to_date, 'style'=>'width:100px; margin-top:6px;')); ?>
        <b><?php echo " Status : "?></b>
      <?php echo $this->Form->input('approved', array('label' => false, 'div' => false, 'class' => 'form-control', 'empty' => 'All', 'options' => array('0'=>'Pending', '1'=>'Approved','2'=>'Declined'), 'selected'=>$all['approved'], 'style'=>'width:100px; margin-top:6px;')); ?>
      <?php echo $this->Form->button('Search', array('class' => 'btn btn-default')); ?>
      <?php echo $this->Html->link('Reset', array('controller' => 'leave', 'action' => 'reset_toggle', 'admin' => true), array('class' => 'btn btn-danger')); ?>
  <?php echo $this->Form->end(); ?>
  <div class="clear"></div>
</div>

<div class="workplace">
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-target"></div>
        <h1>Casual / Paid Toggle</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="leave-index">
          <thead>
            <tr>
              <th width="6%">No</th>
              <th width="10%">Name</th>
              <th width="10%">Days</th>
              <th width="20%">Reason</th>
              <th width="20%">Paid / Casual Days</th>
              <th width="15%" style="text-align:center">Toggle</th>
              <th width="15%">Remarks</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; foreach ($leaves as $leave): 
			$user = $this->requestAction('users/get_user',array('pass'=>array('User.id'=>$leave['Leave']['user_id']))); ?>
				<tr>
				  <td><?php echo h($i); ?></td>
				  <td><?php echo h($user['User']['employee_name']); ?></td>
                    <?php /*
					$leave_date = '';
					foreach($leave['SubLeave'] as $subleave){
						$leave_date .= date('d-m-Y', strtotime($subleave['date'])).' & '; 
					}
					$leave_date = rtrim($leave_date,' & ');*/
					?>
				  <?php /*?><td><?php echo h($leave_date) ?></td><?php */?>
                  <td>
			  <?php 
				switch ($leave['Leave']['days'])
				{
				case 0.5:
					echo 'Half a day';
					break;
				case 1:
					echo 'One day';
					break;
				case 1.5:
					echo 'One & Half a days';
					break;
				case 2:
					echo 'Two days';
					break;
				case 2.5:
					echo 'Two & Half a days';
					break;
				case 3:
					echo 'Three days';
					break;
				}
			  ?></td>
				  <td><?php echo h($leave['Leave']['reason'])?></td>
                  <td>
				<?php if($leave['Leave']['approved'] == 1){
					foreach($leave['SubLeave'] as $subleave){ ?>
                    	<span id="td_span_<?php echo $subleave['id']?>">
							<?php echo date('d-m-Y', strtotime($subleave['date']))?> : <?php echo $subleave['status'] == 'C' ? '<b class="text-info">Casual</b>' : '<b class="text-error">Paid</b>';?>
                        </span><br />
					<?php }
					}
					?>
                    </td>
	              <td style="text-align:center">
                  <?php if($leave['Leave']['approved'] == 0){?>
                  <span class="label label-important">Pending</span>
                  <?php }elseif($leave['Leave']['approved'] == 2){?>
                  <span class="label label-inverse">Declined</span>
                  <?php } else{ ?>
                  <a id="toggle_<?php echo $leave['Leave']['id']?>" href="javascript:toggle(<?php echo $leave['Leave']['id']?>)" user-name="<?php echo $user['User']['employee_name']?>"><i class="icon-retweet"></i></a>
                  <?php } ?>
                  </td>
				  <td><span id="remarks_<?php echo $leave['Leave']['id']?>"><?php echo h($leave['Leave']['remarks'])?></span></td>
				</tr>
            <?php $i++; endforeach; ?>
          </tbody>
        </table>
        <div class="clear"></div>
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
</div>