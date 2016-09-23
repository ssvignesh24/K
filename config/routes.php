<?php

	Route::post("subscribe","Rog=>subscribe",Validation::test(array(
			"email" => "email"
		)));


	// Shop
	Route::post("signup", "Rog=>shop_register",Validation::test(array(
			"name" => "required",
			"title" => "required",
			"shop_type" => "in:1,2",
			"s_started" => "date",
			"s_city" => "required",
			"s_state" => "required",
			"b_name" => "required",
			"b_acc" => "minlength:10",
			"b_branch" => "required"
		)));
	Route::post("product","Shop=>create_product:filter",Validation::test(array(
			"type" => "in:Jewels,Painting & Art,Printing,Digital content,Bags & Purses,Shoes,Gifts",
			"sub_type" => "required",
			"category" => "required",
			"gender" => "in:male,female,both",
			"custom_packing" => "boolean",
			"overview" => "required",
			"description" => "required",
			"tags" => "required",
			"days" => "int",
			"price" => "int",
			"title" => "required"
		)));

	

	Route::post("follow_shop","Shop=>follow_shop",Validation::test(array(
			"shop" => "required"
		)));

	Route::post("unfollow_shop","Shop=>unfollow_shop",Validation::test(array(
			"shop" => "required"
		)));
	Route::get("product","Shop=>new_product:filter");
	
	Route::get("product/:product_id","Product=>product");

	Route::get("search","Product=>search", Validation::test(array(
			"key" => "required"
		)));
	Route::post("search","Product=>search_json", Validation::test(array(
			"key" => "required"
		)));

	Route::get("select/:shop","Shop=>change_shop:filter");
	Route::get("shops","Shop=>list_shops:filter");
	Route::get("shop_script","Shop=>script");
	Route::get(":shop","Shop=>home");
	Route::get(":shop/followers","Shop=>list_followers");

	// Cart
	Route::post("additem","Cart=>add_item",Validation::test(array(
			"product_id" => "required"
		)));
	Route::post("removeitem","Cart=>remove_item",Validation::test(array(
			"product_id" => "required"
		)));
	Route::get("cart","Cart=>show_cart");
	Route::post("cart","Cart=>cart_products");
	Route::get("coupon","Cart=>validate_coupon",Validation::test(array(
			"coupon" => "required|minlength:5"
		)));

	// Customer
	Route::post("register","Rog=>customer_register",Validation::test(array(
			"name" => "required",
			"email" => "email",
			"mobile" => "minlength:10",
			"password" => "minlength:8"
 		)));

	// Login
	Route::post("login","Rog=>login",Validation::test(array(
			"username" => "required",
			"password" => "minlength:8"
		)));
	Route::get("login","Rog=>login_page");

	// Admin URL
	Route::get("admin","Admin=>home");
	Route::get("admin/products","Admin=>products:filter");
	Route::get("admin/approvals","Admin=>approvals:filter");
	Route::get("admin/customers","Admin=>customers:filter");
	Route::get("admin/shops","Admin=>shops:filter");
	Route::get("admin/dash","Admin=>dash:filter");
	Route::get("admin/sales","Admin=>sales:filter");
	Route::get("admin/shipping","Admin=>shipping:filter");
	Route::get("admin/payments","Admin=>payments:filter");
	Route::get("admin/requests","Admin=>requests:filter");
	Route::get("admin/coupons","Admin=>coupons:filter");
	Route::get("admin/rog","Admin=>rog:filter");
	Route::get("admin/product","Admin=>view_product:filter",Validation::test(array(
			"proid" => "required"
		)));

	// Admin AJAX
	Route::post("admin/product_by_shop","Admin=>product_by_shop:filter",Validation::test(array(
			"q" => "required"
		)));
	Route::post("admin/product_by_title","Admin=>product_by_title:filter",Validation::test(array(
			"q" => "required"
		)));
	Route::post("admin/product_by_id","Admin=>product_by_id:filter",Validation::test(array(
			"q" => "required"
		)));
	Route::post("admin/approval_by_shop","Admin=>approval_by_shop:filter",Validation::test(array(
			"q" => "required"
		)));
	Route::post("admin/customer_by_name","Admin=>customer_by_name:filter",Validation::test(array(
			"q" => "required"
		)));

	Route::post("admin/customer_by_mobile","Admin=>customer_by_mobile:filter",Validation::test(array(
			"q" => "required"
		)));

	Route::post("admin/shop_by_title","Admin=>shop_by_title:filter",Validation::test(array(
			"q" => "required"
		)));
	Route::post("admin/shop_by_username","Admin=>shop_by_username:filter",Validation::test(array(
			"q" => "required"
		)));
	Route::post("admin/shop_by_owner","Admin=>shop_by_owner:filter",Validation::test(array(
			"q" => "required"
		)));
	// Admin creation

	Route::post("admin/create_coupons","Admin=>create_coupons",Validation::test(array(
			"coupon_code" => "required",
			"discount_price" => "int",
			"coupon_expires" => "date"
		)));
?>