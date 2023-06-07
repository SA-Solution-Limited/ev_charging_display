<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

class SitemapXML {
	
	protected $urls = array();
	
	public function importStatic($array) {
		foreach ($array as $idx => $row) {
			if (is_array($row)) continue;
			$array[$idx] = array('href' => $row);
		}
		$this->import($array);
	}
	
	public function importDynamic($array) {
		foreach ($array as $row) {
			$sqlOpts = array(
				'from'   => $row['table'],
				'column' => array(
					'CONCAT("'.ArrayHelper::getValue($row, 'prefix').'", `'.$row['slug'].'`, "'.ArrayHelper::getValue($row, 'suffix').'") AS "href"',
				),
				'where'  => array(),
			);
			
			$row['lastmod'] = ArrayHelper::getValue($row, 'lastmod', array());
			if (is_string($row['lastmod'])) {
				$row['lastmod'] = array($row['lastmod']);
			}
			if (count($row['lastmod']) > 1) {
				$sqlOpts['column'][] = 'DATE(GREATEST(COALESCE(`'.implode('`, "1970-01-01 00:00:00"), COALESCE(`', $row['lastmod']).'`, "1970-01-01 00:00:00"))) AS "lastmod"';
			} else if (count($row['lastmod']) == 1) {
				$sqlOpts['column'][] = "DATE(`{$row['lastmod'][0]}`) AS \"lastmod\"";
			} else {
				$sqlOpts['column'][] = 'NULL AS "lastmod"';
			}
			
			if (ArrayHelper::getValue($row, 'priority') != '') {
				$sqlOpts['column'][] = '"'.$row['priority'].'" AS "priority"';
			}
			if (count(ArrayHelper::getValue($row, 'filter', array()))) {
				$sqlOpts['where'] = array_merge($sqlOpts['where'], $row['filter']);
			}
			foreach (ArrayHelper::getValue($row, 'isShow', array()) as $key) {
				// deprecated
				if (preg_match('/^!/', $key)) {
					$sqlOpts['where'][] = '`'.preg_replace('/^!/', '', $key).'` = 0';
				} else {
					$sqlOpts['where'][] = '`'.$key.'` = 1';
				}
			}
			
			$rs = Db::query(Db::sqlBuilder('select', $sqlOpts), array());
			if ($rs === false || count($rs) == 0) continue;
			
			$this->import($rs);
		}
	}
	
	public function import($array) {
		foreach ($array as $row) {
			$this->urls[] = $row;
		}
	}
	
	public function generate() {
		global $site;
		$this->writeln('<?xml version="1.0" encoding="utf-8"?>');
		$this->writeln('');
		$this->writeln('<!-- Technetium PHP Framework');
		$this->writeln('     Version: 2.9');
		$this->writeln('     Author: Tony Leung <tony.leung@cruzium.com>');
		$this->writeln('     Copyright: (c) 2023 Cruzium Digital');
		$this->writeln('     License: GPL-3.0-only -->');
		$this->writeln('');
		$this->writeln('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
		
		/* static route */
		if ($site->multilingual) {
			foreach ($site->languageOptions as $locale) {
				$this->generateUrl($locale);
			}
		} else {
			$this->generateUrl();
		}
		
		/* database route */
		
		$this->writeln('</urlset>');
	}
	
	protected function generateUrl($locale = null) {
		global $site;
		foreach ($this->urls as $row) {
			if (ArrayHelper::getValue($row, 'href', false) === false) continue;
			$this->writeln('<url>', 1);
			$this->writeln('<loc>'.$site->origin.$site->urlBase.($locale ? $locale.'/' : '').$row['href'].'</loc>', 2);
			foreach (array('lastmod', 'changefreq', 'priority') as $prop) {
				if (ArrayHelper::getValue($row, $prop)) $this->writeln('<'.$prop.'>'.$row[$prop].'</'.$prop.'>', 2); 
			}
			$this->writeln('</url>', 1);
		}
	}
	
	protected function writeln($text, $tabs = 0) {
		for ($i = 0; $i < $tabs; $i++) {
			echo("\t");
		}
		echo($text);
		echo(PHP_EOL);
	}
}
?>
