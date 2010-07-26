<?php
	$host = "192.168.2.2"; // The network interface that the IdentD should listen on.
	$ident = "Freelancer"; // The Ident you would like to use.
	$port = 113; // You probably don't need to change this; it's the default port.
	set_time_limit(0);
	$socket = socket_create(AF_INET, SOCK_STREAM, 0);
	$result = socket_bind($socket, $host, $port);
	
	function identd() {
		global $ident;
		global $socket;
		global $result;
		$result = socket_listen($socket, 3);	
		$spawn = socket_accept($socket);
		while (true) {
			$input = socket_read($spawn, 1024);
			$input = trim($input);
			if ($input != null) {
				break;
			}
		}
		echo "Incoming Ident request:\r\n";
		echo ">> ".$input."\r\n";
		echo "<< ".$input." : USERID : UNIX : ".$ident."\r\n";
		socket_write($spawn, $input." : USERID : UNIX : ".$ident."\r\n", strlen($input." : USERID : UNIX : ".$ident."\r\n"));
		socket_close($spawn);
	}
	
	while(true) { identd(); }
?>