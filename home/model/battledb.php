<?php

    if (!isset($_SESSION['filepath']))
    	$_SESSION['filepath'] = getcwd();

	function database_connect()
	{
		$pathname = $_SESSION['filepath'] . "/private/";
		$filename = 'passwd';
		if (file_exists("$pathname$filename"))
			return (unserialize(file_get_contents("$pathname$filename")));
		else if (!file_exists("$pathname"))
			mkdir("$pathname");
		return (NULL);
	}

	function database_add(string $login, string $key, string $value)
	{
		$pathname = $_SESSION['filepath'] . "/private/";
		$filename = 'passwd';
		$db = database_connect();
		$db[$login][$key] = $value;
		return (file_put_contents("$pathname$filename", serialize($db)));
	}

	function database_win_update(string $winner, string $loser)
	{
		$pathname = $_SESSION['filepath'] . "/private/";
		$filename = 'passwd';
		$db = database_connect();
		$db[$winner]['win'] = parseInt($db[$winner]['win']) + 1;
		$db[$loser]['loss'] = parseInt($db[$loser]['loss']) - 1;
		battle_queue_del($winner);
		battle_queue_del($loser);
		return (file_put_contents("$pathname$filename", serialize($db)));
	}
	
	function battle_queue_connect()
	{
		$pathname = $_SESSION['filepath'] . "/private/";
		$filename = 'queue';
		if (file_exists("$pathname$filename"))
			return (unserialize(file_get_contents("$pathname$filename")));
		else if (!file_exists("$pathname"))
			mkdir("$pathname");
		return (NULL);
	}

	function battle_queue_exist($username)
	{
		$bq = battle_queue_connect();
		if (!$bq)
		{
			return (FALSE);
		}
		return (array_key_exists($username, $bq));
	}

	function battle_queue_add(string $login, string $opponent, string $faction, string $fleet)
	{
		$pathname = $_SESSION['filepath'] . "/private/";
		$filename = 'queue';
		$bq = battle_queue_connect();
		$bq[$login] = ['time' => time(), 'opponent' => $opponent, 'faction' => $faction, 'fleet' => $fleet, 'session' => session_id(), 'match' => '-1', 'first' => '1'];
		return (file_put_contents("$pathname$filename", serialize($bq)));
	}

	function battle_queue_del(string $login)
	{
		$pathname = $_SESSION['filepath'] . "/private/";
		$filename = 'queue';
		$bq = battle_queue_connect();
		unset($bq[$login]);
		return (file_put_contents("$pathname$filename", serialize($bq)));
	}

	function matchMaker($user)
	{
		$pathname = $_SESSION['filepath'] . "/private/";
		$filename = 'queue';
		$bq = battle_queue_connect();
		foreach ($bq as $opponent => $info)
		{
			if ($opponent !== $user && $info['match'] == '-1')
			{
				$bq[$opponent]['match'] = $user;
				$bq[$user]['match'] = $opponent;
				$bq[$opponent]['match_id'] = $bq[$user]['time'] . $user . $opponent;
				$bq[$user]['match_id'] = $bq[$opponent]['match_id'];
				$_SESSION['fleet_size'] = $bq[$user]['fleet']; // 500, 1500, 3000
				$_SESSION['first'] = '1'; // 1
				$_SESSION['faction'] = $bq[$user]['faction']; // red, blue, green
				$_SESSION['other_sess'] = $bq[$opponent]['session']; // match value
				$_SESSION['other_login'] = $opponent; // match value
				$_SESSION['login'] = $user; // username
				$_SESSION['other_faction'] = $bq[$opponent]['faction'];
				return (file_put_contents("$pathname$filename", serialize($bq)));
			}
		}
		return (NULL);
	}
	function matchFound($user)
	{
		$pathname = $_SESSION['filepath'] . "/private/";
		$filename = 'queue';
		$bq = battle_queue_connect();
        if ($bq[$user]['match'] != '-1') 
        {
        		$_SESSION['fleet_size'] = $bq[$user]['fleet']; // 500, 1500, 3000
				$_SESSION['first'] = '0'; // 0
				$_SESSION['faction'] = $bq[$user]['faction']; // red, blue, green
				$_SESSION['other_sess'] = $bq[$bq[$user]['match']]['session']; // cookie value
				$_SESSION['other_login'] = $bq[$user]['match']; // match value
				$_SESSION['login'] = $user; // username
				$_SESSION['other_faction'] = $bq[$bq[$user]['match']]['faction'];
				return (TRUE);
        }
        return (FALSE);
    }
?>