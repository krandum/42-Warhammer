<?php
	require_once('battledb.php');
	require_once('hash.php');
	
	function people_create(string $username, string $passwd)
	{
		$err = array();
		if (strlen($username) < 1)
			$err[] = 'username';
		if (strlen($passwd) < 1)
			$err[] = 'password';
		else
			$passwd = user_pass($passwd);
		if (!empty($err))
			return ($err);
		if (database_add($username, 'passwd', $passwd) !== FALSE)
		{
			database_add($username, 'win', 0);
			database_add($username, 'loss', 0);
			return TRUE;
		}
		return (array('general'));
	}

	function people_exist($username)
	{
		$db = database_connect();
		if (!$db)
		{
			echo ("database_connect() failed" . PHP_EOL);
			return (FALSE);
		}
		return (array_key_exists($username, $db));
	}

	function people_get($username, $password)
	{
		$db = database_connect();
		$password = user_pass($password);
		if (array_key_exists($username, $db) && $password === $db[$username]['passwd'])
			return ($username);
		return NULL;
	}
?>