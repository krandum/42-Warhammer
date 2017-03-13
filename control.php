<?php

require_once 'IUpkeep.class.php';
require_once 'Collidable.trait.php';
require_once 'Damageable.trait.php';
require_once 'Spaceship.class.php';
require_once 'Weapon.class.php';
require_once 'Planet.class.php';
require_once 'Blaster.class.php';
require_once 'Cannon.class.php';
require_once 'Scout.class.php';
require_once 'Bomber.class.php';
require_once 'General.class.php';
require_once 'send_data.php';

session_start();

if (array_key_exists("clog", $_SESSION))
	Collidable::$clog = $_SESSION['clog'];

$out = array();

function get_positions( $prev_output, $arr )
{
	$more_ands = 16 - $prev_output;
	while ($more_ands-- > 0)
		$arr[] = "&";
	foreach ($_SESSION['ships'] as $ship)
		$arr[] = ($ship->hull | ($ship->shield << 4))."&";
	return $arr;
}

if ($_SESSION['cur_turn'] === "OTHER")
{
	data_init();
	echo get_data()[0];
}
else if ($_SESSION['activeship'] === "")
{
	$_SESSION['cur_turn'] = "Order";
	$_SESSION['activeship'] = $_SESSION['ships'][0];
	$ship = $_SESSION['activeship'];
	$ship->atUpkeep();
	$out[] = "Spend_UI"; // 0
	$out[] = "&0"; // 1
	$out[] = "&".$ship->power; // 2
	$out[] = "&".$ship->status; // 3
	$out[] = "&".$ship->get_handling(); // 4
	$out[] = "&".$ship->get_dir(); // 5
	$out[] = "&".$ship->get_fac(); // 6
	$out = get_positions(6, $out);
	echo implode($out);
	send_data(array(implode($out)));
}
else if ($_SESSION['cur_turn'] === "Moving" && $_POST['done'] === "yes")
{
	$_SESSION['cur_turn'] = "Shoot";
	$ship = $_SESSION['activeship'];
	$out[] = "Shoot_UI"; // 0
	$out[] = "&".$ship->get_primary()->get_cost(); // 1
	$out[] = "&".$ship->get_secondary()->get_cost(); // 2
	$out[] = "&".$ship->get_fac(); // 3
	$out = get_positions(3, $out);
	echo implode($out);
	send_data(array(implode($out)));
}
else if ($_SESSION['cur_turn'] === "Order" && array_key_exists('power', $_POST)) // First move input screen
{
	$_SESSION['cur_turn'] = "Moving";
	$key = array_search($_SESSION['activeship'], $_SESSION['ships']);
	$ship = $_SESSION['activeship'];
	if ($ship->checkDead() === 1)
	{
		$out[] = "Remove";
		$key = array_search($ship, $_SESSION['ships']);
		$out[] = "&".$key;
		Collidable::remove($ship);
		unset($_SESSION['ships'][$key]);
		array_values($_SESSION['ships']);
		$_SESSION['activeship'] = $_SESSION['ships'][$key];
		$_SESSION['clog'] = Collidable::$clog;
		$_SESSION['cur_turn'] = "Recap";
		echo implode($out);
		send_data(array(implode($out)));
		exit();
	}
	$ship->power = $_POST['power'];
	$i = -1;
	$total = 0;
	while (++$i < $_POST['speed'])
		$total += rand(1, 6);
	$ship->turbo = $total;
	$ship->shield = $_POST['shield'];
	$ship->get_primary()->boost += $_POST['speed'];
	if ($ship->status === Spaceship::MOVING)
		if ($ship->move($ship->get_handling()) === -1)
		{
			if ($ship->checkDead() === 1)
			{
				file_put_contents(("out.txt"), "test");
				$out[] = "Remove";
				$key = array_search($ship, $_SESSION['ships']);
				$out[] = "&".$key;
				Collidable::remove($ship);
				unset($_SESSION['ships'][$key]);
				array_values($_SESSION['ships']);
				$_SESSION['activeship'] = $_SESSION['ships'][$key];
				$_SESSION['clog'] = Collidable::$clog;
				$_SESSION['cur_turn'] = "Recap";
				echo implode($out);
				send_data(array(implode($out)));
				exit();
			}
			$out[] = "Correct"; // 0
			$out[] = "&".$key; // 1
			$out[] = "&".$ship->get_dir(); // 2
			$out[] = "&".$ship->get_x(); // 3
			$out[] = "&".$ship->get_y(); // 4
			$out[] = "&".$_SESSION['cur_turn']; // 5 DOES NOTHING
			$out[] = "&".$ship->get_x()."x".$ship->get_y(); // 6
			$out[] = "&".$ship->status; // 7
			$out[] = "&".$ship->can_turn; // 8
			$_SESSION['clog'] = Collidable::$clog;
			echo implode($out);
			send_data(array(implode($out)));
			exit();
		}
	$out[] = "Move_UI"; // 0
	$out[] = "&".$key; // 1
	$out[] = "&".$ship->get_dir(); // 2
	$out[] = "&".($ship->get_handling() - $ship->get_gone_straight()); // 3
	$out[] = "&".($ship->get_handling() - $ship->moved); // 4
	$out[] = "&".($ship->get_speed() + $ship->turbo - $ship->moved); // 5
	$out[] = "&".$ship->get_x()."x".$ship->get_y(); // 6
	$out[] = "&".$ship->status; // 7
	$out[] = "&".$ship->can_turn; // 8
	$out[] = "&".$ship->get_fac(); // 9
	$out = get_positions(9, $out);
	echo implode($out);
	send_data(array(implode($out)));
}
else if ($_SESSION['cur_turn'] === "Moving" && !array_key_exists('turn', $_POST) && $_POST['done'] !== "yes") // All following moves
{
	$key = array_search($_SESSION['activeship'], $_SESSION['ships']);
	$ship = $_SESSION['activeship'];
	if ($ship->checkDead() === 1)
	{
		$out[] = "Remove";
		$key = array_search($ship, $_SESSION['ships']);
		$out[] = "&".$key;
		Collidable::remove($ship);
		unset($_SESSION['ships'][$key]);
		array_values($_SESSION['ships']);
		$_SESSION['activeship'] = $_SESSION['ships'][$key];
		$_SESSION['clog'] = Collidable::$clog;
		$_SESSION['cur_turn'] = "Recap";
		echo implode($out);
		send_data(array(implode($out)));
		exit();
	}
	if ($ship->move($_POST['straight']) === -1)
	{
		if ($ship->checkDead() === 1)
		{
			file_put_contents(("out.txt"), "test");
			$out[] = "Remove";
			$key = array_search($ship, $_SESSION['ships']);
			$out[] = "&".$key;
			Collidable::remove($ship);
			unset($_SESSION['ships'][$key]);
			array_values($_SESSION['ships']);
			$_SESSION['activeship'] = $_SESSION['ships'][$key];
			$_SESSION['clog'] = Collidable::$clog;
			$_SESSION['cur_turn'] = "Recap";
			echo implode($out);
			send_data(array(implode($out)));
			exit();
		}
		$out[] = "Correct"; // 0
		$out[] = "&".$key; // 1
		$out[] = "&".$ship->get_dir(); // 2
		$out[] = "&".$ship->get_x(); // 3
		$out[] = "&".$ship->get_y(); // 4
		$out[] = "&".$_SESSION['cur_turn']; // 5 DOES NOTHING
		$out[] = "&".$ship->get_x()."x".$ship->get_y(); // 6
		$out[] = "&".$ship->status; // 7
		$out[] = "&".$ship->can_turn; // 8
		$out[] = "&".$ship->get_fac(); // 9
		$_SESSION['clog'] = Collidable::$clog;
		echo implode($out);
		send_data(array(implode($out)));
		exit();
	}
	if ($_POST['mod'] === -1)
		$ship->slowing_down = true;
	else if ($_POST['mod'] === 1)
		$ship->status = Spaceship::MOVING;
	$out[] = "Move_UI"; // 0
	$out[] = "&".$key; // 1
	$out[] = "&".$ship->get_dir(); // 2
	$out[] = "&".($ship->get_handling() - $ship->get_gone_straight()); // 3
	$out[] = "&".($ship->get_handling() - $ship->moved); // 4
	$out[] = "&".($ship->get_speed() + $ship->turbo - $ship->moved); // 5
	$out[] = "&".$ship->get_x()."x".$ship->get_y(); // 6
	$out[] = "&".$ship->status; // 7
	$out[] = "&".$ship->can_turn; // 8
	$out[] = "&".$ship->get_fac(); // 9
	$out = get_positions(9, $out);
	echo implode($out);
	send_data(array(implode($out)));
}
else if ($_SESSION['cur_turn'] === "Moving" && array_key_exists('turn', $_POST) && $_POST['done'] !== "yes")
{
	$key = array_search($_SESSION['activeship'], $_SESSION['ships']);
	$ship = $_SESSION['activeship'];
	if ($ship->checkDead() === 1)
	{
		$out[] = "Remove";
		$key = array_search($ship, $_SESSION['ships']);
		$out[] = "&".$key;
		Collidable::remove($ship);
		unset($_SESSION['ships'][$key]);
		array_values($_SESSION['ships']);
		$_SESSION['activeship'] = $_SESSION['ships'][$key];
		$_SESSION['clog'] = Collidable::$clog;
		$_SESSION['cur_turn'] = "Recap";
		echo implode($out);
		send_data(array(implode($out)));
		exit();
	}
	$ship->turn($_POST['turn']);
	$out[] = "Move_UI"; // 0
	$out[] = "&".$key; // 1
	$out[] = "&".$ship->get_dir(); // 2
	$out[] = "&".($ship->get_handling() - $ship->get_gone_straight()); // 3
	$out[] = "&".($ship->get_handling() - $ship->moved); // 4
	$out[] = "&".($ship->get_speed() + $ship->turbo - $ship->moved); // 5
	$out[] = "&".$ship->get_x()."x".$ship->get_y(); // 6
	$out[] = "&".$ship->status; // 7
	$out[] = "&".$ship->can_turn; // 8
	$out[] = "&".$ship->get_fac(); // 9
	$out = get_positions(9, $out);
	echo implode($out);
	send_data(array(implode($out)));
}
else if ($_SESSION['cur_turn'] === "Shoot")
{
	$_SESSION['cur_turn'] = "Recap";
	$ship = $_SESSION['activeship'];
	if ($ship && $ship->checkDead() === 1)
	{
		$out[] = "Remove";
		$key = array_search($ship, $_SESSION['ships']);
		$out[] = "&".$key;
		Collidable::remove($ship);
		unset($_SESSION['ships'][$key]);
		array_values($_SESSION['ships']);
		$_SESSION['activeship'] = $_SESSION['ships'][$key];
		$_SESSION['clog'] = Collidable::$clog;
		$_SESSION['cur_turn'] = "Recap";
		echo implode($out);
		send_data(array(implode($out)));
		exit();
	}
	$out[] = "Recap_UI"; // 0
	if (array_key_exists("locked", $_POST) && $_POST['locked'] != -1)
	{
		$enemy = $_SESSION['ships'][$_POST['locked']];
		$damage = $ship->dealDamage( array(
				'weapon' => "primary",
				'x' => $_POST['x'],
				'y' => $_POST['y']
			) );
		$enemy->takeDamage($damage);
		if ($damage == -1)
			$damage = "zero";
		$out[] = "&Dealt ".$damage." to ship ".$enemy->get_name()."."; // 1
	}
	else
		$out[] = "&No locked target, combat resolves uneventfully."; // 1
	$out[] = "&".$ship->get_fac(); // 2
	$out = get_positions(2, $out);
	echo implode($out);
	send_data(array(implode($out)));
}
else if ($_SESSION['cur_turn'] === "Recap")
{
	if ($ship && $ship->checkDead() === 1)
	{
		$out[] = "Remove";
		$key = array_search($ship, $_SESSION['ships']);
		$out[] = "&".$key;
		Collidable::remove($ship);
		unset($_SESSION['ships'][$key]);
		array_values($_SESSION['ships']);
		$_SESSION['activeship'] = $_SESSION['ships'][$key];
		$_SESSION['clog'] = Collidable::$clog;
		$_SESSION['cur_turn'] = "Recap";
		echo implode($out);
		send_data(array(implode($out)));
		exit();
	}
	$_SESSION['cur_turn'] = "Order";
	$ship = $_SESSION['activeship'];
	if ($_POST['mod'] == -1)
		$ship->slowing_down = true;
	$key = array_search($_SESSION['activeship'], $_SESSION['ships']);
	if ($key + 1 === count($_SESSION['ships']))
		$key = -1;
	$ship = $_SESSION['ships'][$key + 1];
	$_SESSION['activeship'] = $_SESSION['ships'][$key + 1];
	$ship->atUpkeep();
	$out[] = "Spend_UI"; // 0
	$out[] = "&".($key + 1); // 1
	$out[] = "&".$ship->power; // 2
	$out[] = "&".$ship->status; // 3
	$out[] = "&".$ship->get_handling(); // 4
	$out[] = "&".$ship->get_dir(); // 5
	$out[] = "&".$ship->get_fac(); // 6
	$out = get_positions(6, $out);
	echo implode($out);
	send_data(array(implode($out)));
}

$_SESSION['clog'] = Collidable::$clog;

?>