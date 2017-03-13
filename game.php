<?php

session_start();

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

var_dump($_SESSION);

$_SESSION['match_id'] = "test";
if ($_SESSION["fleet_size"] === "500" && $_SESSION["first"] == "1")
{
	echo "first";
	$_SESSION['log'] = "logs/".$_SESSION['match_id'].".txt";
	data_init();
	get_data();
	echo "got data";
	$_SESSION['my-ships'] = array(
		new Scout(4, 36, 0, $_SESSION['faction']),
		new scout(4, 45, 0, $_SESSION['faction']),
		new Scout(4, 55, 0, $_SESSION['faction']),
		new scout(4, 65, 0, $_SESSION['faction']),
		new Bomber(7, 40, 0, $_SESSION['faction']),
		new Bomber(7, 60, 0, $_SESSION['faction']),
		new General(7, 50, 0, $_SESSION['faction']),
	);
	$_SESSION['their-ships'] = array(
		new Scout(146, 35, 2, $_SESSION['other_faction']),
		new scout(146, 45, 2, $_SESSION['other_faction']),
		new Scout(146, 55, 2, $_SESSION['other_faction']),
		new scout(146, 65, 2, $_SESSION['other_faction']),
		new Bomber(143, 40, 2, $_SESSION['other_faction']),
		new Bomber(143, 60, 2, $_SESSION['other_faction']),
		new General(143, 50, 2, $_SESSION['other_faction']),
	);
	$_SESSION['ships'] = array_merge($_SESSION['my-ships'], $_SESSION['their-ships']);
	$_SESSION['cur_turn'] = "Shoot";
	sleep(1);
	send_data(serialize($_SESSION['ships']));
	exit();
	var_dump($_SESSION['ships']);
	get_data();
}
else if ($_SESSION["fleet_size"] == "500" && $_SESSION["first"] == "0")
{
	echo "second ";
	$_SESSION['log'] = "logs/".$_SESSION['match_id'].".txt";
	data_init();
	sleep(1);
	echo "waited, sending ";
	send_data(array("ready".time()));
	data_init();
	echo "sent ";
	exit();
	$_SESSION['cur_turn'] = "OTHER";
	$_SESSION['ships'] = get_data();
	var_dump($_SESSION['ships']);
	echo "received ";
	sleep(1);
	send_data(array("got it"));
	echo "got ";
}

$_SESSION['planets'] = array(
		'Alpha' => new Planet(54, 29, 14, "sprites/planet_greenlarge.png"),
		'Beta' => new Planet(76, 71, 14, "sprites/planet_redlarge.png")
	);

exit();
$_SESSION['activeship'] = "";
$_SESSION['clog'] = Collidable::$clog;

?>
<html>
<head>
	<style>
		html {
			height: 98%;
			font-family: arial;
		}

		body {
			width: 98%;
			height: 100%;
			border: 0;
			padding: 0;
			margin: 0;
			/*background-image: url("sprites/background.jpg");*/
		}

		table {
			width: 100%;
			height: 100%;
			border-spacing: 1px;
			border: 0;
			padding: 0;
			margin: 0;
			z-index: 3;
		}

		.space {
			position: relative;
			border: 0;
			padding: 0;
			margin: 0;
			width: 26px;
			height: 26px;
			min-width: 26px;
			min-height: 26px;
			z-index: 3;
		}

		.close {
			background-color: rgba(0, 255, 0, 0.6);
		}

		.medium {
			background-color: rgba(255, 255, 0, 0.6);
		}

		.far {
			background-color: rgba(255, 165, 0, 0.6);
		}

		.out {
			background-color: rgba(255, 0, 0, 0.6);
		}

		.ref {
			display: none;
		}

		.ship {
			position: absolute;
			display: block;
			z-index: 2;
			background: none;
			overflow: hidden;
		}

		.ship_finder {
			position: absolute;
			display: none;
			z-index: 5;
		}

		.sprite {
			position: relative;
			width: 200%;
			height: 200%;
			top: -50%;
			left: -50%;
			background-repeat: no-repeat;
			background-position: center center;
		}

		.planet {
			position: absolute;
			display: block;
			background: none;
			overflow: hidden;
			z-index: 1;
		}

		.handly, .space.handly {
			background-color: rgba(255, 165, 0, 0.6);
			z-index: 6;
		}

		.speedy, .space.speedy {
			background-color: rgba(143, 176, 256, 0.6);
			z-index: 6;
		}

		.sweet, .space.sweet, .space.handly.sweet {
			background-color: rgba(0, 255, 0, 0.6);
			z-index: 6;
		}

		.strict, .sweet.strict, .space.sweet.strict, .space.handly.sweet.strict {
			background-color: rgba(255, 0, 0, 0.6);
			z-index: 6;
		}

		.inspect {
			position: absolute;
			display: none;
			justify-content: center;
			align-items: center;
			width: 126px;
			height: 84px;
			font-size: 1.4vw;
			border-radius: 10px;
			z-index: 3;
			background-color: rgba(173, 216, 230, 0.56);
			color: white;
		}

		#hud {
			display: none;
			position: fixed;
			border-radius: 10px;
			text-align: center;
			font-size: 2em;
			justify-content: space-between;
			align-items: center;
			flex-flow: column;
			width: 14%;
			height: 84%;
			top: 8%;
			z-index: 4;
			right: none;
			color: white;
		}

		.blue {
			background-color: rgba(173, 216, 230, 0.56);
		}

		.red {
			background-color: rgba(255, 69, 0, 0.56);
		}

		.green {
			background-color: rgba(156, 221, 92, 0.56);
		}

		.r_hud {
			right: 20px;
		}

		.l_hud {
			left: 20px;
		}

		#recap_hud {
			display: none;
			position: fixed;
			border-radius: 10px;
			text-align: center;
			font-size: 2em;
			justify-content: space-between;
			align-items: center;
			flex-flow: column;
			width: 36%;
			height: 14%;
			top: 43%;
			right: 32%;
			z-index: 4;
			color: white;
		}

		#pp {
			width: 84%;
			height: 10%;
			top: 0;
		}

		#speed {
			width: 84%;
			height: 30%;
			background-image: url('sprites/icon_speed.png');
			background-size: 120% auto;
			display: flex;
			flex-flow: column-reverse;
			justify-content: center;
			align-items: center;
			background-repeat: no-repeat;
			background-position: center center;
		}

		#shield {
			width: 84%;
			height: 30%;
			background-image: url('sprites/icon_shield.png');
			background-size: 90% auto;
			display: flex;
			flex-flow: column-reverse;
			justify-content: center;
			align-items: center;
			background-repeat: no-repeat;
			background-position: center center;
		}

		#damage {
			width: 84%;
			height: 30%;
			background-image: url('sprites/icon_damage.png');
			background-size: 90% auto;
			display: flex;
			flex-flow: column-reverse;
			justify-content: center;
			align-items: center;
			background-repeat: no-repeat;
			background-position: center center;
		}

		#done {
			width: 84%;
			height: 30%;
			background-image: url('sprites/icon_done.png');
			background-size: 80% auto;
			background-repeat: no-repeat;
			background-position: center center;
		}

		#d_hud {
			display: none;
			position: fixed;
			border-radius: 10px;
			text-align: center;
			font-size: 2em;
			flex-direction: row-reverse;
			justify-content: space-between;
			align-items: flex-end;
			width: 76%;
			height: 26%;
			left: 12%;
			bottom: 20px;
			z-index: 4;
			color: white;
		}

		#left {
			width: 30%;
			height: 76%;
			background-image: url('sprites/icon_left.png');
			background-size: auto 90%;
			background-repeat: no-repeat;
			background-position: center center;
		}

		#right {
			width: 30%;
			height: 76%;
			background-image: url('sprites/icon_right.png');
			background-size: auto 90%;
			background-repeat: no-repeat;
			order: -1;
			background-position: center center;
		}

		#status {
			position: absolute;
			width: 30%;
			height: 24%;
			left: 20%;
			top: 0;
			display: flex;
			justify-content: center;
			font-size: 2.4vw;
		}

		#speed_points {
			position: absolute;
			width: 30%;
			height: 24%;
			right: 20%;
			top: 0;
			display: flex;
			justify-content: center;
			font-size: 2.4vw;
		}

		#prime {
			width: 30%;
			height: 76%;
			background-image: url('sprites/icon_damage.png');
			background-size: 76% 90%;
			background-repeat: no-repeat;
			background-position: center center;
			font-size: 2.4vw;
		}

		#duo {
			width: 30%;
			order: -1;
			height: 76%;
			background-image: url('sprites/icon_shield.png');
			background-repeat: no-repeat;
			background-position: center center;
			background-size: 76% 90%;
			font-size: 2.4vw;
		}

		#d_hud #done {
			width: 30%;
			height: 76%;
			background-size: auto 90%;
			order: -2;
		}

		#recap_hud #done {
			width: 30%;
			height: 76%;
			background-size: auto 90%;
			order: -2;
		}
	</style>
</head>
<body id="body">
	<table>
<?php
$i = -1;
while (++$i < 100)
{
	echo "<tr>\n";
	$j = -1;
	while (++$j < 150)
	{
		echo "<td><div class='space' id='".$i."x".$j."''></div></td>\n";
	}
	echo "</tr>\n";
}
?>
	</table>
<?php
foreach ($_SESSION['ships'] as $key => $ship)
{
	echo "<div class='ref' id='".$key."ref' style=\"";
	echo "width: ".ceil($ship->get_length() / 2)."px; ";
	echo "min-width: ".floor($ship->get_length() / 2)."px; ";
	echo "height: ".ceil($ship->get_thickness() / 2)."px; ";
	echo "min-height: ".floor($ship->get_thickness() / 2)."px; ";
	echo "\"></div>";

	echo "<div class='ship' id='".$key."' style=\"";
	switch ($ship->get_dir())
	{
	case 0:
		echo "width: ".(29 * $ship->get_length())."px; ";
		echo "min-width: ".(29 * $ship->get_length())."px; ";
		echo "height: ".(29 * $ship->get_thickness())."px; ";
		echo "min-height: ".(29 * $ship->get_thickness())."px; ";
		echo "top: ".(29 * ($ship->get_y() - floor($ship->get_thickness() / 2)))."px; ";
		echo "left: ".(29 * ($ship->get_x() - ceil($ship->get_length() / 2)))."px; ";
		break;
	case 1:
		echo "width: ".(29 * $ship->get_thickness())."px; ";
		echo "min-width: ".(29 * $ship->get_thickness())."px; ";
		echo "height: ".(29 * $ship->get_length())."px; ";
		echo "min-height: ".(29 * $ship->get_length())."px; ";
		echo "top: ".(29 * ($ship->get_y() - ceil($ship->get_length() / 2)))."px; ";
		echo "left: ".(29 * ($ship->get_x() - ceil($ship->get_thickness() / 2)))."px; ";
		break;
	case 2:
		echo "width: ".(29 * $ship->get_length())."px; ";
		echo "min-width: ".(29 * $ship->get_length())."px; ";
		echo "height: ".(29 * $ship->get_thickness())."px; ";
		echo "min-height: ".(29 * $ship->get_thickness())."px; ";
		echo "top: ".(29 * ($ship->get_y() - floor($ship->get_thickness() / 2)))."px; ";
		echo "left: ".(29 * ($ship->get_x() - floor($ship->get_length() / 2)))."px; ";
		break;
	case 3:
		echo "width: ".(29 * $ship->get_thickness())."px; ";
		echo "min-width: ".(29 * $ship->get_thickness())."px; ";
		echo "height: ".(29 * $ship->get_length())."px; ";
		echo "min-height: ".(29 * $ship->get_length())."px; ";
		echo "top: ".(29 * ($ship->get_y() - floor($ship->get_length() / 2)))."px; ";
		echo "left: ".(29 * ($ship->get_x() - floor($ship->get_thickness() / 2)))."px; ";
		break;
	}
	echo "\"><div class='sprite' id='".$key."s' style=\"";
	echo "background-image: url('".($ship->get_sprite())."'); ";
	echo "background-size: ".(29 * $ship->get_length())."px ".(29 * $ship->get_thickness())."px; ";
	switch ($ship->get_dir())
	{
	case 0:
		echo "transform: rotate(0deg); ";
		break;
	case 1:
		echo "transform: rotate(90deg); ";
		break;
	case 2:
		echo "transform: rotate(180deg); ";
		break;
	case 3:
		echo "transform: rotate(270deg); ";
		break;
	}
	echo "\"></div></div>";
	echo "<div class='inspect' id='".$key."ins'></div>";
	echo "<div class='ship_finder' id='".$key."find'></div>";
}

foreach ($_SESSION['planets'] as $key => $planet)
{
	echo "<div class='planet' id='".$key."' style=\"";
	echo "width: ".(29 * $planet->get_rad() * 2)."px; ";
	echo "min-width: ".(29 * $planet->get_rad() * 2)."px; ";
	echo "height: ".(29 * $planet->get_rad() * 2)."px; ";
	echo "min-height: ".(29 * $planet->get_rad() * 2)."px; ";
	echo "top: ".(29 * $planet->get_y() - (29 * $planet->get_rad()))."px; ";
	echo "left: ".(29 * $planet->get_x() - (29 * $planet->get_rad()))."px; ";
	echo "\"><div class='sprite' id='".$key."s' style=\"";
	echo "background-image: url('".($planet->get_sprite())."'); ";
	echo "background-size: 100% auto;";
	echo "width: ".(29 * $planet->get_rad() * 2)."px; ";
	echo "min-width: ".(29 * $planet->get_rad() * 2)."px; ";
	echo "height: ".(29 * $planet->get_rad() * 2)."px; ";
	echo "min-height: ".(29 * $planet->get_rad() * 2)."px; ";
	echo "left: 0px;";
	echo "top: 0px;";
	echo "\"></div></div>";
}
?>

	<div class="ui" id="hud"></div>
	<div class="ui" id="d_hud"></div>
	<div class="ui" id="recap_hud"></div>
	<script>

	var cur_ship = -1;
	var locked_ship = -1;
	var locked_x = -1;
	var locked_y = -1;
	var hc = -1;
	var sp = 0;
	var hulls;
	var shields;

	function getRange(params, caller, id) {
		hc = new XMLHttpRequest();
		hc.open("POST", "getrange.php", false);
		hc.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		hc.onreadystatechange = function () {
			if (hc.readyState == 4 && hc.status == 200)
			{
				var schism = hc.responseText.split("&");
				var space = document.getElementById(id);
				var color;
				switch (schism[0])
				{
					case "close":
						space.className += " close";
						color = "rgba(0, 255, 0, 0.6)";
						break;
					case "medium":
						space.className += " medium";
						color = "rgba(255, 255, 0, 0.6)";
						break;
					case "far":
						space.className += " far";
						color = "rgba(255, 165, 0, 0.6)";
						break;
					case "out":
						space.className += " out";
						color = "rgba(255, 0, 0, 0.6)";
						break;
				}
				if (schism[1]) {
					var index = schism[1].split("=")[1];
					if (index)
					{
						lock_on_ship(index, color);
						locked_x = schism[2];
						locked_y = schism[3];
					}
				}
			}
		}
		hc.send(params);
	}

	function unlock_ship()
	{
		if (locked_ship != -1)
		{
			var prev = document.getElementById(locked_ship.toString());
			prev.style.backgroundColor = "rgba(0,0,0,0.0)";
			prev.style.borderRadius = "0";
			prev.style.boxShadow = "none";
		}
	}

	function lock_on_ship(index, color) {
		if (locked_ship != index && locked_ship != -1)
			unlock_ship();
		locked_ship = index;
		var shipDiv = document.getElementById(index.toString());
		shipDiv.style.backgroundColor = color;
		shipDiv.style.borderRadius = "3px";
		shipDiv.style.boxShadow = "0 0 20px " + color;
	}

	function getAction(params) {
		hc = new XMLHttpRequest();
		hc.open("POST", "control.php", false);
		hc.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		hc.onreadystatechange = function () {
			unlock_ship();
			if (hc.readyState == 4 && hc.status == 200)
				control_master(hc.responseText);
		}
		hc.send(params);
	}

	var setRange;
	var reseter;

	function setShootRange( cost1, cost2, fac ) {
		let i = -1;
		while (++i < 100)
		{
			let j = -1;
			while (++j < 150)
			{
				var xs = "x";
				let myi = i;
				let myj = j;
				let id = "" + myi + "x" + myj;
				let space = document.getElementById(id);
				let sender = "x=" + myj + "&y=" + myi;

				setRange = function (evt) {
					getRange(sender, evt.target, id);
				}

				reseter = function (evt) {
					space.className = space.className.split(" ")[0];
				}

				space.addEventListener("mouseover", setRange, false);
				space.addEventListener("mouseout", reseter, false);
			}
		}

		function openFire() {
			unsetShootRange(fac);
			getAction("locked=" + locked_ship + "&x=" + locked_x + "&y=" + locked_y);
			locked_ship = -1;
		}

		var hud;
		hud = document.getElementById("d_hud");
		hud.className += " " + fac;
		hud.style.display = "flex";
		var done = document.createElement('div');
		done.id = "done";
		done.className = "done";
		hud.appendChild(done);
		done.addEventListener("click", openFire);
		var prime = document.createElement('div');
		prime.id = "prime";
		prime.className = "prime";
		hud.appendChild(prime);
		prime.innerHTML = "Cost: " + cost1;
		var duo = document.createElement('div');
		duo.id = "duo";
		duo.className = "duo";
		hud.appendChild(duo);
		duo.innerHTML = "Cost: " + cost2;
	}

	function unsetShootRange( fac ) {
		var i = -1;
		while (++i < 100)
		{
			var j = -1;
			while (++j < 150)
			{
				var istr = i.toString();
				var xs = "x";
				var jstr = j.toString();
				var id = istr.concat(xs.concat(jstr));
				var space = document.getElementById(id);

				space.className = space.className.split(" ")[0];
				var newspace = space.cloneNode(true);
				space.parentNode.replaceChild(newspace, space);
			}
		}
		var hud;
		hud = document.getElementById("d_hud");
		hud.className += " " + fac;
		hud.style.display = "none";
		var done = document.getElementById("done");
		done.parentNode.removeChild(done);
		var prime = document.getElementById("prime");
		prime.parentNode.removeChild(prime);
		var duo = document.getElementById("duo");
		duo.parentNode.removeChild(duo);
	}

	function makeTarget(index) {
	}

	function updatePower(dif) {
		var pp = document.getElementById("pp");
		var msg = pp.innerHTML;
		var schism = msg.split(" ");
		var prev = schism[0];
		schism[0] = parseInt(prev) + parseInt(dif);
		pp.innerHTML = schism.join(' ');
	}

	function addTrait(evt) {
		var pp = document.getElementById("pp");
		var msg = pp.innerHTML;
		var schism = msg.split(" ");
		var power = schism[0];
		if (power > 0)
		{
			var msg = evt.target.innerHTML;
			var schism = msg.split("x");
			var prev = schism[0];
			evt.target.innerHTML = ++prev + "x";
			var dif = -1;
			updatePower(dif);
		}
	}

	function spend_UI(power, fac) {
		var hud;
		hud = document.getElementById("hud");
		hud.className += " " + fac;
		if (<?php echo '"'.$_SESSION['first'].'"'; ?> == 1)
			hud.className += " r_hud";
		else
			hud.className += " l_hud";
		hud.style.display = "flex";
		var pp = document.createElement('div');
		pp.id = "pp";
		pp.className = "pp";
		hud.appendChild(pp);
		pp.innerHTML = power + " pp";

		var speed = document.createElement('div');
		speed.id = "speed";
		speed.className = "speed";
		hud.appendChild(speed);
		speed.innerHTML = "0x";
		speed.addEventListener("click", addTrait);

		var shield = document.createElement('div');
		shield.id = "shield";
		shield.className = "shield";
		hud.appendChild(shield);
		shield.innerHTML = "0x";
		shield.addEventListener("click", addTrait);

		var damage = document.createElement('div');
		damage.id = "damage";
		damage.className = "damage";
		hud.appendChild(damage);
		damage.innerHTML = "0x";
		damage.addEventListener("click", addTrait);

		function saveOptions(evt) {
			var speedDiv = document.getElementById("speed");
			var msg = speedDiv.innerHTML;
			speedDiv.parentNode.removeChild(speedDiv);
			var schism = msg.split("x");
			var speedVal = schism[0];
			var shieldDiv = document.getElementById("shield");
			msg = shieldDiv.innerHTML;
			shieldDiv.parentNode.removeChild(shieldDiv);
			schism = msg.split("x");
			var shieldVal = schism[0];
			var damageDiv = document.getElementById("damage");
			msg = damageDiv.innerHTML;
			damageDiv.parentNode.removeChild(damageDiv);
			schism = msg.split("x");
			var damageVal = schism[0];
			var powerDiv = document.getElementById("pp");
			msg = powerDiv.innerHTML;
			powerDiv.parentNode.removeChild(powerDiv);
			schism = msg.split(" ");
			var powerVal = schism[0];
			evt.target.parentNode.removeChild(evt.target);
			hud.style.display = "none";
			getAction("power=" + powerVal + "&speed=" + speedVal + "&shield=" + shieldVal + "&damage=" + damageVal);
		}

		var done = document.createElement('div');
		done.id = "done";
		done.className = "done";
		hud.appendChild(done);
		done.addEventListener("click", saveOptions);
	}

	function setup_inspects() {
		var len = hulls.length;
		let i = -1;
		while (++i < len)
		{
			let ship = document.getElementById(i.toString() + "find");
			let cur = i;

			function inspect(evt) {
				var elem = document.getElementById(cur + "ins");
				elem.style.display = "flex";
				elem.style.left = ship.style.left.split("p")[0] + "px";
				elem.style.top = ship.style.top.split("p")[0] - 84 + "px";
				elem.innerHTML = "Hull: " + hulls[cur] + "<br>Shields: " + shields[cur];
			}

			function hide_inspect(evt) {
				var elem = document.getElementById(cur + "ins");
				elem.style.display = "none";
			}

			ship.addEventListener("mouseover", inspect);
			ship.addEventListener("mouseout", hide_inspect);
		}
	}

	function activate_ship(index) {
		if (cur_ship != -1)
		{
			var prev = document.getElementById(cur_ship.toString());
			prev.style.backgroundColor = "rgba(0,0,0,0.0)";
			prev.style.borderRadius = "0";
			prev.style.boxShadow = "none";
		}
		else
		{
			setup_inspects();
		}
		cur_ship = index;
		var shipDiv = document.getElementById(index.toString());
		shipDiv.style.backgroundColor = "rgba(173, 216, 230, 0.56)";
		shipDiv.style.borderRadius = "3px";
		shipDiv.style.boxShadow = "0 0 20px rgba(86,180,239,0.8)";
	}

	function setGlow(evt) {
		evt.target.style.borderRadius = "6px";
		evt.target.style.boxShadow = "0 0 20px rgba(86,180,239,0.8)";
	}

	function unGlow(evt) {
		evt.target.style.borderRadius = "0";
		evt.target.style.boxShadow = "none";
	}

	var moveShip;

	function forgetMoveBinds(dir, speed, index, canTurn, fac ) {
		var lx, rx, ty, by;
		var x = parseInt(index.split("x")[0]);
		var y = parseInt(index.split("x")[1]);
		switch (dir)
		{
		case 0:
			lx = x + 1;
			rx = x + speed;
			ty = y;
			by = y;
			break;
		case 1:
			lx = x;
			rx = x;
			ty = y + 1;
			by = y + speed;
			break;
		case 2:
			lx = x - speed;
			rx = x - 1;
			ty = y;
			by = y;
			break;
		case 3:
			lx = x;
			rx = x;
			ty = y - speed;
			by = y - 1;
			break;
		}
		let i = ty - 1;
		while (++i <= by && i >= 0)
		{
			let j = lx - 1;
			while (++j <= rx && j >= 0)
			{
				var istr = i.toString();
				var xs = "x";
				var jstr = j.toString();
				var id = istr.concat(xs.concat(jstr));
				var space = document.getElementById(id);
				var straight = Math.max(Math.abs(x - j), Math.abs(y - i));

				space.className = space.className.split(" ")[0];
				var newspace = space.cloneNode(true);
				space.parentNode.replaceChild(newspace, space);
			}
		}
		if (canTurn == 1)
		{
			var leftDiv = document.getElementById("left");
			leftDiv.parentNode.removeChild(leftDiv);
			var rightDiv = document.getElementById("right");
			rightDiv.parentNode.removeChild(rightDiv);
		}
		var doneDiv = document.getElementById("done");
		doneDiv.parentNode.removeChild(doneDiv);
		var statusDiv = document.getElementById("status");
		statusDiv.parentNode.removeChild(statusDiv);
		var speed_pointsDiv = document.getElementById("speed_points");
		speed_pointsDiv.parentNode.removeChild(speed_pointsDiv);
		var hud;
		hud = document.getElementById("d_hud");
		hud.className += " " + fac;
		hud.style.display = "none";
	}

	function setMoveBinds(dir, handling, speed, index, status, strict, canTurn, fac) {
		var lx, rx, ty, by;
		var x = parseInt(index.split("x")[0]);
		var y = parseInt(index.split("x")[1]);
		switch (dir)
		{
		case 0:
			lx = x + 1;
			rx = x + speed;
			ty = y;
			by = y;
			break;
		case 1:
			lx = x;
			rx = x;
			ty = y + 1;
			by = y + speed;
			break;
		case 2:
			lx = x - speed;
			rx = x - 1;
			ty = y;
			by = y;
			break;
		case 3:
			lx = x;
			rx = x;
			ty = y - speed;
			by = y - 1;
			break;
		}
		let i = ty - 1;
		while (++i <= by && i >= 0)
		{
			let j = lx - 1;
			while (++j <= rx && j >= 0)
			{
				var istr = i.toString();
				var xs = "x";
				var jstr = j.toString();
				var id = istr.concat(xs.concat(jstr));
				var space = document.getElementById(id);
				let myi = i;
				let myj = j;
				var straight = Math.max(Math.abs(x - j), Math.abs(y - i));

				moveShip = function(evt) {
					var straight = Math.max(Math.abs(x - myj), Math.abs(y - myi));
					var statusMod = 0;
					if (status == 0 && straight > handling)
						statusMod = 1;
					sp -= straight;
					var ship = document.getElementById(cur_ship.toString());
					var changer = (Math.abs(x - myj) > Math.abs(y - myi)) ? ship.style.left : ship.style.top;
					changer = parseInt(changer.split("p")[0]);
					if (Math.abs(x - myj) > Math.abs(y - myi))
					{
						changer += (myj - x) * 29;
						ship.style.left = changer + "px";
					}
					else
					{
						changer += (myi - y) * 29;
						ship.style.top = changer + "px";
					}
					unGlow(evt);
					forgetMoveBinds(dir, speed, index, canTurn, fac);
					getAction("straight=" + straight + "&mod=" + statusMod);
				};

				if (straight < strict)
					space.className += ' strict';
				else
				{
					if (straight < handling)
						space.className += ' handly';
					else if (straight == handling)
						space.className += ' sweet';
					else
						space.className += ' speedy';
					space.addEventListener("mouseover", setGlow, false);
					space.addEventListener("mouseout", unGlow, false);
					space.addEventListener("click", moveShip, false);
				}
			}
		}
	}

	function move_UI(dir, handling, speed, index, ship_status, strict, canTurn, fac) {
		sp = speed;
		setMoveBinds(dir, handling, speed, index, ship_status, strict, canTurn, fac);
		var hud;
		hud = document.getElementById("d_hud");
		hud.className += " " + fac;
		hud.style.display = "flex";

		if (canTurn == 1)
		{
			var left = document.createElement('div');
			left.id = "left";
			hud.appendChild(left);
			left.addEventListener("click", function(){
				var ship = document.getElementById(cur_ship.toString());
				var ref_ship = document.getElementById(cur_ship.toString() + "ref");
				var width = ship.style.width;
				var height = ship.style.height;
				ship.style.width = height;
				ship.style.minWidth = height;
				ship.style.height = width;
				ship.style.minHeight = width;
				var x = parseInt(index.split("x")[0]);
				var y = parseInt(index.split("x")[1]);
				var xc = ref_ship.style.width.split("p")[0];
				var yc = ref_ship.style.height.split("p")[0];
				var xf = ref_ship.style.minWidth.split("p")[0];
				var yf = ref_ship.style.minHeight.split("p")[0];
				switch (dir)
				{
				case 0: // new is [3]
					ship.style.top = (29 * (y - xf + 1)) + "px";
					ship.style.left = (29 * (x - yf)) + "px";
					break;
				case 1: // new is [0]
					ship.style.top = (29 * (y - yf)) + "px";
					ship.style.left = (29 * (x - xf)) + "px";
					break;
				case 2: // new is [1]
					ship.style.top = (29 * (y - xc)) + "px";
					ship.style.left = (29 * (x - yf)) + "px";
					break;
				case 3: // new is [2]
					ship.style.top = (29 * (y - yf)) + "px";
					ship.style.left = (29 * (x - xf + 1)) + "px";
					break;
				}
				var sprite = document.getElementById(cur_ship.toString() + "s");
				var trans = sprite.style.transform;
				trans = trans.split("(")[1];
				trans = trans.split("d")[0];
				trans = parseInt(trans);
				trans -= 90;
				trans += 360;
				trans %= 360;
				sprite.style.transform = "rotate(" + trans + "deg";
				forgetMoveBinds(dir, speed, index, canTurn, fac);
				getAction("turn=-1");
			});

			var right = document.createElement('div');
			right.id = "right";
			hud.appendChild(right);
			right.addEventListener("click", function(){
				var ship = document.getElementById(cur_ship.toString());
				var ref_ship = document.getElementById(cur_ship.toString() + "ref");
				var width = ship.style.width;
				var height = ship.style.height;
				ship.style.width = height;
				ship.style.minWidth = height;
				ship.style.height = width;
				ship.style.minHeight = width;
				var x = parseInt(index.split("x")[0]);
				var y = parseInt(index.split("x")[1]);
				var xc = ref_ship.style.width.split("p")[0];
				var yc = ref_ship.style.height.split("p")[0];
				var xf = ref_ship.style.minWidth.split("p")[0];
				var yf = ref_ship.style.minHeight.split("p")[0];
				switch (dir)
				{
				case 0: // new is [1]
					ship.style.top = (29 * (y - xc)) + "px";
					ship.style.left = (29 * (x - yf)) + "px";
					break;
				case 1: // new is [2]
					ship.style.top = (29 * (y - yf)) + "px";
					ship.style.left = (29 * (x - xf + 1)) + "px";
					break;
				case 2: // new is [3]
					ship.style.top = (29 * (y - xf + 1)) + "px";
					ship.style.left = (29 * (x - yf)) + "px";
					break;
				case 3: // new is [0]
					ship.style.top = (29 * (y - yf)) + "px";
					ship.style.left = (29 * (x - xf)) + "px";
					break;
				}
				var sprite = document.getElementById(cur_ship.toString() + "s");
				var trans = sprite.style.transform;
				trans = trans.split("(")[1];
				trans = trans.split("d")[0];
				trans = parseInt(trans);
				trans += 90;
				trans += 360;
				trans %= 360;
				sprite.style.transform = "rotate(" + trans + "deg";
				forgetMoveBinds(dir, speed, index, canTurn, fac);
				getAction("turn=1");
			});
		}

		var done = document.createElement('div');
		done.id = "done";
		hud.appendChild(done);
		done.addEventListener("click", function(){
			var statusMod = 0;
			if (strict == 0)
				statusMod = -1;
			forgetMoveBinds(dir, speed, index, canTurn, fac);
			getAction("done=yes&mod=" + statusMod);
		});

		var status = document.createElement('div');
		status.id = "status";
		hud.appendChild(status);
		switch (ship_status)
		{
		case 0:
			status.innerHTML = "Status: Stationary";
			break;
		case 1:
			status.innerHTML = "Status: Moving";
			break;
		}

		var speed_points = document.createElement('div');
		speed_points.id = "speed_points";
		hud.appendChild(speed_points);
		speed_points.innerHTML = "Move Points: " + sp;
	}

	function shove(handling, dir)
	{
		var ship = document.getElementById(cur_ship.toString());
		var changer = (dir % 2 == 0) ? ship.style.left : ship.style.top;
		var changer = parseInt(changer.split("p")[0]);
		changer += ((dir / 2 < 1.0) ? 1 : -1) * handling * 29;
		if (dir % 2 == 0)
			ship.style.left = changer + "px";
		else
			ship.style.top = changer + "px";
	}

	function force(ship_in, dir, x, y, index, status)
	{
		var ship = document.getElementById(ship_in.toString());
		var ref_ship = document.getElementById(cur_ship.toString() + "ref");
		var xc = ref_ship.style.width.split("p")[0];
		var yc = ref_ship.style.height.split("p")[0];
		var xf = ref_ship.style.minWidth.split("p")[0];
		var yf = ref_ship.style.minHeight.split("p")[0];
		switch (dir)
		{
		case 0:
			ship.style.top = (29 * (y - yf)) + "px";
			ship.style.left = (29 * (x - xf)) + "px";
			break;
		case 1:
			ship.style.top = (29 * (y - xc)) + "px";
			ship.style.left = (29 * (x - yf)) + "px";
			break;
		case 2:
			ship.style.top = (29 * (y - yf)) + "px";
			ship.style.left = (29 * (x - xf + 1)) + "px";
			break;
		case 3:
			ship.style.top = (29 * (y - xf + 1)) + "px";
			ship.style.left = (29 * (x - yf)) + "px";
			break;
		}
	}

	function demolish(index)
	{
		var ship = document.getElementById(index.toString());
		ship.parentNode.removeChild(ship);
		cur_ship = -1;
	}

	function set_vitalities( controler )
	{
		var l = controler.length - 1;
		hulls = new Array(l - 16);
		shields = new Array(l - 16);
		var i = 15;
		var cur = 0;
		while (++i < l)
		{
			hulls[cur] = controler[i] & 0xf;
			shields[cur] = controler[i] >> 4;
			//alert("Index: " + cur + "; Health: " + hulls[cur] + "; Shield: " + shields[cur]);
			cur++;
		}
	}

	function set_invis(faction) {
		var i = -1;
		var len = hulls.length;
		while (++i < len) {
			var ship = document.getElementById(i.toString());
			var invis = document.getElementById(i.toString() + "find");
			if ((faction == "blue" && i < len / 2) || (faction == "red" && i >= len / 2))
				invis.style.display = "block";
			else
				invis.style.display = "none";
			invis.style.width = ship.style.width;
			invis.style.minWidth = ship.style.minWidth;
			invis.style.height = ship.style.height;
			invis.style.minHeight = ship.style.minHeight;
			invis.style.left = ship.style.left;
			invis.style.top = ship.style.top;
		}
	}

	function set_recap( msg, fac ) {
		var hud;
		hud = document.getElementById("recap_hud");
		hud.className += " " + fac;
		hud.style.display = "flex";
		hud.innerHTML = msg;

		function send_done() {
			hud.style.display = "none";
			var done = document.getElementById("done");
			done.parentNode.removeChild(done);
			getAction("yay=yeah");
		}

		var done = document.createElement('div');
		done.id = "done";
		done.className = "done";
		hud.appendChild(done);
		done.addEventListener("click", send_done);
	}

	function control_master(raw) {
		var controler = raw.split("&");

		set_vitalities(controler);
		switch (controler[0])
		{
		case 'Spend_UI':
			activate_ship(controler[1]);
			if (parseInt(controler[3]) == 1)
				shove(parseInt(controler[4]), parseInt(controler[5]));
			set_invis(controler[6]);
			spend_UI(controler[2], controler[6]);
			break;
		case 'Move_UI':
			set_invis(controler[9]);
			move_UI( parseInt(controler[2]), parseInt(controler[3]), parseInt(controler[5]), controler[6], parseInt(controler[7]), parseInt(controler[4]), parseInt(controler[8]), controler[9] );
			break;
		case 'Shoot_UI':
			set_invis(controler[3]);
			setShootRange(parseInt(controler[1]), parseInt(controler[2]), controler[3]);
			break;
		case 'Recap_UI':
			set_invis(controler[2]);
			if (controler[1] == "No locked target, combat resolves uneventfully.")
				getAction("nothing=meow");
			else
				set_recap(controler[1], controler[2]);
			break;
		case 'Correct':
			force(parseInt(controler[1]), parseInt(controler[2]), parseInt(controler[3]), parseInt(controler[4]), controler[6], parseInt(controler[7]));
			getAction("nothing=meow");
			break;
		case 'Remove':
			demolish(parseInt(controler[1]));
			getAction("done=yes");
			break;
		}
	}

	//getAction("nothing=meow");
	</script>
</body>
</html>