<?php

class POP3 {
	var $host = '192.168.1.1';
	var $port = 110;
	var $timeout = 10;
	var $error = null;
	var $fd = null;

	//function __construct()
    function POP3($host = null, $port = null) {
	    if (!empty($host)) { $this -> host = $host; }
        if (!empty($port)) { $this -> port = $port; }

		$this -> fd = fsockopen($this -> host, $this -> port, $errno, $errstr, $this -> timeout);
		stream_set_blocking($this -> fd,true);
		if( $errno ) {
			//echo 'Error: '.$errno.': '.$errstr;
			//exit(1);
		}

		$msg = $this->_read();
		if(!$this -> _check($msg)) {
			$this -> error = $msg;
			$this -> _write('QUIT');
		}
	}

	function _write($cmd) {
		fwrite($this -> fd, $cmd . "\r\n");
	}

	function _read($stream=false) {
		$retval = null;
		if( ! $stream )
		{
			$retval = fgets($this -> fd,1024);
			//$retval = preg_replace("/\r?\n/","\r\n",$retval);
		} else {
			while( ! feof($this -> fd) )
			{
				$tmp = fgets($this->fd,1024);
				//$tmp = preg_replace("/\r?\n/","\r\n",$tmp);
				if( chop($tmp) == '.') break;
				$retval .= $tmp;
			}
		}
		return $retval;
	}

	function _check($msg) {
		$stat = substr($msg,0,strpos($msg,' '));
		if($stat == '-ERR') return false;
		//if($stat == '+OK') return true;
		return true;
	}

	//login to server
	function pUSERPASS($user, $password) {
		$this->_write('USER '.$user);
		$msg = $this->_read();
		if( ! $this->_check($msg) )
		{
			$this->error = $msg;
			$this->_write('QUIT');
			return false;
		}
		$this->_write('PASS '.$password);
		$msg = $this->_read();
		if( ! $this->_check($msg) )
		{
			$this -> error = $msg;
			$this -> _write('QUIT');
			return false;
		}
		return true;
	}

	function pSTAT() {
		$retval = null;
		$this->_write('STAT');
		$msg = $this->_read();
		if( ! $this->_check($msg) )
		{
			$this->error = $msg;
			$this->_write('QUIT');
			return false;
		}
		list(,$retval['number'],$retval['size']) = explode(' ', $msg);
		return $retval;
	}

	//list messages on server
	function pLIST() {
		$this->_write('LIST');
		$msg = $this->_read();
		if( ! $this->_check($msg) )
		{
			$this->error = $msg;
			$this->_write('QUIT');
			return false;
		}
		$msg = explode("\n", $this->_read(true));
		for($x = 0; $x < sizeof($msg); $x++ )
		{
			$tmp = explode(' ',$msg[$x]);
			$retval[$tmp[0]] = $tmp[1];
		}
		unset($retval['']);
		return $retval;
	}

	//retrive a message from server
	function pRETR($num) {
		$this->_write('RETR '.$num);
		$msg = $this->_read();
		if( ! $this->_check($msg) )
		{
			$this->error = $msg;
			$this->_write('QUIT');
			return false;
		}
		$msg = $this->_read(true);
		return $msg;
	}

	//marks a message deletion from the server
	//it is not actually deleted until the QUIT command is issued.
	//If you lose the connection to the mail server before issuing
	//the QUIT command, the server should not delete any messages
	function pDELE($num) {
		$this->_write('DELE '.$num);
		$msg = $this->_read();
		if( ! $this->_check($msg) )
		{
			$this->error = $msg;
			$this->_write('QUIT');
			return false;
		}
	}

	//This resets (unmarks) any messages previously marked for deletion in this session
	//so that the QUIT command will not delete them
	function pRSET() {
		$this->_write('RSET');
		$msg = $this->_read();
		if( ! $this->_check($msg) )
		{
			$this->error = $msg;
			return false;
		}
	}

	//This deletes any messages marked for deletion, and then logs you off of the mail server.
	//This is the last command to use. This does not disconnect you from the ISP, just the mailbox.
	function pQUIT() {
		$this->_write('QUIT');
		$msg = $this->_read();
		if( ! $this->_check($msg) )
		{
			$this->error = $msg;
			return false;
		}
	}

	function __destruct() {
		fclose($this->fd);
	}
}

?>