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

session_start();

if (array_key_exists("clog", $_SESSION))
	Collidable::$clog = $_SESSION['clog'];

$ship = $_SESSION['activeship'];

if ($ship && $ship !== "" && $_SESSION['cur_turn'] === "Shoot")
{
	$x = $_POST['x'];
	$y = $_POST['y'];
	$status = $ship->get_primary()->checkRange($x, $y);
	switch ($status)
	{
		case Weapon::CLOSE:
			echo "close"; // rgba(0, 255, 0, 0.6)
			break;
		case Weapon::MEDIUM:
			echo "medium"; // rgba(255, 255, 0, 0.6)
			break;
		case Weapon::FAR:
			echo "far"; // rgba(255, 165, 0, 0.6)
			break;
		case Weapon::OUT:
			echo "out"; // rgba(255, 0, 0, 0.6)
			break;
	}
	echo "&";
	$enemy = Collidable::checkAllAt($x, $y);
	if ($enemy !== -1 && $enemy->get_fac() !== null)
	{
		$index = array_search($enemy, $_SESSION['ships']);
		if ($index !== false && $enemy->get_fac() !== $ship->get_fac())
		{
			echo "lock=".$index;
		}
	}
	echo "&".$x."&".$y;
}

$_SESSION['clog'] = Collidable::$clog;

?>