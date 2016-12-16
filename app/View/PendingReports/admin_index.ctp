<script type="text/javascript">
$(document).ready(function(){
	$("#pending-index").dataTable({
		"iDisplayLength": 10, 
		"sPaginationType": "full_numbers",
		"bLengthChange": true,
		"bFilter": true,
		"bInfo": true,
		"bPaginate": true});
});

jQuery(document).ready(function() {
	$(".b_popup_4").dialog({
		autoOpen: false,
		modal: true,
		width: 400,
		buttons:{                            
			"Ok": function() {
				var id = $( "#pending-id" ).val();
				var status = $( "#pending-status" ).val();
				var remarks = $( "#pending-remarks" ).val();
				var user_id = $( "#user-id" ).val();
				
				$.ajax({
				  url: BaseURL+"/admin/pending_reports/add_remarks",
				  type: "POST",
				  dataType: "JSON",
				  data: {'id':id, 'status':status, 'remarks':remarks, 'user_id':user_id},
				  success: function(msg){
					 $("#button_"+id).attr("class", msg.class);
					 $("#span_"+id).html(msg.status);
					 $("#remarks_"+id).html(msg.remarks);
					  }
				});
				$( "#pending-id" ).val("");
				$( "#pending-status" ).val("");
				$( "#pending-remarks" ).val("");
				
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );
				$( "#pending-id" ).val("");
				$( "#pending-status" ).val("");
				$( "#pending-remarks" ).val("");
			}
		}
	});
/*	
	$(".dropdown-menu a").click(function(){
		
		var id = $(this).attr('pending-id');
		var status = $(this).attr('status');
		var user_id = $(this).attr('user-id');
		
		if(status == 2 || status == 1){
			$(".b_popup_4").dialog('open');
			$('#pending-id').val($(this).attr("pending-id"));
			$('#pending-status').val($(this).attr("status"));
			$('#user-id').val($(this).attr("user-id"));

			return false
		}
		
	});
*/
});

function pending_sent(sts, user_id, pending_id){
	var status = $('#a-pending-'+sts+'-'+pending_id).attr('status');
	if(status == 2 || status == 1){
		$(".b_popup_4").dialog('open');
		$('#pending-id').val(pending_id);
		$('#pending-status').val(status);
		$('#user-id').val(user_id);
	}
}

function delete_multiple()
{
	
	var count = $(":checkbox").filter(':checked').length;
	
	if(count > 0){
		var dataArr = [];
		
		$('input:checked').each(function(){
			if($(this).closest('tr[id]').attr('id') != 'header'){
				dataArr.push($(this).closest('tr[id]').attr('id')); // insert rowid's to array
			}
		});
		
		var sure = confirm ('Are you sure ?'); 
		
		if(sure){
			window.location = BaseURL+'/admin/pending_reports/multi_delete/'+dataArr;
		}
	}
	else{
		alert('Please check atleast one checkbox');
	}
}

</script>

<div class="workplace">

<div class="dialog b_popup_4" style="display: none;" title="Remarks">                                
  <div class="block"> 
	    <input type="hidden" value="" id="pending-id" name="data[PendingReport][id]"/>
	    <input type="hidden" value="" id="pending-status" name="data[PendingReport][status]"/>
	    <input type="hidden" value="" id="user-id" name="data[PendingReport][user_id]"/>
        <span>Remarks:</span>
        <p>
        <textarea placeholder="Remarks..." name="data[PendingReport][remarks]" id="pending-remarks"></textarea>
        </p>
	    <div class="dr"><span></span></div>
    </div>
</div> 


<div class="row-fluid">
    <div class="span1" style="float:right">
		<button type="button" class="btn btn-block btn-danger" onclick="javascript:delete_multiple()">Delete</button>
	</div>
</div>

  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-archive"></div>
        <h1>Pending Reports</h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid table-sorting">
        <table cellpadding="0" cellspacing="0" width="100%" class="table" id="pending-index">
          <thead>
            <tr id='header'>
	          <th width="5%"><input type="checkbox" name="checkall"/></th>
              <th width="5%">No</th>
              <th width="10%">Employee Name</th>
              <th width="10%">Date</th>
              <th width="10%">Request Time</th>
              <th width="25%">Reason</th>
              <th width="15%">Status</th>
              <th width="15%">Remarks</th>
              <th width="5%">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
			$i=1; 
			foreach ($pending_reports as $pending_report): 
				$user = $this->requestAction('users/get_user',array('pass'=>array('User.id'=>$pending_report['PendingReport']['user_id'])));
			?>
            <tr id="<?php echo $pending_report['PendingReport']['id']?>">
                <td><input id="checkbox<?php echo $pending_report['PendingReport']['id']?>" name="checkbox<?php echo $pending_report['PendingReport']['id']?>" type="checkbox" value="<?php echo $pending_report['PendingReport']['id']?>" /></td>
              <td><?php echo h($i); ?></td>
              <td><?php echo $user['User']['employee_name']?></td>
              <td><?php echo date('d-m-Y', strtotime($pending_report['PendingReport']['date'])) ?></td>
              <td><?php echo $pending_report['PendingReport']['start_time'] == '0000-00-00 00:00:00' ? '--' :date('h:i A', strtotime($pending_report['PendingReport']['start_time'])) ?></td>
              <td><?php echo $pending_report['PendingReport']['reason'] ?></td>
              <td>
              <?php if($pending_report['PendingReport']['start_time'] == '0000-00-00 00:00:00'){?>
              <p><span class="label label-important">Pending</span></p>
			  <?php
			  }
			  else{
				  $status = $pending_report['PendingReport']['status'];
				  $button = 'btn-danger';
				  $value = 'Pending';
				  if($status == 1)
				  {
					  $button = 'btn-success';
					  $value = 'Approved';
				  }
				  if($status == 2)
				  {
					  $button = 'btn-inverse';
					  $value = 'Declined';
				  }
				  ?>
              <div class="btn-group"> 
              <button data-toggle="dropdown" class="btn btn-mini <?php echo $button; ?> dropdown-toggle" id="button_<?php echo $pending_report['PendingReport']['id']?>">
              	<span id="span_<?php echo $pending_report['PendingReport']['id']?>"><?php echo $value; ?></span> <span class="caret"></span>
              </button>
                <ul class="dropdown-menu">
<!--                    <li><a href="#" user-id="<?php echo $pending_report['PendingReport']['user_id']?>" pending-id="<?php echo $pending_report['PendingReport']['id']?>" status="0">Pending</a></li>
-->                    <li><a id="a-pending-approve-<?php echo $pending_report['PendingReport']['id']?>" href="javascript:pending_sent('approve', <?php echo $pending_report['PendingReport']['user_id']?>,<?php echo $pending_report['PendingReport']['id']?>)" status="1">Approved</a></li>
                    <li><a id="a-pending-decline-<?php echo $pending_report['PendingReport']['id']?>" href="javascript:pending_sent('decline', <?php echo $pending_report['PendingReport']['user_id']?>,<?php echo $pending_report['PendingReport']['id']?>)" status="2">Declined</a></li>
                </ul>
                </div>
                <?php } ?>
              </td>
			  <td id="remarks_<?php echo $pending_report['PendingReport']['id']?>"><?php echo $pending_report['PendingReport']['remarks'] ?></td>
              <td><?php echo $this->Html->link('<span class="icon-remove"></span>',array('action'=>'delete', $pending_report['PendingReport']['id'], 'admin' => true),  array('title'=>'Delete Report', 'escape'=>false, 'confirm'=>'Are you sure to delete ?')); ?></td>
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