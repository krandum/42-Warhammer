<?php

function send_data( $data )
{
	file_put_contents($_SESSION['log'], $data);
	file_put_contents("out.txt", file_get_contents("out.txt").$data);
}

function data_init()
{
	$_SESSION['last_data'] = unserialize(file_get_contents($_SESSION['log']));
}

function get_data()
{
	while (1)
	{
		$data = unserialize(file_get_contents($_SESSION['log']));
		if ($_SESSION['last_data'] !== $data)
		{
			$_SESSION['last_data'] = $data;
			return $data;
		}
	}
}

?>