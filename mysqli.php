<?php
						DEFINE ('DB_USER', 'name_of_user');
						DEFINE ('DB_PASSWORD', 'password');
						DEFINE ('DB_HOST', 'localhost');
						DEFINE ('DB_NAME', 'name_of_database'); 
						$mysqli = new MySQLi(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
						$mysqli->set_charset('utf-8');
?>						
