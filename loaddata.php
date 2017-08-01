<?php     


/*
 * examples/mysql/loaddata.php
 * 
 * This file is part of EditableGrid.
 * http://editablegrid.net
 *
 * Copyright (c) 2011 Webismymind SPRL
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://editablegrid.net/license
 */
                              


/**
 * This script loads data from the database and returns it to the js
 *
 */
       
require_once('config.php');      
require_once('EditableGrid.php');            

/**
 * fetch_pairs is a simple method that transforms a mysqli_result object in an array.
 * It will be used to generate possible values for some columns.
*/
function fetch_pairs($mysqli,$query){
	if (!($res = $mysqli->query($query)))return FALSE;
	$rows = array();
	while ($row = $res->fetch_assoc()) {
		$first = true;
		$key = $value = null;
		foreach ($row as $val) {
			if ($first) { $key = $val; $first = false; }
			else { $value = $val; break; } 
		}
		$rows[$key] = $value;
	}
	return $rows;
}


// Database connection
$mysqli = mysqli_init();
$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
$mysqli->real_connect($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']); 
                    
// create a new EditableGrid object
$grid = new EditableGrid();

/* 
*  Add columns. The first argument of addColumn is the name of the field in the databse. 
*  The second argument is the label that will be displayed in the header
*/
$grid->addColumn('id', 'ID', 'integer', NULL, false); 
$grid->addColumn('name', 'Name', 'string');  
$grid->addColumn('firstname', 'Firstname', 'string');  
$grid->addColumn('age', 'Age', 'integer');  
$grid->addColumn('height', 'Height', 'float');  
/* The column id_country and id_continent will show a list of all available countries and continents. So, we select all rows from the tables */
$grid->addColumn('id_continent', 'Continent', 'string' , fetch_pairs($mysqli,'SELECT id, name FROM continent'),true);  
$grid->addColumn('id_country', 'Country', 'string', fetch_pairs($mysqli,'SELECT id, name FROM country'),true );  
$grid->addColumn('email', 'Email', 'email');                                               
$grid->addColumn('freelance', 'Freelance', 'boolean');  
$grid->addColumn('lastvisit', 'Lastvisit', 'date');  
$grid->addColumn('website', 'Website', 'string');  
$grid->addColumn('action', 'Action', 'html', NULL, false, 'id');  

$mydb_tablename = (isset($_GET['db_tablename'])) ? stripslashes($_GET['db_tablename']) : 'demo';


error_log(print_r($_GET,true));

$query = 'SELECT *, date_format(lastvisit, "%d/%m/%Y") as lastvisit FROM '.$mydb_tablename ;
$queryCount = 'SELECT count(id) as nb FROM '.$mydb_tablename;

$totalUnfiltered =$mysqli->query($queryCount)->fetch_row()[0];
$total = $totalUnfiltered;


/* SERVER SIDE */
/* If you have set serverSide : true in your Javascript code, $_GET contains 3 additionnal parameters : page, filter, sort
 * this parameters allow you to adapt your query  
 *
 */
$page=0;
if ( isset($_GET['page']) && is_numeric($_GET['page'])  )
  $page =  (int) $_GET['page'];


$rowByPage=50;

$from= ($page-1) * $rowByPage;

if ( isset($_GET['filter']) && $_GET['filter'] != "" ) {
  $filter =  $_GET['filter'];
  $query .= '  WHERE name like "%'.$filter.'%" OR firstname like "%'.$filter.'%"';
  $queryCount .= '  WHERE name like "%'.$filter.'%" OR firstname like "%'.$filter.'%"';
  $total =$mysqli->query($queryCount)->fetch_row()[0];
}

if ( isset($_GET['sort']) && $_GET['sort'] != "" )
  $query .= " ORDER BY " . $_GET['sort'] . (  $_GET['asc'] == "0" ? " DESC " : "" );


$query .= " LIMIT ". $from. ", ". $rowByPage;

error_log("pageCount = " . ceil($total/$rowByPage));
error_log("total = " .$total);
error_log("totalUnfiltered = " .$totalUnfiltered);

$grid->setPaginator(ceil($total/$rowByPage), (int) $total, (int) $totalUnfiltered, null);

/* END SERVER SIDE */ 

error_log($query);

                                                                       
$result = $mysqli->query($query );





$mysqli->close();





// send data to the browser

$grid->renderJSON($result,false, false, !isset($_GET['data_only']));

