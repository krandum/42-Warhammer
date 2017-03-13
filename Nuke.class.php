<?php

Class Nuke extends Weapon
{
	public function __construct( Spaceship $src )
	{
		parent::__construct(5, 3, $src, 0);
	}

	public function shoot( $x, $y )
	{
		$dx = abs($this->owner->get_x() - $x);
		$dy = abs($this->owner->get_y() - $y);
		$delta = $dx + $dy;
		$this->owner->power -= $this->cost;
		if ($delta <= 10)
			 return $this->getDamage(4);
		else if ($delta <= 30)
			return $this->getDamage(5);
		else if ($delta <= 50)
			return $this->getDamage(6);
		$this->owner->power += $this->cost;
		return -1;
	}

	public function checkRange( $x, $y )
	{
		$dx = abs($this->owner->get_x() - $x);
		$dy = abs($this->owner->get_y() - $y);
		$delta = $dx + $dy;
		if ($delta <= 10)
			 return Weapon::CLOSE;
		else if ($delta <= 30)
			return Weapon::MEDIUM;
		else if ($delta <= 50)
			return Weapon::FAR;
		return Weapon::OUT;
	}

	public static function doc()
	{
		return (file_get_contents("Nuke.doc.txt"));
	}
}

?>