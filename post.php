<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['daten'])){

    //xml anlegen mit header
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="no"?><Users></Users>');

    //csv daten in array packen
    $input = array_map('str_getcsv', str_getcsv($_POST['daten'], "\n"));

    //array durchgehen
    foreach($input AS $key => $value){

	//create login
	$vorname = utf8_encode($value[1]);
	$nachname = utf8_encode($value[0]);
	$login = createUsername($nachname).".".createUsername($vorname);

	//create XML content
	$user = $xml->addChild('User');
	$user->addAttribute('Action', 'Insert');
	$user->addAttribute('Id', $login);

	$user->addChild('Firstname', $vorname);
	$user->addChild('Lastname', $nachname);
	$user->addChild('Email', $value[3]);
	$user->addChild('Login', $login);

	$password = $user->addChild('Password', "willkommen");
	$password->addAttribute('Type', 'PLAIN');

	//the global role
	$role_global = $user->addChild('Role', "Hochschulen NRW");
	$role_global->addAttribute('Action', 'Assign');
	$role_global->addAttribute('Id', '_1');
	$role_global->addAttribute('Type', 'Global');

	//another role (i.e. a local one course/group
	//grp oder course id aus der broweserzeile	
	if(!empty($_POST['group'])){
	    $role_global = $user->addChild('Role', "il_grp_member_".$_POST['group']);
	}
	if(!empty($_POST['course'])){
	    $role_global = $user->addChild('Role', "il_crs_member_".$_POST['course']);
	}
	$role_global->addAttribute('Id', 'il_1469_role_'.$_POST['group_role']);
	$role_global->addAttribute('Type', 'Local');

    }

    //save xml in tmp and prompt download
    $xml->asXML('tmp/import.xml');
    Header('Content-type: text/xml');
    header("Content-Disposition: attachment; filename=\"import.xml\"");
    readfile('tmp/import.xml');


    /* nur darstellen
    Header('Content-type: text/xml');
    echo $xml->asXML();
    */

}


function createUsername($name){


	$name = trim($name);
	$name = mb_strtolower($name, 'utf-8');
	$name = str_replace('ö', 'oe', $name);
	$name = str_replace('ä', 'ae', $name);
	$name = str_replace('ü', 'ue', $name);
	$name = str_replace('ß', 'ss', $name);
	$name = str_replace(' ', '-', $name);
	
	RETURN $name;

}

?>
