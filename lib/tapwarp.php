<?php
class TapWarp
{
	public $serveHandlerObj;
	
	public function __construct()
	{
		$this->serveHandlerObj=new stdClass;
		$this->serveHandlerObj->serve=function ($req, TapWarp $tw) {
			$tw->respond('OK');
		};
	}

	public function serve()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Headers: Content-Type, Content-Length, Content-Transfer-Encoding');
		header('Access-Control-Allow-Methods: POST');
		if ($_SERVER['REQUEST_METHOD']!='POST')
			return;

		$content=file_get_contents('php://input');
		$req=json_decode($content);
		if (!preg_match('%^[0-9A-Za-z]{64,128}$%',$req->authKey))
			$this->respond('NG','Invalid authKey.');
		else if (!preg_match('%^-?[0-9]+$%',$req->ordinal))
			$this->respond('NG','Invalid ordinal');
		else if (!preg_match('%^-?[0-9]+$%',$req->total))
			$this->respond('NG','Invalid total');
		else if ($req->format && !preg_match('%^-?[a-z]+(/[a-z0-9_-]+)?$%',$req->format))
			$this->respond('NG','Invalid format');
		else
			$this->serveHandlerObj->serve($req,$this);
	}

	public function respond($result,$message='')
	{
		header('Content-Type: application/json');
		$res=new stdClass;
		$res->result=$result;
		$res->message=$message;
		echo json_encode($res);
	}
}
