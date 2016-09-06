<?php
	error_reporting(E_ALL);
	include 'functions.php';
	if(isset($_POST['order'])){
		$order = $_POST['order'];
		print json_encode(checkDiscounts($order));
	}else{
		header('HTTP/1.0 404 No order was sent', true, 400);
		print json_encode(array('status' => 400, 'error' => 'No order was sent'));
	}
?>