<?php
abstract class AbstractDataTableParam {
	public $draw;
	public $start = 0;
	public $length = 10;
	public $search = array("value"=>"", "regex"=>"");
	public $columns = array(0=>array("data"=>0, "name"=>"", "searchable"=>true, "orderable"=>true, "search"=>array("value"=>"", "regex"=>"")));
	public $order = array(0=>array("column"=>0, "dir"=>"asc"));
}