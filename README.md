editablegrid-mysql-example
==========================

This example shows how to use EditableGrid with a MySQL database

## Installation
First, create a database and load the script **demo.sql**


	shell> mysql -u root -ppassword
	mysql> create database demo; use demo; source demo.sql
	
Then copy **config.php.sample** to **config.php** and edit the values with yours.

	$config = array(
	"db_name" => "",
	"db_user" => "",
	"db_password" => "",
	"db_host" => "localhost"
	);   
	
You can load index.php in a browser and you should see the content of the table demo.