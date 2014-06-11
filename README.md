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

## Filter
### Client side
It's very easy to filter the content of the table. You can use

	EditableGrid.prototype.filter = function(filterString, cols)
	
In this exemple, the filter operates on all columns 
	
	datagrid.editableGrid.filter( $(this).val());

but you can filter only on some columns. Below, the filter operates on columns 0, 3 and 5. 

	datagrid.editableGrid.filter( $(this).val(), [0,3,5]);
	
