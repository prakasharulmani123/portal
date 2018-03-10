<?php 
if($this->Session->read('User.role') == 'admin'){
	$this->layout = "login";
	echo "<h4><b>"."Error"."</b></h4>";
	echo "This url was not found"."</br>";
	echo "Click to go to index page ". " ".$this->Html->link('Index', array('controller'=>'users', 'action'=>'index', 'admin'=>true));
	exit;
}
?>
<a class="logo" href="<?php echo $this->base?>/dailystatus"><?php echo $this->Html->image('logo.png', array('alt'=>'Sumanas Technologies', 'title'=>'Sumanas Technologies - Admin Panel', 'width' => 200, 'height' => 50)); ?>
</a>
<ul class="header_menu">
    <li class="list_icon"><a href="#">&nbsp;</a></li>
</ul>    

<script type="text/javascript">
<?php 
$time = $this->requestAction('entries/check_time_in_out');
if($time){
	if($time['Entry']['on_off'] == 1){
		$datetime2 = new DateTime($time['Entry']['time_in']);
		
		$datetime1 = new DateTime(date('Y-m-d H:i:s'));
		$interval = $datetime1->diff($datetime2);
		
		$hours = $interval->format('%h');
//                print_r($hours);exit;
		$minutes = $interval->format('%i');
		$seconds = $interval->format('%s');
	}
}
?>
<?php if(isset($hours) && isset($minutes) && isset($seconds)){ ?> 
var hours = <?php  echo $hours?>;
var minutes = <?php  echo $minutes?>;
var seconds = <?php  echo $seconds?>;

$(function() {
    setInterval(counter, 1000);

});

function counter() {
    var hh = 0;
    var mm = 0;
    var ss = 0;

    seconds = seconds + 1;
    ss = seconds + 1;
    if (seconds == 60) {
        seconds = 0;
        minutes = minutes + 1;
        mm = minutes + 1;
        if (minutes == 60) {
            minutes = 0;
            hours = hours + 1;
            hh = hours + 1;
        }
    }

    if (seconds < 10) {
        ss = "0" + seconds;
    }else{
        ss = seconds;
    }
    if (minutes < 10) {
        mm = "0" + minutes;
    }else{
        mm = minutes;
    }
    if (hours < 10) {
        hh = "0" + hours;
    }else{
        hh = hours;
    }

    $('#displayTime').html(hh + " : " + mm + " : " + ss);

}
<?php } ?>
</script>

<div id="displayTime" style="text-align:right; font-size:16px; color:white; margin-top:5px; font-weight:bold;"></div>