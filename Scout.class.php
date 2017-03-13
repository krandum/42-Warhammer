<?php

Class Scout extends Spaceship
{
	public function __construct( $x, $y, $d, $f )
	{
		parent::__construct( array(
			'name' => "Seer of Doom",
			'len' => 2,
			'thick' => 1,
			'sprite' => "sprites/".$f."_small.png",
			'hull' => 3,
			'power' => 10,
			'speed' => 20,
			'handling' => 3,
			'primary' => new Blaster( $this ),
			'secondary' => new Cannon( $this ),
			'dir' => $d,
			'fac' => $f ) );
		$this->x = $x;
		$this->y = $y;
	}

	public static function doc()
	{
		return (file_get_contents("Scout.doc.txt"));
	}
}

?>