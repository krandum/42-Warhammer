<?php
	session_start();
	require_once('../model/people.php');
	require_once('../model/hash.php');
	require_once('../model/battledb.php');

	$functions = array('login', 'register', 'battle');
	function register(array $datas)
	{
		$err = NULL;
		if (!isset($datas['username']))
			$err[] = 'username';
		if (!isset($datas['password']))
			$err[] = 'password';
		if ($err === NULL)
		{
			if (people_exist($datas['username']) === FALSE)
			{
				return (people_create($datas['username'], $datas['password']));
			}
			else
				return (array('exist'));
		}
		else
			return $err;
	}

	function login(array $datas)
	{
		$err = NULL;
		if (!isset($datas['username']))
			$err[] = 'username';
		if (!isset($datas['password']))
			$err[] = 'password';
		if ($err === NULL)
		{
			$login = people_get($datas['username'], $datas['password']);
			if ($login === NULL)
				return (array('notfound'));
			session_name($login);
			$_SESSION['username'] = $login;
			return NULL;
		}
		else
			return ($err);
	}

	function battle(array $datas)
	{
		$err = NULL;
		if (!isset($datas['faction']))
			$err[] = 'faction';
		if (!isset($datas['fleet']))
			$err[] = 'fleet';
		if (!isset($datas['username']))
			$err[] = 'username';
		if ($err === NULL)
		{
			$username = people_exist($datas['username']);
			$opponent = ($datas['opponent'] == "-1") ? '-1' : people_exist($datas['opponent']);
			if ($opponent == FALSE)
				return (array('opponentnotfound'));
			if (!battle_queue_add($datas['username'], $datas['opponent'], $datas['faction'], $datas['fleet']))
				return (array('queue'));
			return NULL;
		}
		else
			return ($err);
	}
	
	if ($_POST['from'] && in_array($_POST['from'], $functions))
	{
		$err = $_POST['from']($_POST);
		if (!($err === TRUE || $err === null))
		{
			$str_error = implode('&', $err);
			if ($_POST['error']){
				header('Location: ../' . $_POST['error'] . '.php?' . $str_error);
				exit();
			}
			header('Location: ../' . $_POST['from'] . '.php?' . $str_error);
			exit();
		}
		header('Location: ../' . $_POST['success'] . '.php');
		exit();
	}
?>