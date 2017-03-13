<?php

Class Planet
{
	use Collidable;

	private $x;
	private $y;
	private $rad;
	private	$sprite;

	public function get_sprite() { return $this->sprite; }
	public function get_rad() { return $this->rad; }
	public function get_x() { return $this->x; }
	public function get_y() { return $this->y; }
	public function get_fac() { return null; }

	public function checkCollisionAt( $x, $y )
	{
		$dx = $x - $this->x;
		$dy = $y - $this->y;
		$dist = sqrt($dx * $dx + $dy * $dy);
		if ($dist <= $this->rad - 2)
			return $this;
		return -1;
	}

	public function dealCollideDamage()
	{
		return 42000;
	}

	public function takeCollideDamage( $amount )
	{}

	public function __construct( $x, $y, $rad, $sprite )
	{
		$this->x = $x;
		$this->y = $y;
		$this->rad = $rad;
		$this->sprite = $sprite;
		Collidable::register($this);
	}

	public static function doc()
	{
		return (file_get_contents("Planet.doc.txt"));
	}
}

?>