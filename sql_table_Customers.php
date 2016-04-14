<?php
	/*
		Defines constants for the Customers table.
	*/
	class Customers
	{
		const NAME = 'Customers';
		// Can't declare arrays as const in the PHP version used on Mercury - using const syntax to incicate the value should be treated as such
		public static $STRUCTURE = array(
			'email VARCHAR(32) PRIMARY KEY NOT NULL',
			'password VARCHAR(32) NOT NULL',
			'name VARCHAR(64) NOT NULL',
			'phone VARCHAR(10) NOT NULL'
		);
	}
?>