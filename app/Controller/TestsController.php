<?php
class TestsController extends AppController {
	public $name='Tests';
	
	public function index(){
		echo 'in';
		exit;
	}
	
	public function image(){
		$im = imagegrabscreen();
		imagepng($im, "tests/myscreenshot.png");
		imagedestroy($im);
	}
}
