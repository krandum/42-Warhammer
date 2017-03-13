<?php

require_once 'IUpkeep.class.php';

abstract Class Weapon implements IUpkeep
{
	const CLOSE = 0;
	const MEDIUM = 1;
	const FAR = 2;
	const OUT = 3;
	protected $damage;
	protected $cost;
	protected $owner;
	protected $defaultCharge;
	public	  $boost;

	public function get_cost() { return $this->cost; }

	public function __construct( $dam, $c, Spaceship $src, $charge )
	{
		$this->damage = $dam;
		$this->cost = $c;
		$this->owner = $src;
		$this->defaultCharge = $charge;
		$this->boost = $this->defaultCharge;
	}

	public function atUpkeep()
	{
		$this->boost = $this->defaultCharge;
	}

	abstract public function shoot( $x, $y );

	abstract public function checkRange( $x, $y );

	protected function getDamage( $dc )
	{
		$out = 0;
		$i = $this->damage * ($this->boost + 1);
		while ($i--)
			if ($dc <= rand(1, 6))
				$out++;
		return $out;
	}

	public static function doc()
	{
		return (file_get_contents("Weapon.doc.txt"));
	}
}

?>