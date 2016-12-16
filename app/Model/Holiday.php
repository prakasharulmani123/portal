<?php
class Holiday extends AppModel {
	public $name = 'Holiday';

	public $validate = array(
		'date' => array(
			'rule'    => 'isUnique',
			'message' => 'This Date has already been taken.'
		)
	);
}
