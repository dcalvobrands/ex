<?php
	error_reporting(E_ALL);
	if(isset($_POST['submit-demo'])){
		include 'functions.php';
		$demo_order = array(
			"id" => "1",
			"customer_id" => "2",
			"items" => array(
				array(
					"product-id" => "B102",
					"quantity" =>"10",
					"unit-price" => "4.99",
					"total" => "49.90"
				),
				array(
					"product-id" => "B101",
					"quantity" =>"15",
					"unit-price" => "4.99",
					"total" => "24.95"
				),
				array(
					"product-id" => "A101",
					"quantity" =>"2",
					"unit-price" => "9.75",
					"total" => "19.50"
				),
				array(
					"product-id" => "A102",
					"quantity" =>"1",
					"unit-price" => "49.50",
					"total" => "49.50"
				)
					
			),
			"total" => "149.30"
		);
		print httpPost('service.php', array("order" => $demo_order));
	}else{
		include'tpl.html';
	}
	
	
		
?>
