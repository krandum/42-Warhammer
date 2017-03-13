<?php

trait Collidable
{
	public static $clog = array(); // has to be public because sessions

	abstract public function checkCollisionAt( $x, $y );
	abstract public function dealCollideDamage();
	abstract public function takeCollideDamage( $amount );

	public static final function register( $obj )
	{
		self::$clog[] = $obj;
	}

	public static final function remove( $obj )
	{
		$key = array_search($obj, self::$clog);
		if ($key !== false)
			unset(self::$clog[$key]);
	}

	public static final function checkAllAt( $x, $y )
	{
		foreach (self::$clog as $key => $obj)
		{
			if ($obj->checkCollisionAt($x, $y) !== -1)
				return $obj;
		}
		return -1;
	}
}
?>