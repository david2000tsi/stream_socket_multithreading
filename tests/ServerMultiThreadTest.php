<?php

require_once(__DIR__.'/../vendor/autoload.php');

use StreamSocket\SocketServer;
use StreamSocket\ClientConnectionHandle;

class ClientConnectionCallback extends ClientConnectionHandle
{
	public function executeTask()
	{
		echo("Thread running on client connection (".$this->clientId.")\n");

		$recv = SocketServer::recvfrom($this->clientConnection, 64);
		if($recv)
		{
			echo($recv."\n");
			SocketServer::sendto($this->clientConnection, "Sending data from server to client (connection ".$this->clientId.")");
		}

		$this->finished = true;
		return;
	}
}

class ServerMultiThreadTest
{
	public static function run()
	{
		$socketServer = new SocketServer("127.0.0.1", 8000);
		$clientConnections = [];
		$clientId = 1;

		if(!$socketServer->isServerCreated())
		{
			echo($socketServer->getErrorDescription());
			return;
		}

		while(true)
		{
			echo("Waiting for client connections...".date("Y/m/d H:i:s")."\n");
			$clientConnection = $socketServer->accept();
			if($clientConnection)
			{
				echo("Connection accepted...\n");
				$client = new ClientConnectionCallback($clientId++, $clientConnection);
				$clientConnections[] = $client;

				$client->start();
			}

			foreach($clientConnections as $conn)
			{
				echo "Checking threads...\n";
				if($conn->isFinished() && $conn->join())
				{
					echo("Closing client connection (".$conn->getClientId().")\n");
					SocketServer::close($conn->getClientConnection());
					array_splice($clientConnections, array_search($conn, $clientConnections), 1);
				}
			}
		}
	}
}

ServerMultiThreadTest::run();
