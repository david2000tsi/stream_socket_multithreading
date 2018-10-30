<?php

namespace StreamSocket;

class SocketServer
{
	private $socket;
	private $ip;
	private $port;
	private $errno;
	private $errstr;
	private $handleMultipleConnections;
	private $stopAccept;

	public function __construct(string $ip, int $port, bool $handleMultipleConnections = true)
	{
		$this->ip = $ip;
		$this->port = $port;
		$this->errno = 0;
		$this->errstr = "";
		$this->handleMultipleConnections = $handleMultipleConnections;
		$this->stopAccept = false;

		$this->socket = stream_socket_server("tcp://".$this->ip.":".$this->port, $this->errno, $this->errstr);
	}

	public function __destruct()
	{
		if($this->socket)
		{
			fclose($this->socket);
			$this->socket = null;
		}
	}

	public function isServerCreated()
	{
		return ((bool) $this->socket);
	}

	public function getErrorDescription()
	{
		if(!$this->socket)
		{
			return $this->errstr." (".$this->errno.")";
		}
		return "";
	}

	public function stopAccept()
	{
		$this->stopAccept = true;
	}

	// Waits for client's connection, when $this->handleMultipleConnections is false should be passed how many tries is to be executed.
	// When $blocking is true the this function only will be return when has a valid connection.
	public function accept(bool $blocking = false, int $timeout = 1)
	{
		$this->stopAccept = false;
		$read = array($this->socket);
		$write = null;
		$exception = null;
		$newConnection = false;

		if(!$this->socket)
		{
			return false;
		}

		if($this->handleMultipleConnections)
		{
			while(!$this->stopAccept)
			{
				stream_select($read, $write, $exception, $timeout);

				foreach($read as $conn)
				{
					if($conn == $this->socket)
					{
						$newConnection = stream_socket_accept($this->socket, $timeout);
					}
				}

				if(($blocking && $newConnection) || (!$blocking))
				{
					break;
				}
			}
		}
		else
		{
			$newConnection = @stream_socket_accept($this->socket, $timeout);
		}
		return $newConnection;
	}

	// Case $this->handleMultipleConnections is false soh we are using single connection, isn ot cenessary pass $connectionId (use default value).
	public static function recvfrom($connection, int $length)
	{
		$receivedData = "";
		$continue = true;

		while($continue)
		{
			$buffer = stream_socket_recvfrom($connection, $length);
			$recv = strlen($buffer);

			if($buffer && $recv > 0)
			{
				$receivedData .= $buffer;

				if($recv < $length)
				{
					$continue = false;
				}
			}
			else
			{
				$continue = false;
			}
		}

		return $receivedData;
	}

	// Case $this->handleMultipleConnections is false soh we are using single connection, isn ot cenessary pass $connectionId (use default value).
	public static function sendto($connection, string $data)
	{
		return (stream_socket_sendto($connection, $data) == 0);
	}

	// Case $this->handleMultipleConnections is false soh we are using single connection, isn ot cenessary pass $connectionId (use default value).
	public static function close($connection)
	{
		fclose($connection);
	}
}
