<?php
	require('func.make_query.php');
	mysql_connect('localhost','root','');
	mysql_select_db('test');
	$array1=array('username'=>'goro','password'=>'test');
	$array2=array('id'=>'3');
	make_query(true,'delete','accounts',$array1);
?>
