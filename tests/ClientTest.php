<?php

require_once(__DIR__.'/../vendor/autoload.php');

use StreamSocket\SocketClient;

class ClientTest
{
	public static function run()
	{
		$socketClient = new SocketClient("127.0.0.1", 8000);
		if(!$socketClient->isOpenedSocket())
		{
			return;
		}

		$result = $socketClient->write("Sending data from client to server");
		if($result)
		{
			$rec = $socketClient->read(128);
			if($rec)
			{
				echo($rec."\n");
			}
		}
		$socketClient->close();
		$socketClient = null;
	}
}

ClientTest::run();
