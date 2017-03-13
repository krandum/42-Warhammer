<?php

Class Bomber extends Spaceship
{
	public function __construct( $x, $y, $d, $f )
	{
		parent::__construct( array(
			'name' => "Bringer of Chaos",
			'len' => 4,
			'thick' => 3,
			'sprite' => "sprites/".$f."_med.png",
			'hull' => 6,
			'power' => 10,
			'speed' => 12,
			'handling' => 6,
			'primary' => new Blaster( $this ),
			'secondary' => new Cannon( $this ),
			'dir' => $d,
			'fac' => $f ) );
		$this->x = $x;
		$this->y = $y;
	}

	public static function doc()
	{
		return (file_get_contents("Bomber.doc.txt"));
	}
}

?>