<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

require_once('includes/class/class.service.php');
require_once('includes/entity/entity.routing.php');

class RoutingService extends Service {
	
	public static function getById($value, $opts = array()) {
		return(static::getUnique('rouId', $value, $opts));
	}
	
	public static function getBySlug($slug, $locale = null, $opts = array()) {
		$filter = array(
			'rouSlug' => $slug,
			'rouLocale' => $locale,
		);
		return(ArrayHelper::getValue(static::getAll($filter, $opts), 0, null));
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
		$filter = static::prepareFilter(static::generateEntityFilter('RoutingEntity'), array(
			'rouId_not' => null,
		), $filter);
		
		$opts = static::prepareOpts(array(
			'enabledOnly' => false,
		), $opts);
		
		$sqlOpts = array(
			'from'   => '[table:routing]',
			'column' => array('*'),
			'where'  => array('`rouIsDeleted` = 0'),
		);
		$sqlParam = array();
		
		static::injectFilters(array(
			'rouId' => $filter['rouId'],
			'rouId_not' => $filter['rouId_not'],
			'rouType' => $filter['rouType'],
			'rouSlug' => $filter['rouSlug'],
			'rouTarget' => $filter['rouTarget'],
			'rouIsEnabled' => $filter['rouIsEnabled'],
		), $sqlOpts, $sqlParam);
		self::injectKeywordFilter($filter['keyword'], array('rouSlug', 'rouTarget'), $sqlOpts, $sqlParam);
		
		if ($filter['rouLocale'] != null) {
			$sqlOpts['where'][] = '(`rouLocale` = :rouLocale OR (`rouLocale` IS NULL AND `rouType` = "REWRITE"))';
			$sqlParam[':rouLocale'] = $filter['rouLocale'];
		}
		
		if ($opts['enabledOnly']) {
			$sqlOpts['where'][] = '`rouIsEnabled` = 1';
		}
		
		if ($opts['countOnly']) {
			return(static::getCount($sqlOpts, $sqlParam));
		} else {
			if (is_array($opts['limit']) && count($opts['limit']) == 2) {
				$sqlOpts['limit'] = ':offset, :length';
				$sqlParam[':offset'] = $opts['limit'][0];
				$sqlParam[':length'] = $opts['limit'][1];
			}
			if (is_array($opts['order']) && count($opts['order'])) {
				$sqlOpts['order'] = $opts['order'];
			}
			return(static::toEntity(static::getEntries($sqlOpts, $sqlParam), 'RoutingEntity'));
		}
	}
	
	public static function save(RoutingEntity &$entity) {
		return($entity->insertOrUpdate('rouId'));
	}
	
	public static function delete(RoutingEntity &$entity) {
		$entity->rouIsDeleted = 1;
		$entity->rouDelKey = time();
		return(static::save($entity));
	}
	
	public static function generateSlug($length = 8) {
		return(static::generateUid('routing', 'rouSlug'));
	}
	
	public static function isDuplicatedSlug($slug, $locale = null, $id = null) {
		$filter = array(
			'rouSlug' => $slug,
			'rouLocale' => $locale,
			'rouId_not' => $id,
		);
		return(static::getAll($filter, array('countOnly' => true)) > 0);
	}
	
	public static function recordClick($id) {
		if (!($entity = static::getById($id))) return;
		$entity->rouTotalClick++;
		$entity->insertOrUpdate();
	}
	
}
?>
