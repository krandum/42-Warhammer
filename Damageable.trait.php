<?php

trait Damageable
{
	private static $log = array();

	abstract public function dealDamage( $args );
	abstract public function takeDamage( $amount );
	abstract public function checkDead();

	public static final function register( $obj )
	{
		$log[] = $obj;
	}
}

?>