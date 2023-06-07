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

$pagePluginStyles = array(
	$this->urlBase.'library/cdn-plugins/swiper/swiper-element-bundle.min.css',
);

$pageLevelPlugins = array(
	$this->urlBase.'library/cdn-plugins/swiper/swiper-element-bundle.min.js',
);

require_once('includes/templates/com.header.php');
?>
<div class="page-body">
	<div class="page-content" ng-controller="ChargingController">
		<div class="status-vacant" ng-if="state.status == 'vacant'">
			<swiper-container init="false" ng-if="data.slideshow.length">
				<swiper-slide ng-repeat="item in data.slideshow track by $index">
					<img ng-src="{{item}}" />
				</swiper-slide>
			</swiper-container>
		</div>
		<div class="card status-entrance" ng-if="state.status == 'entrance'">
			<div class="card-image">
				<img src="<?php echo($this->urlBase); ?>images/charging/handbrake.png" />
			</div>
			<div class="card-body">
				司機請留在駕駛室<br />
				拉好泊車制動
			</div>
		</div>
		<div class="card status-charging" ng-if="state.status == 'charging' || state.status == 'charging_completed'">
			<div class="plate">{{state.plate}}</div>
			<div class="battery" ng-attr-data-color="{{state.batteryColor}}" ng-attr-data-battery-level="{{state.currentBatteryLevel}}">
				<div class="battery-body">
					<div class="battery-level">{{state.currentBatteryLevel | number: 0}}%</div>
					<div class="battery-fill" ng-style="{width: state.currentBatteryLevel + '%'}">
						<div class="battery-fill-strip"></div>
						<div class="battery-level">{{state.currentBatteryLevel | number: 0}}%</div>
					</div>
				</div>
				<div class="battery-tip"></div>
			</div>
			<div class="battery">
				<div class="battery-level-pointer">
					<div class="pointer" ng-style="{left: state.initialBatteryLevel + '%'}"></div>
				</div>
			</div>
			<div class="energy-consumption">
				<span>{{state.status == 'charging_completed' ? '充電完成' : '充電中'}}</span>
				<span class="ml-auto">{{state.energyConsumption | number: 1}} kWh</span>
			</div>
		</div>
		<div class="card status-exit" ng-if="state.status == 'exit'">
			<div class="card-image">
				<img src="<?php echo($this->urlBase); ?>images/charging/exit.png" />
			</div>
			<div class="card-body">
				請離開充電區<br />
				慢駛前進
			</div>
		</div>
		<div class="card status-warning" ng-if="state.status == 'warning'">
			<div class="card-image">
				<img src="<?php echo($this->urlBase); ?>images/charging/warning.png" />
			</div>
			<div class="card-body">
				{{state.warning.message}}<br />
				<small class="d-block text-muted">錯誤代碼：{{state.warning.code}}</small>
			</div>
		</div>
		<div class="logo">
			<div class="container">
				<div class="logo-chunk">
					<div class="logo-title">XXXX：</div>
					<div class="logo-image">
						<img src="<?php echo($this->urlBase); ?>images/logo-gmi-motors.png" alt="GMi Motors" />
						<img src="<?php echo($this->urlBase); ?>images/logo-epd.png" alt="環境保護署" />
					</div>
				</div>
				<div class="logo-chunk">
					<div class="logo-title">XXXX：</div>
					<div class="logo-image">
						<img src="<?php echo($this->urlBase); ?>images/logo-pyss.png" alt="培英中學" />
					</div>
				</div>
			</div>
		</div>
	</div>
	<aside class="page-sidebar" ng-controller="SidebarController">
		<div class="card" data-display="12" ng-if="data.tropicalCycloneInfo">
			<div class="card-header">
				<h2 class="card-title">熱帶氣旋資訊</h2>
			</div>
			<div class="card-body" ng-bind-html="data.tropicalCycloneInfo"></div>
		</div>
		<div class="card" data-display="12" ng-if="data.weatherForecast">
			<div class="card-header">
				<h2 class="card-title">天氣預測</h2>
			</div>
			<div class="card-body" ng-bind-html="data.weatherForecast"></div>
		</div>
		<div class="card" data-display="12" ng-repeat="item in data.trafficNews">
			<div class="card-header">
				<h2 class="card-title">特別交通消息</h2>
			</div>
			<div class="card-body">
				{{item.description}}
			</div>
		</div>
		<div class="card" data-display="6" ng-repeat="item in data.trafficSnapshots">
			<div class="card-header">
				<h2 class="card-title">交通情況快拍</h2>
			</div>
			<figure class="card-image">
				<div class="img-holder"><img ng-src="{{item.image}}" /></div>
				<figcaption>{{item.label}}</figcaption>
			</figure>
		</div>
	</aside>
</div>
<script type="text/javascript">
app.controller('ChargingController', ['$rootScope', '$scope', '$element', '$api', '$task', '$timeout', function($rootScope, $scope, $element, $api, $task, $timeout) {

	$scope.data = {
		slideshow: null
	};

	$scope.state = {
		status: undefined,
		initialBatteryLevel: undefined,
		currentBatteryLevel: undefined,
		batteryColor: undefined
	};

	var helper = {
		deriveBatteryColor: function() {
			if ($scope.state.currentBatteryLevel <= 15) {
				$scope.state.batteryColor = 'danger';
			} else if ($scope.state.currentBatteryLevel <= 30) {
				$scope.state.batteryColor = 'warning';
			} else {
				$scope.state.batteryColor = 'success';
			}
		}
	};

	var poll = function() {
		$api.getChargingStatus($scope.state.status, $scope.state.currentBatteryLevel).then(function(response) {
			if (!response.success) return;
			angular.extend($scope.state, response.data);
			helper.deriveBatteryColor();
			$rootScope.status = response.data.status;
		}).finally(function() {
			poll();
		});
	};
	poll();

	var initSlideshow = function() {
		$timeout(function() {
			var _swiper = $element.find('swiper-container')[0];
			if (!_swiper) return;
			var opts = {
				loop: true,
				autoplay: {
					delay: 5000
				}
			}
			Object.assign(_swiper, opts);
			_swiper.initialize();
		});
	}

	$task.register('slideshow', function() {
		$api.getSlideshow().then(function(response) {
			$scope.data.slideshow = null;
			$timeout(function() {
				$scope.data.slideshow = response.data;
				initSlideshow();
			});
		});
	}, 6 * 60 * 60, true);

	$scope.$watch(function() {
		return($scope.state.status);
	}, function(newValue, oldValue) {
		if (newValue == oldValue) return;
		if (newValue == 'vacant') {
			initSlideshow();
		}
	})
}]);

app.controller('SidebarController', ['$rootScope', '$scope', '$element', '$api', '$task', function($rootScope, $scope, $element, $api, $task) {

	$scope.slides = [];
	$scope.data = {
		tropicalCycloneInfo: null,
		weatherForecast: null,
		trafficNews: null,
		trafficSnapshots: null
	};

	$task.register('weatherForecast', function() {
		$api.getWeatherForecast().then(function(response) {
			angular.extend($scope.data, response.data);
		});
	}, 10 * 60, true);

	$task.register('trafficNews', function() {
		$api.getTrafficNews($rootScope.trafficNewsKeywords).then(function(response) {
			$scope.data.trafficNews = response.data;
		});
	}, 10 * 60, true);

	$task.register('trafficSnapshots', function() {
		$scope.data.trafficSnapshots = $rootScope.trafficSnapshots.map(function(item) {
			return({
				label: item.label,
				image: 'https://tdcctv.data.one.gov.hk/' + item.code + '.JPG?_=' + new Date().getTime()
			});
		});
	}, 5 * 60, true);

	$task.register('sidebarSlideshow', function() {
		var $cards = $($element).find('.card');
		if ($cards.filter('.active').length == 0) {
			$cards.eq(0).addClass('active');
		} else {
			var $activeCard = $cards.filter('.active').removeClass('active');
			if ($activeCard.next().length) {
				$activeCard.next().addClass('active');
			} else {
				$cards.eq(0).addClass('active');
			}
		}
	}, function() {
		return($($element).find('.card.active').data('display') || 12);
	}, true);

	$scope.$watch(function() {
		return($scope.data);
	}, function() {
		var $cards = $($element).find('.card');
		if ($cards.filter('.active').length == 0) {
			$cards.eq(0).addClass('active');
		}
	}, true);
}]);
</script>
<?php
require_once('includes/templates/com.footer.php');
?>
