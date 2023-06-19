/**
 * Technetium PHP Framework
 * @version 3.0
 * @author Tony Leung <tony.leung@cruzium.com>
 * @copyright Copyright (c) 2023 Cruzium Digital
 * @license https://opensource.org/license/gpl-3-0/ GPL-3.0-only
 */

app.factory('$api', ['$rootScope', '$http', '$q', '$translate', function($rootScope, $http, $q, $translate) {
	
	var endpoint = {
		api: urlApi,
		hko: 'https://data.weather.gov.hk/weatherAPI/opendata/weather.php',
		td: 'https://resource.data.one.gov.hk/td/en/'
	};
	
	var ajax = {
		submit: function(config) {
			var deferred = $q.defer();
			ApiHelper[config.method](config.url, config.data, function(response) {
				deferred.resolve(response);
			});
			return(deferred.promise);
		},
		query: function(type, url, data) {
			var config = {
				method: 'get',
				url: ajax.helper.buildUrl(type, url)
			};
			data = data || {};
			data._ = new Date().getTime();
			var url = Util.parseUrl(config.url);
			config.url = url.protocol + '//' + url.host + (url.port ? ':' + url.port : '') + url.pathname + Util.addQueryString(data, url.search ? url.search : '?') + url.hash;
			return(ajax.submit(config));
		},
		post: function(type, url, data) {
			var config = {
				method: 'post',
				url: ajax.helper.buildUrl(type, url),
				data: data || {}
			};
			return(ajax.submit(config));
		},
		delete: function(type, url, data) {
			var config = {
				method: 'delete',
				url: ajax.helper.buildUrl(type, url),
				data: data || {}
			};
			return(ajax.submit(config));
		},
		helper: {
			buildUrl: function(type, url, data) {
				if (url.constructor == Array) {
					url = url.filter(function(v) {
						return(v !== null && v !== undefined && v !== '');
					}).join('/');
				}
				return((endpoint[type] ? endpoint[type] : endpoint.api) + url + (data ? Util.addQueryString(data) : ''));
			},
			getHeaders: function() {
				return({
					'Accept-Language': locale,
					'Authorization': $rootScope.accessToken ? ('Bearer ' + $rootScope.accessToken) : undefined
				});
			}
		}
	};
	
	return({
		getChargingStatus: function(currentStatus, currentBatteryLevel) {
			return(ajax.query('api', 'status/', {status: currentStatus, currentBatteryLevel: currentBatteryLevel}));
		},
		getSlideshow: function() {
			return(ajax.query('api', 'slideshow/'));
		},
		getWeatherReport: function(location) {
			return(ajax.query('hko', '', {
				dataType: 'rhrread',
				lang: 'tc'
			}).then(function(response) {
				var data = {
					summary: $translate.instant('weather.icon_' + response.icon),
					icon: urlBase + 'images/weather/icons/' + response.icon + '.png'
				};
				var idx = response.temperature.data.findIndex(function(item) {
					return(item.place == location);
				});
				data.temperature = {
					value: response.temperature.data[idx].value,
					unit: '°' + response.temperature.data[idx].unit,
				};
				return({
					success: true,
					data: data
				});
			}));
		},
		getWeatherWarning: function() {
			return(ajax.query('hko', '', {
				dataType: 'warnsum',
				lang: 'tc'
			}).then(function(response) {
				return({
					success: true,
					data: Object.values(response).map(function(item) {
						return({
							code: item.code,
							name: item.name,
							icon: urlBase + 'images/weather/warnings/' + item.code + '.png'
						})
					})
				});
			}));
		},
		getWeatherForecast: function() {
			return(ajax.query('hko', '', {
				dataType: 'flw',
				lang: 'tc'
			}).then(function(response) {
				return({
					success: true,
					data: {
						weatherForecast: [
							response.forecastDesc,
							response.outlook
						].map(function(line) {
							return('<p>' + line + '</p>');
						}).join(''),
						tropicalCycloneInfo: response.tcInfo ? '<p>' + response.tcInfo + '</p>' : null,
					}
				});
			}));
		},
		getTrafficNews: function(keywords) {
			keywords = keywords || [];
			if (keywords.constructor != Array) {
				keywords = [keywords];
			}
			keywords = keywords.map(function(keyword) {
				return(keyword.toLowerCase());
			});
			return(ajax.query('td', 'specialtrafficnews.xml').then(function(response) {
				return({
					success: true,
					data: $(response).find('message').toArray().map(function(elem) {
						return({
							description: $(elem).find('ChinText').text(),
							date: $(elem).find('ReferenceDate').text()
						});
					}).filter(function(item) {
						var match = keywords.map(function(keyword) {
							return(item.description.indexOf(keyword) > -1 ? 1 : 0);
						});
						return(match.sum() > 0);
					})
				});
			}));
		}
	});
}]);
