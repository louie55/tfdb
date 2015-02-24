<?php
//CONNECT TO THE DATABASE

require_once "ez_sql_core.php";
require_once "ez_sql_pdo.php";

//CHANGE BELOW LINE IF MYSQL DATABASE LOGIN INFO CHANGES.

//ORDER OF ITEMS: (USER,PASS,DB NAME,SERVER)


//Connect to the database
//$db = new ezSQL_mysql('transformers','transformers','transformers','grintfar.ipowermysql.com');
$db = new ezSQL_pdo('mysql:host=grintfar.ipowermysql.com;dbname=transformers', 'transformers', 'transformers');

?>