<?php

require_once "config.inc.php";
require_once "db.inc.php";

header("Content-Type: text/plain");

$tables = db::select("TABLE_NAME")
			->from("INFORMATION_SCHEMA.TABLES")
			->where("TABLE_TYPE = 'BASE TABLE'")
			->orderby("TABLE_NAME")
			->fetchAll();
?>
USE [fdim_hk]
GO


<?php
foreach ($tables as $table) {

$cols = db::select("COLUMN_NAME")
		->from("INFORMATION_SCHEMA.COLUMNS")
		->where("TABLE_NAME = :table")
		->fetchAll(array(":table" => $table));
?>
------ [fdim_hk].[dbo].[<?= $table ?>] ------

SET IDENTITY_INSERT [fdim_hk].[dbo].[<?= $table ?>] ON
GO

INSERT INTO [fdim_hk].[dbo].[<?= $table ?>] (<?php 
	$comma = "\r\n";
	foreach ($cols as $col) {
		echo $comma . "[$col]";
		$comma = ",\r\n";
	}
?>

) 

SELECT <?php 
	$comma = "\r\n";
	foreach ($cols as $col) {
		echo $comma . "[$col]";
		$comma = ",\r\n";
	}
?>

FROM [fdim_hk_bak].[dbo].[<?= $table ?>]
GO

SET IDENTITY_INSERT [fdim_hk].[dbo].[<?= $table ?>] OFF
GO


<?php
}
