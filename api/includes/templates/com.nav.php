<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

if (!function_exists('renderMenu')) {
	function renderMenu($menu, $opts = array(), $level = 0) {
		$opts = array_merge(array(
			'active' => array(),
			'maxLevel' => -1,
			'showIcon' => false,
			'iconPrefix' => 'fas fa-',
			'showOpener' => false,
			'controlKey' => null,
			'listClass' => null,
			'listItemClass' => null,
			'anchorClass' => null,
		), $opts);
		
		$tpl_menu = '
			<ul class="{{class}}">
				{{items}}
			</ul>
		';
		
		$tpl_submenu = '
			<li class="{{listitemclass}}">
				<a href="{{href}}" class="{{anchorclass}}">
					{{icon}}
					<span>{{title}}</span>
				</a>
				{{opener}}
				{{submenu}}
			</li>
		';
		
		$items = array_filter(array_map(function($key, $item) use ($tpl_submenu, $opts, $level) {
			if (!ArrayHelper::getValue($item, 'name')) return(false);
			if ($opts['controlKey'] != null) {
				if (is_array($opts['controlKey'])) {
					$failedConditions = array_filter($opts['controlKey'], function($cond) use ($item) {
						return(!ArrayHelper::getValue($item, $cond, true));
					});
					if (count($failedConditions)) {
						return(false);
					}
				} else if (!ArrayHelper::getValue($item, $opts['controlKey'], true)) {
					return(false);
				}
			}
			
			$hasChild = isset($item['child']) && count($item['child']) > 0;
			$isActive = isset($opts['active'][$level]) && $key == $opts['active'][$level];
			
			$prop = array(
				'listItemClass' => array($opts['listItemClass']),
				'anchorClass'   => array($opts['anchorClass'], ArrayHelper::getValue($item, 'className')),
				'title'         => _r($item['name']),
				'icon'          => '',
				'href'          => ArrayHelper::getValue($item, 'href', 'javascript:;'),
				'opener'        => '',
				'submenu'       => '',
			);
			
			if ($opts['showIcon'] && ArrayHelper::getValue($item, 'icon')) {
				$prop['icon'] = '<i class="'.$opts['iconPrefix'].$item['icon'].'"></i>';
			}
			if ($opts['showOpener'] && $hasChild) {
				$prop['opener'] = '<button type="button" class="nav-opener" aria-label="Expand"><span></span></button>';
			}
			if (($opts['maxLevel'] == -1 || $opts['maxLevel'] > $level + 1) && $hasChild) {
				$prop['submenu'] = renderMenu($item['child'], $opts, $level + 1);
			}
			
			if ($isActive) $prop['listItemClass'][] = 'active';
			$prop['listItemClass'] = trim(implode(' ', $prop['listItemClass']));
			$prop['anchorClass']   = trim(implode(' ', $prop['anchorClass']));
			
			return(TemplateHelper::bindParams($tpl_submenu, $prop));
		}, array_keys($menu), array_values($menu)));
		
		return(TemplateHelper::bindParams($tpl_menu, array(
			'class' => trim($opts['listClass'] ? $opts['listClass'] : ($level == 0 ? 'nav' : '')),
			'items' => implode('', $items),
		)));
	}
}
?>
