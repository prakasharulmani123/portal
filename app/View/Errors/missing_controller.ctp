
<?php 
$this->layout = "login";

echo "<h4><b>"."Error"."</b></h4>";
echo "This controller was not found"."</br>";

if($this->Session->read('User.role')=='admin'){
	echo "Click to go to index page ". " ".$this->Html->link('Index', array('controller'=>'users', 'action'=>'index', 'admin'=>true));
}
elseif($this->Session->read('User.role')!='admin'){
	echo "Click to go to index page ". " ".$this->Html->link('Index', array('controller'=>'users', 'action'=>'dashboard'));
}
?>