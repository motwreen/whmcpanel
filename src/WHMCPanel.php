<?php

namespace Motwreen\WHMCPanel;

use Motwreen\WHMCPanel\Classes\Methods;
use Config;

class WHMCpanel
{	
	protected $method;
	protected $params;
	public 	  $response;
	protected $url;

	public function checkForMethod($method)
	{
		if(!in_array($method, array_keys($this->methodsList()))){
			throw new \RuntimeException('Method Not Found: '.$method);
		}
	}

	private function methodsList()
	{
		return [
			'listaccts'=>[],
			'listpkgs'=>[],
			'accountsummary'=>[],
			'createacct'=>[],
			'suspendacct'=>['user','reason'],
			'unsuspendacct'=>['user'],
		];	
	}

	public function __call($name,$arguments)
	{
		$this->checkForMethod($name);
		$this->method = $name;
		$this->setParams($arguments);
		$this->bulidUrl();
		$this->fire();
		$this->parseJson();
		return current( (Array)$this->response->data );
	}

	private function bulidUrl()
	{
		$server 			= Config::get('services.whm.server');
		$port 				= Config::get('services.whm.port');
		$endpoint 			= Config::get('services.whm.endpoint');
		$url 				= "https://$server:$port/$endpoint/$this->method?api.version=1$this->params";
		$this->url 			= $url;
	}

	private function setParams(Array $paramArray)
	{
		$paramString = null;
		$i = 0;
		if($paramArray){
			foreach ($paramArray[0] as $key => $value) {
					$paramString .= "&$key=$value";
				$i++;
			}
		}
		$this->params = $paramString;
		return $paramString;
	}


	private function fire()
	{
		$username	= Config::get('services.whm.username');
		$password	= Config::get('services.whm.password');
		$curl 		= curl_init();                                // Create Curl Object
		$header[0] 	= "Authorization: Basic " . base64_encode($username.":".$password) . "\n\r";
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);       // Allow self-signed certs
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);       // Allow certs that do not match the hostname
		curl_setopt($curl, CURLOPT_HEADER,0);               // Do not include header in output
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);       // Return contents of transfer on curl_exec
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);    // set the username and password
		curl_setopt($curl, CURLOPT_URL, $this->url);            // execute the query
		
		$result = curl_exec($curl);
		if ($result == false) {
			throw new \RuntimeException(curl_error($curl));
		}
		curl_close($curl);
		$this->response = $result; 
	}


	public function parseJson()
	{
		$data = json_decode($this->response);
		$this->response = $data; 
		return $this->response;
	}
		
}
