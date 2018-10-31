<?php

namespace StreamSocket;

use Thread;

abstract class ClientConnectionHandle extends Thread
{
	private $clientId;
	private $clientConnection;
	private $finished;

	public function __construct(int $clientId,  $clientConnection)
	{
		$this->clientId = $clientId;
		$this->clientConnection = $clientConnection;
		$this->finished = false;
	}

	protected abstract function executeTask();

	public function getClientId()
	{
		return $this->clientId;
	}

	public function getClientConnection()
	{
		return $this->clientConnection;
	}

	public function isFinished()
	{
		return $this->finished;
	}

	public function run()
	{
		$this->executeTask();
	}
}
