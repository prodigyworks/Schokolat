<?php
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
	}
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 450;
	$crud->title = "Product";
	$crud->table = "{$_SESSION['DB_PREFIX']}product";
	$crud->sql = "SELECT A.*
				  FROM  {$_SESSION['DB_PREFIX']}product A
				  ORDER BY A.name";
	$crud->columns = array(
			array(
				'name'       => 'id',
				'viewname'   => 'uniqueid',
				'length' 	 => 6,
				'showInView' => false,
				'filter'	 => false,
				'bind' 	 	 => false,
				'editable' 	 => false,
				'pk'		 => true,
				'label' 	 => 'ID'
			),
			array(
				'name'       => 'name',
				'length' 	 => 50,
				'label' 	 => 'Name'
			),
			array(
				'name'       => 'productid',
				'length' 	 => 20,
				'label' 	 => 'Barcode'
			),
			array(
				'name'       => 'retailprice',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Retail Price'
			)
		);
		
	$crud->run();
?>
