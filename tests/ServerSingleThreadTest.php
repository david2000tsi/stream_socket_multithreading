<?php

require_once(__DIR__.'/../vendor/autoload.php');

use StreamSocket\SocketServer;

class ServerSingleThreadTest
{
	public static function run()
	{
		$socketServer = new SocketServer("127.0.0.1", 8000, false);
		$clientConnections = [];
		$clientId = 1;

		if(!$socketServer->isServerCreated())
		{
			echo($socketServer->getErrorDescription());
			return;
		}

		while(true)
		{
			echo("Waiting for client connection...".date("Y/m/d H:i:s")."\n");
			$clientConnection = $socketServer->accept();
			if($clientConnection)
			{
				echo("Connection accepted...\n");
				$recv = SocketServer::recvfrom($clientConnection, 64);
				if($recv)
				{
					echo($recv."\n");
					SocketServer::sendto($clientConnection, "Sending data from server to client.");
				}

				echo("Closing client connection.\n");
				SocketServer::close($clientConnection);
			}
		}
	}
}

ServerSingleThreadTest::run();
