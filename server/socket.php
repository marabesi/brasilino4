<?php

define('FORWARD',  25);
define('BACKWARD', 24);
define('LEFT',     23);
define('RIGHT',    22);

$host = 'localhost';
$port = '9002';
$null = NULL;

shell_exec('gpio mode ' . RIGHT . ' out');
shell_exec('gpio mode ' . LEFT . ' out');
shell_exec('gpio mode ' . BACKWARD . ' out');
shell_exec('gpio mode ' . FORWARD . ' out');

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

socket_bind($socket, 0, $port);

socket_listen($socket);

$clients = array($socket);

echo 'Socket listening on host: ' . $host . ' port: ' . $port . PHP_EOL;

while (true)
{
	$changed = $clients;

	socket_select($changed, $null, $null, 0, 10);

	if (in_array($socket, $changed))
    {
		$socket_new = socket_accept($socket);
		$clients[] = $socket_new;

		$header = socket_read($socket_new, 1024);
		perform_handshaking($header, $socket_new, $host, $port);

		socket_getpeername($socket_new, $ip);
		$response = mask(json_encode(array('server' => $ip . ' connected')));
		send_message($response);

		$found_socket = array_search($socket, $changed);
		unset($changed[$found_socket]);
	}

	foreach ($changed as $changed_socket)
    {
		while(socket_recv($changed_socket, $buf, 1024, 0) >= 1)
		{
			$received_text = unmask($buf);
			$jsonObject = json_decode($received_text);

			if (strpos($jsonObject->data, '[EES]') !== false ||
					strpos($jsonObject->data, '[DDS]') !== false ||
					strpos($jsonObject->data, '[AAS]') !== false ||
					strpos($jsonObject->data, '[FFS]') !== false)
			{
				if ($jsonObject->data == '[AAS]') {
            shell_exec('gpio write ' . FORWARD . ' 1');
        }
        if ($jsonObject->data == '[DDS]') {
            shell_exec('gpio write ' . RIGHT . ' 1');
        }
        if ($jsonObject->data == '[EES]') {
            shell_exec('gpio write ' . LEFT . ' 1');
        }
        if ($jsonObject->data == '[FFS]') {
						shell_exec('gpio write ' . BACKWARD . ' 1');
        }
			}

			if (strpos($jsonObject->data, '[EEE]') !== false ||
					strpos($jsonObject->data, '[DDE]') !== false ||
					strpos($jsonObject->data, '[AAE]') !== false ||
					strpos($jsonObject->data, '[FFE]') !== false)
			{
				if ($jsonObject->data == '[AAE]') {
            shell_exec('gpio write ' . FORWARD . ' 0');
        }
        if ($jsonObject->data == '[DDE]') {
            shell_exec('gpio write ' . RIGHT . ' 0');
        }
        if ($jsonObject->data == '[EEE]') {
            shell_exec('gpio write ' . LEFT . ' 0');
        }
        if ($jsonObject->data == '[FFE]') {
						shell_exec('gpio write ' . BACKWARD . ' 0');
        }
			}

      $createResponse = json_encode(array('server' => $ip . ' : ' . utf8_encode($received_text)));

      echo $received_text . PHP_EOL;

			$response_text = mask($createResponse);
			send_message($response_text);

			break 2;
		}

		$buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);

		if ($buf === false)
        {
			$found_socket = array_search($changed_socket, $clients);
			socket_getpeername($changed_socket, $ip);
			unset($clients[$found_socket]);

			$response = mask(json_encode(array('server' => $ip . ' disconnected')));
			send_message($response);
		}
	}
}

socket_close($sock);

function send_message($msg)
{
	global $clients;
	foreach($clients as $changed_socket)
	{
		@socket_write($changed_socket,$msg,strlen($msg));
	}
	return true;
}

function unmask($text)
{
	$length = ord($text[1]) & 127;
	if($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}

function mask($text)
{
	$b1 = 0x80 | (0x1 & 0x0f);
	$length = strlen($text);

	if($length <= 125)
		$header = pack('CC', $b1, $length);
	elseif($length > 125 && $length < 65536)
		$header = pack('CCn', $b1, 126, $length);
	elseif($length >= 65536)
		$header = pack('CCNN', $b1, 127, $length);
	return $header.$text;
}

function perform_handshaking($receved_header,$client_conn, $host, $port)
{
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach($lines as $line)
	{
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
		{
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($client_conn,$upgrade,strlen($upgrade));
}
