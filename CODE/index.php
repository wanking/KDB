<?php
header("Content-type:text/html;charset=utf-8");
include("KDB.php");

/* $db1 = new KDB('admin');
if($db1->conn("localhost","root","")){echo 'database connected';}else{echo 'cann\'t connenct to server';}
$db1->select_db('wx');
$user = array(
				array('username'=>'kdb','password'=>'xxx'),
				array('username'=>'kdb2','password'=>'xxx2')
			  );
$update = array('username'=>'xxxx');
if($db1->update($update,'id=32')) echo '更新成功'; */


try {
	$db2 = new KDB('admin');
	
	print_r($db2);
	echo PHP_EOL;
	print_r($db2->getOneById(1));
	echo PHP_EOL;
	echo '数据库input中的app表';
	echo PHP_EOL;
	$db3 = new KDB('cniyv_admin','ptscniyvone1009','115.29.11.40','1234','towebappptstowebapppts');
	echo PHP_EOL;
	print_r($db3);
	echo PHP_EOL;
	print_r($db3->getOne("ptsid=1"));
	print_r($db2->getOneById(1));
	$db2->switchTable("app");
	$db2->switchDb("input");
	print_r($db2);
	print_r($db2->getOneById(1));

} catch (Exception $e) {
	echo $e->getMessage();
}
?>