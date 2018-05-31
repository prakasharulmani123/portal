<script type="text/javascript">
$(document).ready(function(){
	$( "#LateEntryDate" ).datepicker({
		dateFormat: 'dd-mm-yy',
		changeMonth: true,
		changeYear: true,
		}
	);

	$("#validation").submit(function(){
            if($('#userlist').val() == null){
                    alert('Please Select Atlease one Employee');
                    return false;
            }
	});

$('#userlist').multiSelect({

  selectableHeader: "<input type='text' class='search-input' autocomplete='on' placeholder='Employee Name'>",
  selectionHeader: "<input type='text' class='search-input' autocomplete='on' placeholder='Employee Name'>",
  afterInit: function(ms){
    var that = this,
        $selectableSearch = that.$selectableUl.prev(),
        $selectionSearch = that.$selectionUl.prev(),
        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
    .on('keydown', function(e){
      if (e.which === 40){
        that.$selectableUl.focus();
        return false;
      }
    });

    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
    .on('keydown', function(e){
      if (e.which == 40){
        that.$selectionUl.focus();
        return false;
      }
    });
  },
  afterSelect: function(){
    this.qs1.cache();
    this.qs2.cache();

  },
  afterDeselect: function(){
    this.qs1.cache();
    this.qs2.cache();
  }
});


});
</script>

<div class="workplace">
<?php echo $this->Form->create('LateEntry', array('id' => 'validation')); ?>
  <div class="row-fluid">
    <div class="span12">
      <div class="head">
        <div class="isw-archive"></div>
        <h1>Add Late Entries </h1>
        <div class="clear"></div>
      </div>
      <div class="block-fluid">
        <div class="row-form">
          <div class="span3">Employee:</div>
          <div class="span9">
             <?php /*?><?php echo $this->Form->input('category_id', array('empty' => true,'label'=>false)); ?><?php */?>
                 <div class="row-fluid">
                       <div class="span6">
                         <table cellpadding="0" cellspacing="0" width="100%" class="table">
                            <tbody>
                            <tr>
                            <td><select id='userlist' multiple='multiple' name="data[LateEntry][user][]">
                            <?php $users = $this->requestAction('users/get_all_users'); $i = 0;?>
                            <?php foreach($users as $user):?>
                              <option value='<?php  echo $user['User']['id']?>' name='data[LateEntry][user][<?php echo $i?>]'><?php  echo $user['User']['employee_name']?></option>
                            <?php $i++; endforeach;?>
                            </select></td>
                            </tr>
                         </tbody></table>            
           </div>
          </div>
</div>
        <div class="clear"></div>
        </div>
		<div class="row-form">
          <div class="span3">Start Date*:</div>
          <div class="span2">
            <?php echo $this->Form->input('date', array('class' => 'validate[required]', 'type'=>'text', 'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
        
		<?php
        $hours = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12');
        $minutes = array('00'=>'00', '05'=>'05', '10'=>'10', '15'=>'15', '20'=>'20', '25'=>'25', '30'=>'30', '35'=>'35', '40'=>'40', '45'=>'45', '50'=>'50', '55'=>'55');
        $mer = array('am'=>'am','pm'=>'pm');
        $approved = array('approved'=>'Approved');
        ?>

		<div class="row-form">
          <div class="span3">Entry Time*:</div>
            <?php echo $this->Form->input('start_time', array('class' => 'validate[required]','type'=>'hidden')); ?>
          <div class="span1">
            <?php echo $this->Form->input('start_hours', array('class' => 'validate[required]','type'=>'select', 'options'=>$hours, 'label'=>false, 'empty'=>'hours', 'name'=>'data[LateEntry][start][hours]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('start_minutes', array('class' => 'validate[required]','type'=>'select', 'options'=>$minutes, 'label'=>false, 'empty'=>'minutes', 'name'=>'data[LateEntry][start][minutes]')); ?>
          </div>
          <div class="span1">
            <?php echo $this->Form->input('start_mer', array('class' => 'validate[required]','type'=>'select', 'options'=>$mer, 'label'=>false, 'empty'=>'meridian', 'name'=>'data[LateEntry][start][meridian]')); ?>
          </div>
          <div class="clear"></div>
        </div>
        
		<div class="row-form">
          <div class="span3">Status :</div>
          <div class="span2">
            <?php echo $this->Form->input('approved', array('class' => 'validate[required]','type'=>'select', 'label'=>false,'options'=>$approved ,'label'=>false)); ?>
          </div>
          <div class="clear"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="dr"><span></span></div>
  
  <div class="row-fluid">                
    <div align="center" class="span12">
      <input type="submit" name="save" id="save" value="Add" class="btn" />
    </div>
  </div>
  <?php echo $this->Form->end(); ?>
</div>

