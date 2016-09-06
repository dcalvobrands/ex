IMPORTANT:

	- In order to run the exercise you may need to change the base route in config.php
	
	- The coding test done was:  1-discounts
	
Notes:

	- It is assumed that discounts are acomulable
	
	- Data from customers and products is directly read from JSON
	
	- It is assumed that the cheapest product is the one with lower total (not the cheapest one per unit) "If you buy two or more products of category "Tools" (id 1), you get a 20% discount on the cheapest product."
	
	- index.php allows testing of a given order (PHP Array)
	
	- functions.php includes al the logic
	
	- tpl.php is a small template for the test
	
	- data/discounts.json contains the discount logic and allows to add more discounts of the same types (new types would require additional development)
	
	- service.php recives the post and applies the discounts on the order, returning JSON order with discounts applied and explained