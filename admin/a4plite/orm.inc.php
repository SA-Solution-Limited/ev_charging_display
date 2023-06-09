<?php

class orm
{
	private static function getParam($comment, $word)
	{
		$params = array();
		foreach (explode("\n", $comment) as $line) {
			if (preg_match('/\*\s+@' . $word . '\s+(.[^\s]+)/', trim($line), $match))
				return $match[1];
		}
		return null;
	}

	private static function getEntity($obj)
	{
		$reflect = new ReflectionClass($obj);
		$comment = $reflect->getDocComment();

		$table = self::getParam($comment, "table");
		
		$primarykey = null;

		$columns = array();
		$fks = array();
		$props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
		foreach ($props as $prop) {
			$comment = $prop->getDocComment();
			$column = self::getParam($comment, "column");
			if ($column != null) {
				$columns[$column] = $prop->getName();
				continue;
			}
			$fk = self::getParam($comment, "fk");
			if ($fk != null) {
				$fks[$fk] = $prop->getName();
				continue;
			}
			$column = self::getParam($comment, "id");
			if ($column != null) {
				$primarykey = $column;
				$columns[$column] = $prop->getName();
				continue;
			}
		}

		return array("table" => $table, "primarykey" => $primarykey, "columns" => $columns, "fks" => $fks);
	}

	private static function bind($row, $entity, $obj)
	{
		foreach ($entity["columns"] as $column => $attr) {
			if (property_exists($obj, $attr))
			    $obj->{$attr} = $row[$column];
		}
		foreach ($entity["fks"] as $fk => $attr) {
			if (property_exists($obj, $attr)) {
				$loader = new orm_Loader();
				$loader->obj = $obj;
				$loader->attr = $attr;
				$loader->pk_column = $entity["primarykey"];
				$arr = explode(".", $fk);
				$loader->fk_table = $arr[0];
				$loader->fk_column = $arr[1];
				$obj->{$attr} = $loader;
			}
		}
		$obj->_new = false;
		return $obj;
	}

	public static function findById($obj, $id)
	{
		$entity = self::getEntity($obj);
		$row = db::select("*")
				->from($entity["table"])
				->where($entity["primarykey"] . " = :id")
				->fetchOneRow(array(":id" => $id));
		if ($row == null)
			return null;
		else
			return self::bind($row, $entity, new $obj());
	}

	public static function findAll($obj, $filter = "", $param = array())
	{
		$entity = self::getEntity($obj);
		$rows = db::select("*")
				->from($entity["table"])
				->where($filter)
				->fetchAll($param);
		$result = array();
		foreach ($rows as $row)
			$result[] = self::bind($row, $entity, new $obj());
		return $result;
	}

	public static function findFirst($obj, $filter = "", $param = array())
	{
		$entity = self::getEntity($obj);
		$row = db::select("*")
				->from($entity["table"])
				->where($filter)
				->fetchOneRow($param);
		if ($row == null)
			return null;
		else
			return self::bind($row, $entity, new $obj());
	}

	public static function insert($obj, $value)
	{
		$entity = self::getEntity($obj);
		$query = db::insert()->into($entity["table"]);
		$binding = array();
		foreach ($entity["columns"] as $column => $attr) {
			if ($column == $entity["primarykey"])
				continue;
			$query->values($column, ":" . $attr);
			$binding[":" . $attr] = $value->{$attr};
		}
		if ($entity["primarykey"] != null)
		    $value->{$entity['columns'][$entity["primarykey"]]} = $query->execute($binding);
		$value->_new = false;
		return $value->{$entity['columns'][$entity["primarykey"]]};
	}

	public static function delete($obj, $value)
	{
		$entity = self::getEntity($obj);
		return db::delete()
		->from($entity["table"])
		->where($entity["primarykey"] . " = :id")
		->execute(array(":id" => $value->{$entity['columns'][$entity["primarykey"]]}));
	}

	public static function update($obj, $value)
	{
		$entity = self::getEntity($obj);
		$query = db::update($entity["table"]);
		$binding = array();
		foreach ($entity["columns"] as $column => $attr) {
			if ($column == $entity["primarykey"]) {
				$pk = $attr;
				continue;
			}
			$query->set($column, ":" . $attr);
			$binding[":" . $attr] = $value->{$attr};
		}
		$binding[":id"] = $value->{$pk};
		return $query->where($entity["primarykey"] . " = :id")->execute($binding);
	}
}

class orm_loader
{
	public $obj;
	public $attr;
	public $pk_column;
	public $fk_table;
	public $fk_column;

	private static function canonize($name, $upper = false) {
		$canonize = "";
		$arr = str_split($name);
		foreach ($arr as $c) {
			if ($c == "_")
				$upper = true;
			else {
				$canonize .= $upper ? strtoupper($c) : strtolower($c);
				$upper = false;
			}
		}
		return $canonize;
	}

	public function load($val = null)
	{
		$obj = $this->obj;
		$attr = $this->attr; 
		$pk_column = self::canonize($this->pk_column);
		$obj->$attr = orm::findAll(self::canonize($this->fk_table, true), $this->fk_column . " = :id", array(":id" => $val == null ? $obj->{$pk_column} : $val));
	}
}