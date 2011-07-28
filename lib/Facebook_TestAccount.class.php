<?php
Class Facebook_TestAccount {
   protected $AppId;
   protected $AppSecret;
   protected $app_access_token;
   protected $domain = "graph.facebook.com";
   
   function __construct($AppId,$AppSecret,$app_access_token = NULL){
     $this->AppId = $AppId;
	 $this->AppSecret = $AppSecret;
	 if($app_access_token){
		$this->app_access_token = $app_access_token;
	 }else{
		if(!$this->app_access_token = $this->getAppAccessToken()){
		  die("Could Not Generate Access Token for this App, Please Provide by yourself");
		}
     }
   }
   
   public function getAppAccessToken(){
    $parameter = array('client_id'   => $this->getAppId(),
						'client_secret' => $this->getAppSecret(),
						'grant_type'    => 'client_credentials');
	try{
			$request = $this->request('/oauth/access_token', 'POST',$parameter);
		} catch (Exception $e){ die($e); }
	$request = $request ? str_replace('access_token=','',$request) : NULL;	
	return $request;						 
   }
   
   public function getAppId() {
     return $this->AppId;
   }   
   
   public function getAppSecret() {
    return $this->AppSecret;
   }
   
   public function getInfoID($id) {
     try{
			$request = $this->request('/' . $id, 'GET',NULL,false);
			} catch (Exception $e){ $request = $e; }
	return $request;	
   }
   
   public function create($parameter = null){
    if(!is_array($parameter))$parameter = array();

    try{
	    if($parameter['name']){ $next_name = $parameter['name']; }else{
	       $users = $this->access();
		   $next_name = substr(md5(time()),4,3)." user".(count($users['data'])+1);}
		   
		   	$default_parameter = array('installed'=>TRUE,
										'name'=>$next_name,
										'permissions' => 'publish_stream');
			$parameter = array_merge($default_parameter,$parameter);	
			$parameter['access_token'] = $this->app_access_token;
		   
			$request = $this->request('/' . $this->getAppId() . '/accounts/test-users', 'POST',$parameter);
			} catch (Exception $e){ $request = $e; }
    return $request;
   }
   
   public function createMany($maxUser,$parameter = null){
    $users = $this->access();
	$total_user = count($users['data']);
    $reqs = array();	
	$default_parameter = array('installed'=>TRUE,'permissions' => 'publish_stream');
	$parameter = array_merge($default_parameter,$parameter);	
	$parameter['access_token'] = $this->app_access_token;
	
	for($i=1;$i<=$maxUser;$i++){
	  $next_name = substr(md5(time()),4,3)." user".($total_user+$i);	
		try{
			$parameter['name'] = $next_name;
			$request = $this->request('/' . $this->getAppId() . '/accounts/test-users', 'POST',$parameter);
			$reqs[] = $request;
			} catch (Exception $e){ $request = $e; }
	}		
    return $reqs;
   }
   
   public function createManyConnected($maxUser,$parameter = array()){
	$parameter['installed'] = TRUE;
	$reqs = $this->createMany($maxUser,$parameter);
    $req_connected = $this->recursiveConnect($reqs);
    return $req_connected;
   }
   
   public function recursiveConnect( array $stack, $stack_connected = array()){
    $pin_item = array_shift($stack);
	foreach($stack as $item){
	 if($request = $this->connect($pin_item['id'],$item['id'],$pin_item['access_token'],$item['access_token']))
	 {
	   $stack_connected[] = $request;
	 }
	}
	if(count($stack) <= 1) return $stack_connected;
	$this->recursiveConnect($stack,$stack_connected);
   }
   
   public function connect($uid1,$uid2,$uat1,$uat2 = NULL){
	$request = array();	
		try{
			$request1 = $this->request('/' . $uid1 . '/friends/' . $uid2, 'POST',
			                          array('access_token'=>$uat1));
			$request[$uid1."_".$uid2][] = $request1;						  
			if($uat2){
				$request2 = $this->request('/' . $uid2 . '/friends/' . $uid1, 'POST',
										  array('access_token'=>$uat2));
				$request[$uid1."_".$uid2][] = $request2;						  
			}
			
			
			} catch (Exception $e){ $request = false; }
	return $request;	
   }
   
   public function editAccount($uid,$parameter){
   if(!is_array($parameter))$parameter = array();
   $default_parameter = array('name'=>NULL,'password'=>NULL);
			$parameter = array_merge($default_parameter,$parameter);	
			$parameter['access_token'] = $this->app_access_token;
	try{
			$request = $this->request('/' . $uid, 'POST' ,$parameter);
			} catch (Exception $e){ $request = false; }
	return $request;	
   }
   
   public function delete($uid){
    try{
			$request = $this->request('/' . $uid, 'POST' ,
						array('method'=>'delete','access_token'=>$this->app_access_token));
			} catch (Exception $e){ $request = false; }
	return $request;	
   }
   
   public function deleteAll(){
     $request = $this->access();
	 $ret = null;
	 foreach($request['data'] as $value){
		if($deleted = $this->delete($value['id'])){
		  $ret[] = $value['id'];
		}
	 }
	 return $ret;
   }
   
   public function access(){
    try{
			$request = $this->request('/' . $this->getAppId() . '/accounts/test-users', 'POST',
						array('access_token'=>$this->app_access_token,'method'=>'GET'));
			} catch (Exception $e){ $request = false; }
    return $request;		
   }
   
   protected function request($path,$method = "POST",$args = array(),$ssl = true){
   $ch = curl_init();
   $method = strtoupper($method);
   $url = $ssl ? "https://".$this->domain.$path : "http://".$this->domain.$path;
   
    if($method == 'POST'){ 
		curl_setopt($ch, CURLOPT_POST, true); 
	}elseif($method == 'GET'){
		curl_setopt($ch, CURLOPT_HTTPGET, true);
	}elseif($method == 'DELETE'){
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPGET, true);
	}
	 
    if($args)
	 curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args, null, '&'));
	
	curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   
	curl_setopt($ch, CURLOPT_URL, $url);
	
	$result = curl_exec($ch);
	if ($result === false) {
      curl_close($ch);
      throw $e;
    }
	curl_close($ch);
	
	return json_decode($result,true);
   }
   
}