<?php
include "lib/Facebook_TestAccount.class.php";

$appID = '';
$Secret = '';
$app_access_token = '';
$acc = new Facebook_TestAccount($appID,$Secret,$app_access_token);

echo "<pre>";
	
 switch($_REQUEST['action']){
	case 'create' : 
	$parameter = NULL;
	if($_REQUEST['installed'])$parameter['installed'] = TRUE;
	if($_REQUEST['permissions'])$parameter['permissions'] = $_REQUEST['permissions'];
	print_r($acc->create($parameter));
	break;
	
	case 'getinfo' :
	print_r($acc->getInfoID($_REQUEST['uid']));
	break;
	
	case 'createn' :
	if($_REQUEST['installed'])$parameter['installed'] = TRUE;
	if($_REQUEST['permissions'])$parameter['permissions'] = $_REQUEST['permissions'];
	print_r($acc->createMany($_REQUEST['n'],$parameter));
	break;
	
	case 'connect' : 
	print_r($acc->connect($_REQUEST['uid1'],$_REQUEST['uid2'],$_REQUEST['uat1'],$_REQUEST['uat2']));
	break;
	
	case 'editaccount' :
    print_r($acc->editaccount($_REQUEST['uid'],array('name'=>$_REQUEST['name'],'password'=>$_REQUEST['password'])));
	break;
	
	case 'delete' : 
	print_r($acc->delete($_REQUEST['uid']));
	break;
	
	case 'access' : 
	print_r($acc->access());
	break;
	
	case 'deleteall' :
	print_r($acc->deleteAll());
	break;
	
	default :
	print_r($acc->access());
	break;
 }
 
 echo "</pre>";