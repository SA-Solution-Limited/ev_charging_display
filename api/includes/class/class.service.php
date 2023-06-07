<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

class Service {
	
	/**
	 * Return the last inserted ID from database.
	 * @return mixed ID
	 */
	public static function getLastInsertId() {
		return(Db::getLastInsertId());
	}
	
	/**
	 * Generate a unique ID for a database entry.
	 * @param string $table Name of the table where the entry will be inserted to.
	 * @param string $column Name of the column where the unique ID will be stored.
	 * @param int $length Length of the unique ID.
	 * @return string A unique ID.
	 */
	public static function generateUid($table, $column, $length = 8) {
		$char  = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789');
		$exist = true;
		while ($exist) {
			$uid = '';
			while (strlen($uid) < $length) {
				$uid .= $char[rand(0, count($char)-1)];
			}
			
			$sqlOpts = array(
				'table'  => $table,
				'column' => 'COUNT(1) AS "count"',
				'where'  => array('`'.$column.'` = :'.$column),
			);
			$sqlParam = array(':'.$column => $uid);
			
			$rs = Db::query(Db::sqlBuilder('select', $sqlOpts), $sqlParam);
			$exist = $rs[0]['count'] > 0;
		}
		return($uid);
	}
	
	/**
	 * Generate a list of availble filters based on properties of a given entity (i.e. table columns).
	 * @param Entity $entity Database entity.
	 * @return array Key-value pair where the keys refer to entity properties and values are set to `null`.
	 */
	protected static function generateEntityFilter($entity) {
		if ($entity == null) return(array());
		if (is_string($entity)) {
			$entity = new $entity();
		}
		$array = (array)$entity;
		foreach ($array as $col => $value) {
			if (preg_match('/(created|creator|modified|modifier)$/i', $col)) {
				unset($array[$col]);
				continue;
			}
			foreach (explode('|', '|_not|_like|_regexp|_max|_min') as $suffix) {
				$array[$col.$suffix] = null;
			}
		}
		return($array);
	}
	
	/**
	 * Merge lists of filters.
	 * @param array $args
	 * @return array Merged filters.
	 */
	protected static function prepareFilter() {
		$array = array(
			'keyword' => null,
		);
		$args = func_get_args();
		foreach ($args as $filter) {
			if (!is_array($filter)) continue;
			$array = array_replace($array, $filter);
		}
		return($array);
	}
	
	/**
	 * Inject filter to `$sqlOpts` and `$sqlParam` used for `Db::sqlBuilder` (i.e. add a `WHERE` clause to SQL query).
	 * @see Db::sqlbuilder()
	 * @param string $key Column name.
	 * @param mixed $value Value.
	 * @param array $sqlOpts Array of SQL builder options.
	 * @param array $sqlParam Key-value pair where the keys refer to tokens of SQL query and values refer to, value.
	 */
	public static function injectFilter($key, $value, array &$sqlOpts, array &$sqlParam, $encrypted = false) {
		// "in" and "not_in" is deprecated and replaced by intelligent guess of $value
		if ($key === null || $value === null || $value === '') return;
		if (!isset($sqlOpts['where']) || !is_array($sqlOpts['where'])) {
			$sqlOpts['where'] = array();
		}
		
		$column = preg_replace('/_(not|in|not_in|like|not_like|regexp|not_regexp|min|max)$/', '', $key); // derive column name
		$token  = ':'.(strpos($key, '.') !== false ? preg_replace('/.+?\./', '', $key) : $key); // remove table prefix, add colon prefix
		$isReverse = !!preg_match('/_not/', $token);
		
		if (preg_match('/_(max|min|like|regexp)$/', $token, $matches)) {
			$column = $encrypted ? "[decrypt:{$column}]" : "{$column}";
			switch ($matches[1]) {
				case 'like':
					$operator = ($isReverse ? 'NOT ' : '').'LIKE';
					$value = array_map(function($v) {
						return("%{$v}%");
					}, $value);
					break;
				case 'regexp':
					$operator = ($isReverse ? 'NOT ' : '').'REGEXP';
					break;
				case 'max':
					$operator = '<=';
					break;
				case 'min':
					$operator = '>=';
					break;
			}
			if (!is_array($value)) {
				$value = array($value);	
			}
			$whereClause = array_map(function($idx, $item) use ($column, $operator, $token, &$sqlParam) {
				$sqlParam["{$token}_{$idx}"] = $item;
				return("{$column} {$operator} {$token}_{$idx}");
			}, array_keys(array_values($value)), $value);
			if ($isReverse) {
				$sqlOpts['where'][] = '('.implode(' AND ', $whereClause).')';
			} else {
				$sqlOpts['where'][] = '('.implode(' OR ', $whereClause).')';
			}
			return;
		}
		
		if (is_array($value)) {
			$inClause = implode(', ', array_map(function($idx, $item) use ($token, $encrypted, &$sqlParam) {
				$sqlParam["{$token}_{$idx}"] = $item;
				return($encrypted ? "[encrypt:{$token}_{$idx}]" : "{$token}_{$idx}");
			}, array_keys(array_values($value)), $value));
			$operator = ($isReverse ? 'NOT ' : '').'IN';
			$sqlOpts['where'][] = "{$column} {$operator} ({$inClause})";
			return;
		}
		
		$operator = $isReverse ? '<>' : '=';
		$sqlOpts['where'][] = "{$column} {$operator} ".($encrypted ? "[encrypt:{$token}]" : $token);
		$sqlParam[$token] = $value;
	}
	
	/**
	 * Inject multiple filters to `$sqlOpts` and `$sqlParam` used for `Db::sqlBuilder`.
	 * @see Service::injectFilter()
	 * @param array $filters Key-value pair where the keys refer to column name and values refer to, value.
	 * @param array $sqlOpts Array of SQL builder options.
	 * @param array $sqlParam Key-value pair where the keys refer to tokens of SQL query and values refer to, value.
	 */
	public static function injectFilters(array $filters, array &$sqlOpts, array &$sqlParam) {
		foreach ($filters as $key => $value) {
			self::injectFilter($key, $value, $sqlOpts, $sqlParam);
		}
	}
	
	/**
	 * Inject a filter for keyword searching to `$sqlOpts` and `$sqlParam` used for `Db::sqlBuilder`.
	 * @see Service::injectFilter()
	 * @param string $keyword Keyword to search.
	 * @param array $comparator List of columns to search for the given keyword.
	 * @param array $sqlOpts Array of SQL builder options.
	 * @param array $sqlParam Key-value pair where the keys refer to tokens of SQL query and values refer to, value.
	 */
	public static function injectKeywordFilter($keyword, array $comparator, array &$sqlOpts, array &$sqlParam) {
		if ($keyword == null || count($comparator) == 0) return;
		if (!is_array($keyword)) {
			$keyword = explode(' ', $keyword);
		}
		$whereClause = implode(' OR ', array_map(function($idx, $piece) use ($comparator, &$sqlParam) {
			$sqlParam[":keyword_{$idx}"] = "%{$piece}%";
			return(implode(' OR ', array_map(function($column) use ($idx) {
				return("{$column} LIKE :keyword_{$idx}");
			}, $comparator)));
		}, array_keys($keyword), $keyword));
		$sqlOpts['where'][] = "({$whereClause})";
	}
	
	/**
	 * Return a list of standard options.
	 */
	protected static function prepareOpts() {
		$array = array(
			'group' => array(),
			'order' => array(),
			'limit' => null,
			'countOnly' => false,
		);
		$args = func_get_args();
		foreach ($args as $opts) {
			if (!is_array($opts)) continue;
			$array = array_replace($array, $opts);
		}
		return($array);
	}
	
	/**
	 * Fetch entries from database.
	 * @see Db::sqlBuilder()
	 * @param array $sqlOpts Array of SQL builder options.
	 * @param array $sqlParam Key-value pair where the keys refer to tokens of SQL query and values refer to, value.
	 * @param boolean $debug Whether to enable debug logging.
	 * @return array Record set.
	 */
	public static function getEntries(array $sqlOpts, array $sqlParam, $debug = false) {
		$rs = Db::query(Db::sqlBuilder('select', $sqlOpts), $sqlParam, $debug);
		return($rs === false || count($rs) == 0 ? array() : $rs);
	}
	
	/**
	 * Return the number of entries from database.
	 * @see Db::sqlBuilder()
	 * @param array $sqlOpts Array of SQL builder options.
	 * @param array $sqlParam Key-value pair where the keys refer to tokens of SQL query and values refer to, value.
	 * @param boolean $debug Whether to enable debug logging.
	 * @return int Number of entries.
	 */
	public static function getCount(array $sqlOpts, array $sqlParam, $debug = false) {
		$sqlOpts['column'] = array('1');
		$sqlOpts['order'] = array();
		$sqlOpts['limit'] = null;
		$rs = Db::query('SELECT COUNT(1) AS "count" FROM ('.Db::sqlBuilder('select', $sqlOpts).') A', $sqlParam, $debug);
		return($rs === false ? 0 : $rs[0]['count']);
	}
	
	/**
	 * Transform a record set into an array of entities.
	 * @param array $rs Record set, or a single record.
	 * @param string|Entity $entity Name of entity class or an entity.
	 * @return array|Entity Array of entities, or a single entity.
	 */
	public static function toEntity($rs, $entity) {
		if (!is_object($entity)) {
			$entity = new $entity();
		}
		if (ArrayHelper::isAssociative($rs)) {
			$data = clone $entity;
			$data->bind($rs);
		} else {
			$data = array();
			foreach ($rs as $row) {
				$obj = clone $entity;
				$obj->bind($row);
				$data[] = $obj;
			}
		}
		return($data);
	}
	
}
?>
