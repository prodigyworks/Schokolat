<?php
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
		
		public function postScriptEvent() {
?>
	
<?php			
		}
	}
	
	$eventid = $_GET['id'];
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 450;
	$crud->title = "Events";
	$crud->table = "{$_SESSION['DB_PREFIX']}eventproductmatrix";
	$crud->sql = "SELECT A.*, C.name AS productname, B.name AS eventname
				  FROM  {$_SESSION['DB_PREFIX']}eventproductmatrix A
				  INNER JOIN  {$_SESSION['DB_PREFIX']}event B
				  ON B.id = A.eventid
				  INNER JOIN  {$_SESSION['DB_PREFIX']}product C
				  ON C.id = A.productid
				  WHERE A.eventid = $eventid
				  ORDER BY C.name";
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
				'name'       => 'eventid',
				'type'       => 'DATACOMBO',
				'default'	 => $eventid,
				'length' 	 => 30,
				'editable'	 => false,
				'label' 	 => 'Event',
				'table'		 => 'event',
				'table_id'	 => 'id',
				'alias'		 => 'eventname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'productid',
				'type'       => 'DATACOMBO',
				'length' 	 => 30,
				'label' 	 => 'Product',
				'table'		 => 'product',
				'table_id'	 => 'id',
				'alias'		 => 'productname',
				'table_name' => 'name'
			),
			array(
				'name'       => 'stock',
				'length' 	 => 12,
				'align'		 => 'right',
				'label' 	 => 'Stock Level'
			)
		);
		
	$crud->run();
?>
