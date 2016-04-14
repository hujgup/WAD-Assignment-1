<?php
	/*
		COS30030 Web Application Development - Assignment 1
		Author: Jake Tunaley (Student I.D. 100593584)

		Purpose: Defines a MySQL table for Bookings.
	*/

	/*
		Defines constants for the Bookings table.
	*/
	class Bookings
	{
		const NAME = 'Bookings';
		// Can't declare arrays as const in the PHP version used on Mercury - using const syntax to incicate the value should be treated as such
		public static $STRUCTURE = array(
			'referenceNumber INT NOT NULL AUTO_INCREMENT',
			'email VARCHAR(32) NOT NULL',
			'name VARCHAR(64) NOT NULL',
			'phone VARCHAR(10) NOT NULL',
			'unit INT',
			'streetNum INT NOT NULL',
			'streetName VARCHAR(32) NOT NULL',
			'suburb VARCHAR(32) NOT NULL',
			'destination VARCHAR(32) NOT NULL',
			'pickupDate CHAR(10) NOT NULL',
			'pickupTime CHAR(5) NOT NULL',
			"status VARCHAR(10) NOT NULL DEFAULT 'unassigned'",
			'PRIMARY KEY (referenceNumber)',
			'FOREIGN KEY (email) REFERENCES Customers(email)'
		);
	}
?>