<?
/*
*	Code by KroniX
*	E-mail: KroniX@rp-welten.net
*	Website: http://rp-welten.net
*/
	// Coreinformationen
	$core = array(
		'type'			=> 0,		// Core Type 0 => Trinity | 1 => Mangos | 2 => Arcemu
		'account_db'	=> '',		// Der Name der Account Datenbank
		'char_db'		=> '',		// Der Name der Charakter Datenbank
		'world_db'		=> '',		// Der Name der World Datenbank
		'realm_id'		=> 1,		// Wenn dein Core Trinity ist, die RP Realm ID
	);

	// Verbindungsinformationen
	$db = array(
		'host'		=> 'localhost',			// Link zur Datenbank
		'port'		=> '3306',				// Datenbank Port
		'user'		=> '',		// Datenbank Account
		'password'	=> '',			// Datenbank Passwort
	);
	
	// MYSQL-Datenbank Verbindung herstellen
	$mysql = mysql_connect($db['host'] . ':' . $db['port'], $db['user'], $db['password']);
	if (!$mysql) die('Connection error: ' . mysql_error());
	unset($db['password']);
?>