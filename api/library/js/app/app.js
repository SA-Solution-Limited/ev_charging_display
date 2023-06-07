/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

window.app = window.app || angular.module('app', ['ngResource', 'ngSanitize', 'pascalprecht.translate']);

app.config(function($sceDelegateProvider, $translateProvider) {
	$translateProvider.useStaticFilesLoader({
		prefix: urlNgLib + 'locale/locale.',
		suffix: '.json'
	}).useSanitizeValueStrategy('escape').preferredLanguage(locale);
});

app.run(function($rootScope) {
	$rootScope.format = {
		date: 'D/M/YYYY (ddd)',
		time: 'HH:mm',
		datetime: 'D/M/YYYY (dd) HH:mm'
	};
	$rootScope.unit = {
		currency: 'HK$'
	};
});

app.controller('HeaderController', ['$rootScope', '$scope', '$api', '$task', function($rootScope, $scope, $api, $task) {

	$scope.date = '';
	$scope.weather = {};

	$task.register('clock', function() {
		$scope.date = dayjs().format($rootScope.format.datetime);
	}, function() {
		return(60 - dayjs().$s - dayjs().$ms * 0.001);
	}, true);

	$task.register('weatherReport', function() {
		$api.getWeatherReport($rootScope.location).then(function(response) {
			$.extend($scope.weather, response.data);
		});
	}, 10 * 60, true);

	$task.register('weatherWarning', function() {
		$api.getWeatherWarning().then(function(response) {
			$scope.weather.warnings = response.data;
		});
	}, 10 * 60, true);

	$scope.$watch(function() {
		return($rootScope.status);
	}, function(newValue, oldValue) {
		$scope.warning = $rootScope.status == 'warning';
	});
}]);
