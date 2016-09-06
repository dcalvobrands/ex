<?php
	include 'config.php';
	function httpPost($url, $data)
{
    $curl = curl_init(BASE_URL.$url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


function checkDiscounts($order){
	$discounts = json_decode(file_get_contents(dirname(__FILE__).'/data/discounts.json'),true);
	foreach($discounts as $discount){
		$order = applyDiscount($order, $discount);
	}
	return array("order" => $order);
}
function applyDiscount( $order, $discount ){
	if($discount['type']== 'full_order'){
		$order = checkFullOrderDiscount($order, $discount);
	}else if($discount['type']== 'cheapest'){
		$order = checkCheapestDiscount($order, $discount);
	}else if($discount['type']== 'extra_units'){
		$order = checkExtraDiscount($order, $discount);
	}
	return $order;
}
function customerRevenueHigherThan( $customer_id, $revenue ){
	$customers = json_decode(file_get_contents(dirname(__FILE__).'/data/customers.json'),true);
	foreach($customers as $customer){
		if($customer['id'] == $customer_id){
			if($customer['revenue'] > $revenue){
				return true;
			}else{
				return false;
			}
		}
	}
}
function checkFullOrderDiscount($order, $discount){
	if(customerRevenueHigherThan($order['customer_id'], $discount['min_revenue'])){
		for($i = 0; $i < count($order['items']); $i++){
			$order['items'][$i]['unit-price'] = (float)$order['items'][$i]['unit-price'] * (100 - $discount['discount']) /100;
			$order['items'][$i]['total'] = (float)$order['items'][$i]['total'] * (100 - $discount['discount']) /100;
		}
		$order['total'] = (float)$order['total'] * (100 - $discount['discount']) /100;
		$order['discounts'][] = $discount['description'];
		
	}
	return $order;
}
function checkCheapestDiscount($order, $discount){
	$category_items = array();
	$category = $discount['category'];
	$products = json_decode(file_get_contents(dirname(__FILE__).'/data/products.json'),true);
	for($i = 0; $i < count($order['items']); $i++){
		for($x = 0; $x < count($products); $x++){
			if($order['items'][$i]['product-id'] == $products[$x]['id']){
				if($products[$x]['category'] == $category){
					$category_items[] = $i;
				}
			}
		}
	}
	if(count($category_items)>1){
		$cheapest_id = $category_items[0];
		$cheapest_price = $order['items'][$category_items[0]]['total'];
		for($i = 0; $i < count($category_items);$i++){
			if($order['items'][$category_items[$i]]['total'] < $cheapest_price){
				$cheapest_price = $order['items'][$category_items[$i]]['total'];
				$cheapest_id = $category_items[$i];
			}
		}
		$price = (float)$order['items'][$cheapest_id]['total'];
		$order['items'][$cheapest_id]['unit-price'] = (float)$order['items'][$cheapest_id]['unit-price'] * (100 - $discount['discount']) /100;
		$order['items'][$cheapest_id]['total'] = (float)$order['items'][$cheapest_id]['total'] * (100 - $discount['discount']) /100;
		$difference = $price - $order['items'][$cheapest_id]['total'];
		$order['total'] = (float)$order['total'] - $difference;
		$order['discounts'][] = str_replace('%product%', $order['items'][$cheapest_id]['product-id'], $discount['description']) ;
	}
	return $order;
}
function checkExtraDiscount($order, $discount){
	$category = $discount['category'];
	$products = json_decode(file_get_contents(dirname(__FILE__).'/data/products.json'),true);
	$free_items = [];
	for($i = 0; $i < count($order['items']); $i++){
		for($x = 0; $x < count($products); $x++){
			if($order['items'][$i]['product-id'] == $products[$x]['id']){
				if($products[$x]['category'] == $category){
					$quantity = (int)$order['items'][$i]['quantity'];
					if($quantity >= $discount['units']){
						$free_items_count = floor($quantity / $discount['units']) * $discount['extra_units'];
						$item = $order['items'][$i];
						$item['total']= 0;
						$item['unit-price']= 0;
						$item['quantity']= $free_items_count;
						$free_items[] = $item;
						$order['discounts'][] = str_replace(array('%free_units%', '%product%'), array($free_items_count, $products[$x]['id']), $discount['description']);
					}
					
				}
				break;
			}
		}
	}
	$order['free_items'] = $free_items;
	return  $order;
}