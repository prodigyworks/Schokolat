<?php
	require_once("crud.php");
	
	class CustomerCrud extends Crud {
		
		
		public function postScriptEvent() {
?>
	
			/* Derived  address callback. */
			function fullAddress(node) {
				var address = "";
				
				if ((node.street) != "") {
					address = address + node.street;
				} 
				
				if ((node.town) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.town;
				} 
				
				if ((node.city) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.city;
				} 
				
				if ((node.city) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.city;
				} 
				
				if ((node.county) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.county;
				} 
				
				if ((node.postcode) != "") {
					if (address != "") {
						address = address + ", ";
					}
					
					address = address + node.postcode;
				} 
				
				return address;
			}
<?php			
		}
	}
	
	$crud = new CustomerCrud();
	$crud->dialogwidth = 650;
	$crud->title = "Events";
	$crud->table = "{$_SESSION['DB_PREFIX']}event";
	$crud->sql = "SELECT A.*
				  FROM  {$_SESSION['DB_PREFIX']}event A
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
				'name'       => 'shortname',
				'length' 	 => 10,
				'label' 	 => 'Short Name'
			),
			array(
				'name'       => 'imageid',
				'type'		 => 'IMAGE',
				'required'   => false,
				'length' 	 => 35,
				'showInView' => false,
				'label' 	 => 'Logo'
			),			
			array(
				'name'       => 'firstname',
				'length' 	 => 15,
				'label' 	 => 'First Name'
			),			
			array(
				'name'       => 'lastname',
				'length' 	 => 15,
				'label' 	 => 'Last Name'
			),			
			array(
				'name'       => 'street',
				'length' 	 => 60,
				'showInView' => false,
				'label' 	 => 'Street'
			),
			array(
				'name'       => 'town',
				'length' 	 => 60,
				'showInView' => false,
				'required'	 => false,
				'label' 	 => 'Town'
			),
			array(
				'name'       => 'city',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'City'
			),
			array(
				'name'       => 'county',
				'length' 	 => 30,
				'showInView' => false,
				'label' 	 => 'County'
			),
			array(
				'name'       => 'postcode',
				'length' 	 => 10,
				'showInView' => false,
				'label' 	 => 'Post Code'
			),
			array(
				'name'       => 'address',
				'length' 	 => 70,
				'editable'   => false,
				'bind'		 => false,
				'type'		 => 'DERIVED',
				'function'	 => 'fullAddress',
				'label' 	 => 'Address'
			),
			array(
				'name'       => 'email',
				'length' 	 => 40,
				'label' 	 => 'Email'
			),
			array(
				'name'       => 'telephone',
				'length' 	 => 12,
				'label' 	 => 'Telephone'
			)
		);
		
	$crud->subapplications = array(
			array(
				'title'		  => 'Products',
				'imageurl'	  => 'images/minimize.gif',
				'application' => 'eventproducts.php'
			),
			array(
				'title'		  => 'Forecasts',
				'imageurl'	  => 'images/minimize.gif',
				'application' => 'eventforecast.php'
			)
		);
		
	$crud->run();
?>
