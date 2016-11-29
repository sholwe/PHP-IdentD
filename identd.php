<?php
  $runas = "nobody"; // This is who we want to run this process as (drop root)
  $host = "127.0.0.1"; // The network interface that the IdentD should listen on. (IP)
  $ident = "clay"; // The Ident you would like to use.
  $port = 113; // You probably don't need to change this; it's the default port.
  set_time_limit(0);
  $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die();
  $result = socket_bind($socket, $host, $port) or die();
  // Running as root is pretty lame
  $nobody = posix_getpwnam($runas);
  posix_setgid($nobody['gid']) or die();
  posix_setuid($nobody['uid']) or die();

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

  while(true) { identd(); usleep(10000); }
?>
