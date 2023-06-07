<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

class Entity {
	
	function __construct() {
		/* empty default value of audit columns */
		$keys = preg_grep('/(created|creator|modified|modifier)$/i', array_keys((array)$this));
		array_walk($keys, function($key) {
			$this->{$key} = null;
		});
	}

	/**
	 * Bind values from a source array.
	 * @param array $array Key-value pair where the keys match entity's properties.
	 * @param array $exclude List of properties to exclude.
	 */
	public function bind($array, array $exclude = array()) {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $val) {
			if (in_array($key, $exclude)) continue;
			$this->{$key} = ArrayHelper::getValue((array)$array, $key, $this->{$key});
		}
	}
	
	/**
	 * Clone properties and values of another entity to this entity.
	 * @param Entity $entity Source entity to clone from.
	 * @param string|null $operator Define how to filter properties of source entity. Valid values are `include` and `exclude`. `null` to disable filter.
	 * @param array $props List of property to apply to filter.
	 */
	public function extend($entity, $operator = null, $props = array()) {
		$vars = get_object_vars($entity);
		$filter = array();
		foreach ($props as $val) {
			$filter[$val] = '';
		}
		switch ($operator) {
			case 1:
			case 'include':
				$vars = array_intersect_key($vars, $filter);
				break;
			case -1;
			case 'exclude':
				$vars = array_diff_key($vars, $filter);
				break;
		}
		foreach ($vars as $key => $val) {
			$this->{$key} = $entity->{$key};
		}
	}
	
	/**
	 * Set value of properties with value equals to empty string to `null`
	 */
	public function clean() {
		foreach ((array)$this as $key => $value) {
			if ($value === '') {
				$this->$key = null;
			}
		}
	}
	
	/**
	 * Update audit columns with the given user ID.
	 * @param int $userId ID of the user manipulating the entity.
	 */
	public function writeLog($userId) {
		$keys = array_values(preg_grep('/created$/i', array_keys((array)$this)));
		if (count($keys) == 0) return;
		$utc = DateHelper::getUtcTime();
		if ($this->{$keys[0]} == null) {
			foreach (preg_grep('/created$/i', array_keys((array)$this)) as $prop) {
				$this->$prop = $utc;
			}
			foreach (preg_grep('/creator$/i', array_keys((array)$this)) as $prop) {
				$this->$prop = $userId;
			}
		} else {
			foreach (preg_grep('/modified$/i', array_keys((array)$this)) as $prop) {
				$this->$prop = $utc;
			}
			foreach (preg_grep('/modifier$/i', array_keys((array)$this)) as $prop) {
				$this->$prop = $userId;
			}
		}
	}
	
	/**
	 * Insert, update or replace an entity to database.
	 * @param string|null $pk Primary key of the entity. If provided, `INSERT` or `UPDATE` statment will be used, `REPLACE` statement otherwise.
	 * @param array $encrypted List of encrypted columns.
	 * @param boolean $debug Whether to enable debug logging.
	 * @return int|boolean Number of records updated, `false` if operation failed.
	 */
	public function insertOrUpdate($pk = null, $encrypted = array(), $debug = false) {
		$entityClass = get_class($this);
		$entity = new $entityClass();
		$entity->bind((array)$this);
		$entity->clean();
		$sqlOpts = array(
			'table' => preg_replace('/Entity$/i', '', strtolower($entityClass)),
			'column' => array_keys((array)$entity),
			'encrypted' => $encrypted,
		);
		if (is_array($pk)) {
			$pk = ArrayHelper::getValue(array_values($pk), 0);
		}
		if ($pk != null) {
			$sqlOpts['column'] = array_diff($sqlOpts['column'], array($pk));
			if ($entity->$pk === null) {
				$sql = Db::sqlBuilder('insert', $sqlOpts);
				$sqlParam = $entity->prepareSqlParam(array($pk));
			} else {
				$sqlOpts['where'] = array("`{$pk}` = :{$pk}");
				$sql = Db::sqlBuilder('update', $sqlOpts);
				$sqlParam = $entity->prepareSqlParam();
			}
		} else {
			$sql = Db::sqlBuilder('replace', $sqlOpts);
			$sqlParam = $entity->prepareSqlParam();
		}
		$result = Db::update($sql, $sqlParam, $debug);
		if ($pk != null && $this->$pk == null) {
			$this->$pk = Db::getLastInsertId();
		}
		return($result);
	}
	
	public function prepareSqlParam(array $exclude = array()) {
		$array = array();
		foreach ((array)$this as $prop => $value) {
			if (in_array($prop, $exclude)) continue;
			$array[":$prop"] = $value;
		}
		return($array);
	}
}
?>
