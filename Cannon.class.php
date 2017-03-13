<?php

Class Cannon extends Weapon
{
	public function __construct( Spaceship $src )
	{
		parent::__construct(2, 1, $src, 1);
	}

	public function shoot( $x, $y )
	{
		$dx = abs($this->owner->get_x() - $x);
		$dy = abs($this->owner->get_y() - $y);
		$delta = $dx + $dy;
		$this->owner->power -= $this->cost;
		if ($this->owner->status === Spaceship::MOVING)
			return -1;
		if ($delta <= 10)
			 return $this->getDamage(4);
		else if ($delta <= 25)
			return $this->getDamage(5);
		else if ($delta <= 40)
			return $this->getDamage(6);
		$this->owner->power += $this->cost;
		return -1;
	}

	public function checkRange( $x, $y )
	{
		$dx = abs($this->owner->get_x() - $x);
		$dy = abs($this->owner->get_y() - $y);
		$delta = $dx + $dy;
		if ($this->owner->status === Spaceship::MOVING)
			return Weapon::OUT;
		if ($delta <= 10)
			 return Weapon::CLOSE;
		else if ($delta <= 20)
			return Weapon::MEDIUM;
		else if ($delta <= 30)
			return Weapon::FAR;
		return Weapon::OUT;
	}

	public static function doc()
	{
		return (file_get_contents("Cannon.doc.txt"));
	}
}

?>