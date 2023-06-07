<?php
/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 * 
 * @var Site $this
 */
?>
<!DOCTYPE html>
<html lang="<?php echo($this->locale); ?>" ng-app="app">
<head>
<?php require('com.meta.php'); ?>
</head>

<body class="<?php echo(isset($pageClass) ? $pageClass : ''); ?>">
<div class="page-wrapper">
	<div class="page-header" ng-controller="HeaderController" ng-class="{'bg-danger': warning}">
		<div class="container">
			<div class="clock">{{date}}</div>
			<div class="weather">
				<div class="weather-info-chunk">
					<img class="weather-icon" ng-src="{{weather.icon}}" ng-attr-alt="{{weather.summary}}" />
					<span class="weather-temperature">{{weather.temperature.value}}{{weather.temperature.unit}}</span>
				</div>
				<div class="weather-info-chunk" ng-if="weather.warnings.length">
					<img class="weather-warning" ng-src="{{item.icon}}" ng-attr-alt="{{item.name}}" ng-repeat="item in weather.warnings" />
				</div>
			</div>
		</div>
	</div>
