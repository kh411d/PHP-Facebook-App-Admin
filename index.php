<style>
/* QUICKFORM BASE STYLE */
.quickform form {
    margin: 0;
    padding: 0;
}
.quickform fieldset {
    clear: both;
    border: 1px solid #808080;
    margin: 0.2em 0 0 0;
    padding: 0.5em;
	width:200px;
}
.quickform fieldset legend {
    white-space: nowrap;
    font-weight: bold;
    background-color: #CCCCCC;
    padding: 0.1em 0.5em;
    display: block;
    margin: 0;
}
.quickform label {
    
}
.quickform label.element {
    display: block;
    float: left;
    padding: 0;
    text-align: right;
}
.quickform input, .quickform textarea, .quickform select {
    width: 200px;
}
.quickform textarea {
    overflow: auto;
}
.quickform br {
    clear: left;
}
.quickform div.row {
    padding: 5px 0px;
    margin: 0;
    clear: both;
	width:250px;
}
.quickform div.element {
    display: inline;
    float: left;
    /*margin: 0.5em 0 0 1em;*/
	width:250px;
    padding: 0;
}
.quickform div.reqnote {
    clear: both;
    font-size: 80%; 
    margin: 0.5em 0 0 1em;
}
.quickform span.error, .quickform span.required {
    color: red;
}
.quickform div.error {
    border: 1px solid red;
    padding: 0.5em;
}
</style>
<?php
include "lib/Facebook_TestAccount.class.php";
define('ROOT_DIR',dirname(realpath(__FILE__)));
$path = ROOT_DIR.'/lib/PEAR/'. PATH_SEPARATOR . ROOT_DIR .'/lib/';
set_include_path($path); 

require_once 'HTML/QuickForm2.php';
require_once 'HTML/QuickForm2/Renderer.php';

//$appID = '172614152809223';
///$Secret = 'c832e8f8265bb5dd5fea7d4d8f016289';
//$acc = new Facebook_TestAccount($appID,$Secret);

	$form = new HTMLQuickForm2('app_login','POST','action=""  ');
		
		 $appid = $form->addElement('text','AppId')->setLabel('App ID');
		 $appid->addRule('required', 'AppId is required', null,HTML_QuickForm2_Rule::SERVER);
		 
		 $appsecret = $form->addElement('text','AppSecret')->setLabel('App Secret');
		 $appsecret->addRule('required', 'AppSecret is required', null,HTML_QuickForm2_Rule::SERVER);
		 $button = $form->addElement('submit','submit','value="App Login"');

		$group_action = $form->addElement('fieldset')->setLabel('Action :');
         $group_action->addElement('select','action','',array('options'=>array('access'=>'List All Users',
																		'create'=>'Create a User',
																		'createn'=>'Create N Users',
																		'connect'=>'Connect 2 Users',
																		'connectAll'=>'Connect All Users',
																		'delete'=>'Delete User',
																		'deleteAll'=>'Delete All Users',
																		'editaccount'=>'Edit User Account',
																		'revoke'=>'UnInstalled a User',
																		'revokeall'=>'UnInstalled All User',
																		'getinfo'=>'Get User Info'
																		)))->setLabel('Method');
		$group_action->addElement('text','n')->setLabel('N Numbers of Users');																
		 
		$group = $form->addElement('fieldset')->setLabel('Parameter :');
		$group->addElement('select','parameter[installed]','',array('options'=>array(''=>'Pick?','true'=>'true','false'=>'false')))->setLabel('User Installed');	
		$group->addElement('text','parameter[permissions]')->setLabel('User App Permissions');	
		$group->addElement('text','parameter[name]')->setLabel('User Name');
		$group->addElement('text','parameter[password]')->setLabel('User Password');
		$group->addElement('text','parameter[uid1]')->setLabel('User ID1');
		$group->addElement('text','parameter[uid2]')->setLabel('User ID2');
			
				

		$renderer = HTML_QuickForm2_Renderer::factory('default');
		 $form_layout = $form->render($renderer);
		
			
echo $form_layout;
exit;
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