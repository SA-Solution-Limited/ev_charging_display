<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

require_once('includes/class/class.service.php');
require_once('includes/entity/entity.configuration.php');

class ConfigurationService extends Service {
	
	protected static $cache = array();

	public static function getValue($key, $refresh = false) {
		if (!$refresh && array_key_exists($key, self::$cache)) {
			return(self::$cache[$key]);
		}
		$rs = Db::query('SELECT `cfgValue` FROM [table:configuration] WHERE `cfgKey` = :cfgKey', array(
			':optKey'  => $key,
		));
		if ($rs === false || count($rs) == 0) {
			return(null);
		};
		self::$cache[$key] = $rs[0]['cfgValue'];
		return(self::$cache[$key]);
	}

	public static function getByKey($value, $opts = array()) {
		return($value == null ? null : static::getUnique('cfgKey', $value, $opts));
	}

	public static function getUnique($key, $value, $opts = array()) {
		if ($key == null || $value == null) return(null);
		$filter = array(
			$key => $value,
		);
		$opts = array_merge(array(
			'limit' => array(0, 1),
		), $opts);
		return(ArrayHelper::getValue(static::getAll($filter, $opts), 0, null));
	}
	
	public static function getAll($filter = array(), $opts = array()) {
		$filter = static::prepareFilter(
			static::generateEntityFilter('ConfigurationEntity'),
			array(
				'cfgGroup' => null,
			),
			$filter
		);
		
		$sqlOpts = array(
			'table'  => 'configuration',
			'column' => array('*'),
			'where'  => array(),
			'order'  => array(),
		);
		$sqlParam = array();
		
		static::injectFilters(array(
			'cfgKey' => $filter['cfgKey'],
		), $sqlOpts, $sqlParam);
				
		if ($filter['cfgGroup'] != null) {
			static::injectFilter('cfgKey_regexp', '^'.preg_quote($filter['cfgGroup'].'.', '/'), $sqlOpts, $sqlParam);
		}
		
		if ($opts['countOnly']) {
			return(static::getCount($sqlOpts, $sqlParam));
		}
		
		if (is_array($opts['order']) && count($opts['order'])) {
			$sqlOpts['order'] = $opts['order'];
		}
		if (is_array($opts['limit']) && count($opts['limit']) == 2) {
			$sqlOpts['limit'] = ':offset, :length';
			$sqlParam[':offset'] = $opts['limit'][0];
			$sqlParam[':length'] = $opts['limit'][1];
		}
		$data = static::toEntity(static::getEntries($sqlOpts, $sqlParam), 'ConfigurationEntity');

		array_walk($data, function($row) {
			self::$cache[$row->cfgKey] = $row->cfgValue;
		});

		return($data);
	}
	
}
?>
