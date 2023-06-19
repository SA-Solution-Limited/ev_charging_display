/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

app.run(function($rootScope) {
	$rootScope.location = '觀塘';
	$rootScope.trafficNewsKeywords = ['龍翔道', '觀塘道'];
	$rootScope.trafficSnapshots = [
		{code: 'K810F', label: '觀塘道近牛頭角港鐵站'},
		{code: 'AID07111', label: '觀塘道近欣榮大廈'},
		{code: 'AID07112', label: '觀塘道近福淘街'},
		{code: 'K621F', label: '觀塘道近啟業邨'},
		{code: 'AID07116', label: '龍翔道近彩虹邨金漢樓'}
	];
});
