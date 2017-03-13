<?php

Class General extends Spaceship
{
	public function __construct( $x, $y, $d, $f )
	{
		parent::__construct( array(
			'name' => "Seer of Doom",
			'len' => 10,
			'thick' => 5,
			'sprite' => "sprites/".$f."_large.png",
			'hull' => 7,
			'power' => 10,
			'speed' => 20,
			'handling' => 7,
			'primary' => new Blaster( $this ),
			'secondary' => new Cannon( $this ),
			'dir' => $d,
			'fac' => $f ) );
		$this->x = $x;
		$this->y = $y;
	}

	public static function doc()
	{
		return (file_get_contents("General.doc.txt"));
	}
}

?>