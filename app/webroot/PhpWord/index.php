<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
require_once 'PhpWord/Autoloader.php';
require_once 'PhpWord/PhpWord.php';
require_once 'PhpWord/Settings.php';
require_once 'PhpWord/Template.php';
require_once 'PhpWord/DocumentProperties.php';
require_once 'PhpWord/Collection/Titles.php';
//require_once 'PhpWord/Collection/AbstractCollection.php';
//require_once 'PhpWord/Titles.php';

Autoloader::register();

error_reporting(E_ALL);
define('CLI', (PHP_SAPI == 'cli') ? true : false);
define('EOL', CLI ? PHP_EOL : '<br />');
define('SCRIPT_FILENAME', basename($_SERVER['SCRIPT_FILENAME'], '.php'));
define('IS_INDEX', SCRIPT_FILENAME == 'index');

?>

<?php

// New Word document
echo date('H:i:s') , " Create new PhpWord object" , EOL;
$phpWord = new PhpWord();

$document = $phpWord->loadTemplate('resources/Sample_07_TemplateCloneRow.docx');

// Variables on different parts of document
$document->setValue('weekday', date('l')); // On section/content
$document->setValue('time', date('H:i')); // On footer
$document->setValue('serverName', realpath(__DIR__)); // On header

$document->setValue('m_loc', 'test'); // On footer

// Simple table
/*
$document->cloneRow('rowValue', 10);

$document->setValue('rowValue#1', 'Sun er');
$document->setValue('rowValue#2', 'Mercury');
$document->setValue('rowValue#3', 'Venus');
$document->setValue('rowValue#4', 'Earth');
$document->setValue('rowValue#5', 'Mars');
$document->setValue('rowValue#6', 'Jupiter');
$document->setValue('rowValue#7', 'Saturn');
$document->setValue('rowValue#8', 'Uranus');
$document->setValue('rowValue#9', 'Neptun');
$document->setValue('rowValue#10', 'Pluto');

$document->setValue('rowNumber#1', '1');
$document->setValue('rowNumber#2', '2');
$document->setValue('rowNumber#3', '3');
$document->setValue('rowNumber#4', '4');
$document->setValue('rowNumber#5', '5');
$document->setValue('rowNumber#6', '6');
$document->setValue('rowNumber#7', '7');
$document->setValue('rowNumber#8', '8');
$document->setValue('rowNumber#9', '9');
$document->setValue('rowNumber#10', '10');
*/

// Table with a spanned cell
/*
$document->cloneRow('userId', 3);

$document->setValue('userId#1', '1');
$document->setValue('userFirstName#1', 'James');
$document->setValue('userName#1', 'Taylor');
$document->setValue('userPhone#1', '+1 428 889 773');

$document->setValue('userId#2', '2');
$document->setValue('userFirstName#2', 'Robert');
$document->setValue('userName#2', 'Bell');
$document->setValue('userPhone#2', '+1 428 889 774');

$document->setValue('userId#3', '3');
$document->setValue('userFirstName#3', 'Michael');
$document->setValue('userName#3', 'Ray');
$document->setValue('userPhone#3', '+1 428 889 775');
*/

$name = 'Sample_07_TemplateCloneRow.docx';
echo date('H:i:s'), " Write to Word2007 format", EOL;
$document->saveAs($name);
rename($name, "results/{$name}");

//echo getEndingNotes(array('Word2007' => 'docx'));
?>
</body>
</html>