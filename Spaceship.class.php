<?php

require_once 'IUpkeep.class.php';

Class Spaceship implements IUpkeep
{
	use Collidable, Damageable {
		Collidable::register insteadof Damageable;
	}

	const STATIONARY = 0;
	const MOVING = 1;
	protected	$name;
	protected	$length;
	protected	$thickness;
	protected	$sprite;
	public		$hull; // Changed by other classes
	public		$shield; // Changed by other classes
	public		$power; // Changed by other classes
	protected	$x;
	protected	$y;
	protected	$dir;
	public		$turbo; // Changed by other classes
	protected	$speed;
	protected	$handling;
	protected	$primary;
	protected	$secondary;
	public		$status; // Changed by other classes
	protected	$gone_straight;
	public		$moved; // Changed by other classes
	public		$slowing_down; // Changed by other classes
	public		$can_turn; // Changed by other classes
	protected	$faction;

	public function get_length() { return $this->length; }
	public function get_thickness() { return $this->thickness; }
	public function get_x() { return $this->x; }
	public function get_y() { return $this->y; }
	public function get_speed() { return $this->speed; }
	public function get_handling() { return $this->handling; }
	public function get_gone_straight() { return $this->gone_straight; }
	public function get_dir() { return $this->dir; }
	public function get_sprite() { return $this->sprite; }
	public function get_primary() { return $this->primary; }
	public function get_secondary() { return $this->secondary; }
	public function get_fac() { return $this->faction; }
	public function get_name() { return $this->name; }
	private function _set_dir($newdir) { $this->dir = ($newdir + 4) % 4; }

	public function dealDamage( $args )
	{
		if ($args['weapon'] === "primary")
		{
			return $this->primary->shoot($args['x'], $args['y']);
		}
		else if ($args['weapon'] === "secondary")
		{
			return $this->secondary->shoot($args['x'], $args['y']);
		}
	}

	public function takeDamage( $amount )
	{
		if ($amount > $this->shield)
		{
			$amount -= $this->shield;
			$this->shield = 0;
			$this->hull -= $amount;
		}
		else
			$this->shield -= $amount;
	}

	public function checkDead()
	{
		if ($this->hull >= 0)
			return 0;
		return 1;
	}

	public function checkCollisionAt( $x, $y )
	{
		$xmin = $this->x;
		$xmax = $this->x;
		$ymin = $this->y;
		$ymax = $this->y;
		$halfLen = $this->length / 2;
		$halfThick = $this->thickness / 2;
		switch ($this->dir)
		{
		case 0:
			$ymin -= floor($halfThick);
			$ymax += ceil($halfThick) - 1;
			$xmin -= floor($halfLen);
			$xmax += ceil($halfLen) - 1;
			break;
		case 1:
			$ymin -= ceil($halfLen);
			$ymax += floor($halfLen) - 1;
			$xmin -= floor($halfThick);
			$xmax += ceil($halfThick) - 1;
			break;
		case 2: // TODO: double check that this isn't one off, compare with Move_UI left and right event listeners. To check: position one ship in dir 2 or 3, other ships go through bounds
			$ymin -= floor($halfThick);
			$ymax += ceil($halfThick) - 1;
			$xmin -= floor($halfLen);
			$xmax += ceil($halfLen) - 1;
			break;
		case 3:
			$ymin -= floor($halfLen);
			$ymax += ceil($halfLen) - 1;
			$xmin -= floor($halfThick);
			$xmax += ceil($halfThick) - 1;
			break;
		}
		if ($xmin <= $x && $x <= $xmax && $ymin <= $y && $y <= $ymax)
			return ($this);
		return -1;
	}

	public function dealCollideDamage()
	{
		return $this->hull;
	}

	public function takeCollideDamage( $amount )
	{
		$this->takeDamage($amount);
	}

	public function __construct( array $args )
	{
		$this->name = $args['name'];
		$this->length = $args['len'];
		$this->thickness = $args['thick'];
		$this->sprite = $args['sprite'];
		$this->hull = $args['hull'];
		$this->power = $args['power'];
		$this->speed = $args['speed'];
		$this->handling = $args['handling'];
		$this->primary = $args['primary'];
		$this->secondary = $args['secondary'];
		$this->dir = ($args['dir'] + 4) % 4;
		$this->faction = $args['fac'];
		$this->status = self::MOVING;
		$this->shield = 0;
		$this->turbo = 0;
		$this->gone_straight = 0;
		$this->moved = 0;
		$this->slowing_down = false;
		$this->can_turn = 0;
		Collidable::register($this);
		Damageable::register($this);
	}

	public final function move( $amount )
	{
		$goal = 0;
		$cur = 0;
		$iter = 0;
		$check = 0;
		switch ($this->dir)
		{
		case 0:
			$goal = $this->x + $amount;
			$cur = $this->x;
			$iter = 1;
			break;
		case 1:
			$goal = $this->y + $amount;
			$cur = $this->y;
			$iter = 1;
			break;
		case 2:
			$goal = $this->x - $amount;
			$cur = $this->x;
			$iter = -1;
			break;
		case 3:
			$goal = $this->y - $amount;
			$cur = $this->y;
			$iter = -1;
			break;
		}
		try {
			$cur += ceil($this->length / 2) * $iter;
			while (($iter === 1 && $cur < $goal) || ($iter === -1 && $cur > $goal))
			{
				$selfcheck = 0;
				if ($this->dir % 2 == 0)
				{
					$check = Collidable::checkAllAt( $cur, $this->y );
					$selfcheck = $this->checkCollisionAt( $cur, $this->y );
				}
				else
				{
					$check = Collidable::checkAllAt( $this->x, $cur );
					$selfcheck = $this->checkCollisionAt( $this->x, $cur );
				}
				if ($check !== -1 && $selfcheck === -1)
					throw new Exception("collision!");
				$cur = $cur + $iter;
			}
		}
		catch (Exception $e) {
			$dist = $cur - ($this->dir % 2 === 0 ? $this->x : $this->y);
			$this->moved += $dist;
			$this->gone_straight += $dist;
			switch ($this->dir)
			{
			case 0:
				$this->x += $dist;
				break;
			case 1:
				$this->y += $dist;
				break;
			case 2:
				$this->x += $dist;
				break;
			case 3:
				$this->y += $dist;
				break;
			}
			$this->turbo = $this->moved - $this->speed;
			$this->can_turn = 0;
			$this->status = self::STATIONARY;
			$check->takeCollideDamage($this->dealCollideDamage());
			$this->takeCollideDamage($check->dealCollideDamage());
			return -1;
		}
		switch ($this->dir)
		{
		case 0:
			$this->x += $amount;
			break;
		case 1:
			$this->y += $amount;
			break;
		case 2:
			$this->x -= $amount;
			break;
		case 3:
			$this->y -= $amount;
			break;
		}
		$this->moved += $amount;
		$this->gone_straight += $amount;
		if ($this->gone_straight >= $this->handling)
			$this->can_turn = 1;
		if ($this->moved > $this->handling)
		{
			$this->slowing_down = false;
			$this->status = self::MOVING;
		}
	}

	public final function turn( $mod )
	{
		if (!$this->can_turn)
			return;
		$this->_set_dir($this->dir + $mod);
		$this->gone_straight = 0;
		$this->can_turn = 0;
	}

	public function atUpkeep()
	{
		if ($this->slowing_down === true)
			$this->status = self::STATIONARY;
		$this->shield = 0;
		$this->turbo = 0;
		$this->gone_straight = 0;
		$this->moved = 0;
		if ($this->status === self::STATIONARY)
			$this->can_turn = 1;
		else
			$this->can_turn = 0;
		$this->primary->atUpkeep();
		$this->secondary->atUpkeep();
	}

	public static function doc()
	{
		return (file_get_contents("Spaceship.doc.txt"));
	}

	public function __toString() {
		return $this->doc();
	}
}

?>