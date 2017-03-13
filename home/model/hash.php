<?php
	function user_pass($password)
	{
		return hash('sha256', hash('snefru', '^&fsd+&' . $password) . 'gfd765-+');
	}
?>