<?php
	$bookingsName = "Bookings";
	$bookingsStructure = array(
		"referenceNumber INT NOT NULL AUTO_INCREMENT",
		"email VARCHAR(32) NOT NULL",
		"name VARCHAR(64) NOT NULL",
		"phone VARCHAR(10) NOT NULL",
		"unit INT",
		"streetNum INT NOT NULL",
		"streetName VARCHAR(32) NOT NULL",
		"suburb VARCHAR(32) NOT NULL",
		"destination VARCHAR(32) NOT NULL",
		"pickupDate CHAR(10) NOT NULL",
		"pickupTime CHAR(5) NOT NULL",
		"status VARCHAR(10) NOT NULL DEFAULT 'unassigned'",
		"PRIMARY KEY (referenceNumber)",
		"FOREIGN KEY (email) REFERENCES Customers(email)"
	);
?>