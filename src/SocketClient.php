<?php

namespace StreamSocket;

class SocketClient
{
	private $socket;
	private $ip;
	private $port;
	private $errno;
	private $errstr;

	public function __construct(string $ip, int $port)
	{
		$this->ip = $ip;
		$this->port = $port;
		$this->errno = 0;
		$this->errstr = "";
		$this->receivedData = "";

		$this->socket = stream_socket_client("tcp://".$this->ip.":".$this->port, $this->errno, $this->errstr);
	}

	public function __destruct()
	{
	}

	public function isOpenedSocket()
	{
		return ((bool) $this->socket);
	}

	public function write(string $data)
	{
		$written = null;
		$bytesToWrite = strlen($data);

		if($bytesToWrite == 0)
		{
			return false;
		}

		$written = fwrite($this->socket, $data);

		if(!$written || $written != $bytesToWrite)
		{
			return false;
		}

		return true;
	}

	public function read(int $length)
	{
		if(!$this->socket)
		{
			return false;
		}

		$this->receivedData = "";

		while(!feof($this->socket))
		{
			$this->receivedData .= fread($this->socket, $length);
		}

		return $this->receivedData;
	}

	public function close()
	{
		if($this->socket)
		{
			fclose($this->socket);
			$this->socket = null;
		}
	}
}
