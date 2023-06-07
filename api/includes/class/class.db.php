<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

class Db {
	
	protected static $dbh = null;
	
	public static function query($query, $param = array(), $debug = 0) {
		if (!self::$dbh && !self::connect()) return(false);
		$query = self::prepare($query);
		$stmt = self::$dbh->prepare($query);
		self::bindParam($stmt, $param ? $param : array());
		$stmt->execute();
		if ($debug) echo($stmt->queryString.PHP_EOL);
		
		if ($stmt->errorCode() !== '00000') {
			if ($debug) {
				$e = $stmt->errorInfo();
				self::exception($e[2]);
			}
			return(false);
		}
		return($stmt->fetchAll());
	}
	
	public static function insert($query, $param = array(), $debug = 0) {
		if (!self::$dbh && !self::connect()) return(false);
		$query = self::prepare($query);
		$stmt = self::$dbh->prepare($query);
		self::bindParam($stmt, $param ? $param : array());
		$stmt->execute();
		if ($debug) echo($stmt->queryString.PHP_EOL);
		
		if ($stmt->errorCode() !== '00000') {
			if ($debug) {
				$e = $stmt->errorInfo();
				self::exception($e[2]);
			}
			return(false);
		}
		return($stmt->rowCount());
	}
	
	public static function update($query, $param = array(), $debug = 0) {
		return(self::insert($query, $param, $debug));
	}
	
	public static function delete($query, $param = array(), $debug = 0) {
		return(self::insert($query, $param, $debug));
	}

	public static function tableExists($name) {
		if (!self::$dbh && !self::connect()) return(false);
		return(Db::query("SELECT 1 FROM [table:{$name}]") !== false);
	}
	
	public static function beginTransaction() {
		if (!self::$dbh && !self::connect()) return(false);
		self::$dbh->beginTransaction();
	}
	
	public static function commitTransaction() {
		if (!self::$dbh) return(false);
		self::$dbh->commit();
	}
	
	public static function discardTransaction() {
		if (!self::$dbh) return(false);
		self::$dbh->rollBack();
	}
	
	public static function getLastInsertId() {
		if (!self::$dbh) return(false);
		$rs = self::query('SELECT LAST_INSERT_ID() AS "id"');
		return($rs === false ? false : $rs[0]['id']);
	}
	
	public static function sqlBuilder($type, $opts) {
		switch (strtolower($type)) {
			case 'select':
				return(self::sqlBuilder_select($opts));
			case 'insert':
				return(self::sqlBuilder_insert($opts));
			case 'replace':
				return(self::sqlBuilder_replace($opts));
			case 'update':
				return(self::sqlBuilder_update($opts));
			case 'delete':
				return(self::sqlBuilder_delete($opts));
		}
		return(false);
	}
	
	protected static function sqlBuilder_select($opts) {
		$_d = array(
			'from'        => null,
			'table'       => null,
			'view'        => null,
			'column'      => '*',
			'join'        => array(),
			'naturaljoin' => array(),
			'innerjoin'   => array(),
			'outerjoin'   => array(),
			'leftjoin'    => array(),
			'rightjoin'   => array(),
			'where'       => array(),
			'group'       => array(),
			'order'       => array(),
			'limit'       => null,
		);
		$opts = array_merge($_d, $opts);
		if (!$opts['from'] && !$opts['table'] && !$opts['view']) return(false);
		
		if (is_array($opts['column'])) $opts['column'] = implode(', ', $opts['column']);
		$sql = "SELECT {$opts['column']} FROM ";
		
		if ($opts['from']) {
			if (is_array($opts['from'])) {
				$sql .= implode(', ', $opts['from']);
			} else {
				$sql .= $opts['from'];
			}
		} else if ($opts['table']) {
			if (is_array($opts['table'])) {
				$sql .= "[table:".implode('], [table:', $opts['from'])."]";
			} else {
				$sql .= "[table:{$opts['table']}]";
			}
		} else if ($opts['view']) {
			if (is_array($opts['view'])) {
				$sql .= "[view:".implode('], [view:', $opts['from'])."]";
			} else {
				$sql .= "[view:{$opts['view']}]";
			}
		}
		
		$joinMapping = array(
			'join'        => 'JOIN',
			'naturaljoin' => 'NATURAL JOIN',
			'innerjoin'   => 'INNER JOIN',
			'outerjoin'   => 'OUTER JOIN',
			'leftjoin'    => 'LEFT JOIN',
			'rightjoin'   => 'RIGHT JOIN',
		);
		foreach ($joinMapping as $key => $syntax) {
			if (empty($opts[$key]) || !is_array($opts[$key])) continue;
			$sql .= implode('', array_map(function($statement) use ($syntax) {
				return(" {$syntax} {$statement}");
			}, $opts[$key]));
		}
		
		if (!empty($opts['where']) && is_array($opts['where'])) {
			$sql .= ' WHERE '.implode(' AND ', array_map(function($statement) {
				return("({$statement})");
			}, $opts['where']));
		}
		if (!empty($opts['group'])) $sql .= " GROUP BY ".implode(', ', $opts['group']);
		if (!empty($opts['order'])) $sql .= " ORDER BY ".implode(', ', $opts['order']);
		if (!empty($opts['limit'])) $sql .= " LIMIT {$opts['limit']}";
		
		return($sql);
	}
	
	protected static function sqlBuilder_insert($opts) {
		$_d = array(
			'table'  => null,
			'column' => array(), // key-value pair
			'encrypted' => array(), // array of encrypted columns
		);
		$opts = array_merge($_d, $opts);
		if (!$opts['table'] || count($opts['column']) == 0) return(false);
		
		$columns = implode(', ', array_map(function($col) {
			return("`$col`");
		}, $opts['column']));
		$values  = implode(', ', array_map(function($col) use ($opts) {
			return(in_array($col, $opts['encrypted']) ? "[encrypt::{$col}]" :":$col");
		}, $opts['column']));
		$odku = ''.implode(', ', array_map(function($col) use ($opts) {
			return("`$col` = ".(in_array($col, $opts['encrypted']) ? "[encrypt::{$col}]" :":$col"));
		}, $opts['column']));
		return("INSERT INTO [table:{$opts['table']}] ({$columns}) VALUES ({$values}) ON DUPLICATE KEY UPDATE {$odku}");
	}
	
	protected static function sqlBuilder_replace($opts) {
		$_d = array(
			'table'  => null,
			'column' => array(), // key-value pair
			'encrypted' => array(), // array of encrypted columns
		);
		$opts = array_merge($_d, $opts);
		if (!$opts['table'] || count($opts['column']) == 0) return(false);
		
		$columns = implode(', ', array_map(function($col) {
			return("`$col`");
		}, $opts['column']));
		$values  = implode(', ', array_map(function($col) use ($opts) {
			return(in_array($col, $opts['encrypted']) ? "[encrypt::{$col}]" :":$col");
		}, $opts['column']));
		return("REPLACE INTO [table:{$opts['table']}] ({$columns}) VALUES ({$values})");
	}
	
	protected static function sqlBuilder_update($opts) {
		$_d = array(
			'table'  => null,
			'column' => array(), // array of columns to update
			'encrypted' => array(), // array of encrypted columns
			'where'  => null,
		);
		$opts = array_merge($_d, $opts);
		if (!$opts['table'] || count($opts['column']) == 0 || !$opts['where']) return(false);
		
		$columns = implode(', ', array_map(function($col) use ($opts) {
			return("`$col` = ".(in_array($col, $opts['encrypted']) ? "[encrypt::{$col}]" :":$col"));
		}, $opts['column']));
		$where = implode(' AND ', $opts['where']);
		return("UPDATE [table:{$opts['table']}] SET {$columns} WHERE {$where}");
	}
	
	protected static function sqlBuilder_delete($opts) {
		$_d = array(
			'table' => null,
			'where' => null,
		);
		$opts = array_merge($_d, $opts);
		if (!$opts['table'] || !$opts['where']) return(false);
		
		return("DELETE FROM [table:{$opts['table']}] WHERE ".implode(' AND ', $opts['where']));
	}
	
	public static function connect() {
		if (!defined('DB_HOST')) return(false);
		$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
		self::$dbh = new PDO($dsn, DB_USER, DB_PASS, array(
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET time_zone = "'.DB_TIMEZONE.'";',
		));
		return(true);
	}
	
	public static function disconnect() {
		self::$dbh = null;
		return(true);
	}
	
	protected static function prepare($query) {
		$dbPfx = DB_PREFIX;
		$dbKey = DB_AESKEY;
		$query = preg_replace('/\[table:(.+?)\]/', "`{$dbPfx}$1`", $query);
		$query = preg_replace('/\[view:(.+?)\]/', "`{$dbPfx}view_$1`", $query);
		$query = preg_replace('/\[encrypt:(.+?)\]/', "AES_ENCRYPT($1, '{$dbKey}')", $query);
		$query = preg_replace('/\[decrypt:(.+?)\]/', "CONVERT(AES_DECRYPT($1, '{$dbKey}') USING utf8)", $query);
		return($query);
	}
	
	protected static function bindParam(&$stmt, array $param = array()) {
		foreach ($param as $key => $val) {
			if (!preg_match('/^:/', $key)) $key++;
			switch (gettype($val)) {
				case 'integer':
					$type = PDO::PARAM_INT;
					break;
				case 'NULL':
					$type = PDO::PARAM_NULL;
					break;
				case 'double': // using PARAM_INT leads to truncation of all decimal places
				default:
					$type = PDO::PARAM_STR;
			}
			$stmt->bindValue($key, $val, $type);
		}
	}
	
	protected static function exception($message) {
		echo($message.PHP_EOL);
	}
	
}
?>