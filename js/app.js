var app = angular.module('ftpAdmFrontend', ['ngAnimate', 'ngRoute', 'ngSanitize', 'mgcrea.ngStrap', 'ftpAdmFrontendDirv', 'ftpAdmFrontendService']);

app.config(['$routeProvider', function($routeProvider) {
	$routeProvider.when('/', {
		templateUrl: 'view/overview.html'
	});
	$routeProvider.when('/login', {
		templateUrl: 'view/login.html'
	});
	$routeProvider.when('/account', {
		templateUrl: 'view/account.html',
        controller: 'AccountController'
	});
	$routeProvider.otherwise({
		redirectTo: '/'
	});
}]);

app.controller('LoginController', ['$scope', '$http', '$alert', '$locale', '$location',
	function($scope, $http, $alert, $locale, $location) {
		$scope.login = '';
		$scope.password = '';

		$scope.submit = function() {
			if ($scope.password && $scope.password.length == 0)
				return;

			$http({
				method: 'POST',
				url: $scope.config.api.base_url + '/api/v1/auth/',
				data: {
					'login': $scope.login,
					'password': $scope.password
				},
				cache: false,
				responseType: "json",
				withCredentials: true
			}).success(function(data, status, headers, config) {
				$scope.loadUserInfo(function() {
					$location.path('/');
				});
			}).error(function(data, status, headers, config) {
				if(status === 500 || status === -1) {
					message = '<span faf-tr="host.problem">' + $locale.translate('host.problem') + '</span>';
					type = 'danger';
				} else if(status === 401) {
					message = '<span faf-tr="login.authentification.failed">' + $locale.translate('login.authentification.failed') + '</span>';
					type = 'warning';
				} 
				$alert({
					content: message,
					container: '#display-alert',
					type: type,
					html: true,
					show: true
				});
			});
		}
	}
]);

app.controller('MainController', ['$scope', '$http', '$location',
	function($scope, $http, $location) {
		$scope.connected = false;
		$scope.user = {
			fullname: ''
		};

		$http({
			method: 'GET',
			url: 'config.json',
			cache: true,
			responseType: "json",
		}).success(function(data, status, headers, config) {
			$scope.config = data;
			$http({
				method: 'GET',
				url: $scope.config.api.base_url + '/api/v1/auth/',
				cache: false,
				responseType: "json",
				withCredentials: true
			}).success(function(data, status, headers, config) {
				$scope.loadUserInfo(function() {
					$scope.connected = true;
					if ($location.path() == '/login')
						$location.path('/');
				});
			}).error(function(data, status, headers, config) {
				$scope.connected = false;
				if ($location.path() != '/login')
					$location.path('/login');
			});
		}).error(function(data, status, headers, config) {
		});

		$scope.disconnect = function() {
			$http({
				method: 'DELETE',
				url: $scope.config.api.base_url + '/api/v1/auth/',
				cache: false,
				responseType: "json",
				withCredentials: true
			}).success(function(data, status, headers, config) {
				$scope.connected = false;
				$scope.user = {
					fullname: ''
				};

				if (interval !== null)
					clearInterval(interval);
				interval = null;

				$location.path('/login');
			}).error(function(data, status, headers, config) {
				debugger;
			});
		}

		$scope.$on('$routeChangeStart', function(event, next, current) {
			if ($scope.connected) {
				if ($location.path() == '/login')
					$location.path('/');
			} else {
				if ($location.path() != '/login')
					$location.path('/login');
			}
		});

		var interval = null;
		$scope.loadUserInfo = function(callback) {
			$http({
				method: 'GET',
				url: $scope.config.api.base_url + '/api/v1/user/',
				cache: false,
				responseType: "json",
				withCredentials: true
			}).success(function(data, status, headers, config) {
				$scope.connected = true;
				$scope.user = data.user;

				$scope.usage_color = 'progress-bar-success';
				$scope.usage_pct = (data.user.used_space / data.user.total_space) * 100.0;
				$scope.usage_width = $scope.usage_pct;

				if ($scope.usage_pct > 80)
					$scope.usage_color = 'progress-bar-warning';

				if ($scope.usage_pct > 95)
					$scope.usage_color = 'progress-bar-danger';

				if ($scope.usage_pct > 100)
					$scope.usage_width = 10000 / $scope.usage_pct;

				if (interval === null) {
					interval = setInterval(function fetch() {
						$scope.loadUserInfo();
					}, 20000);
				}	

				if (callback)
					callback();
			}).error(function(data, status, headers, config) {
				$scope.connected = false;
				$scope.user = {
					fullname: ''
				};
				
				if (interval !== null)
					clearInterval(interval);
				interval = null;

				if ($location.path() != '/login')
					$location.path('/login');
			});
		}
	}
]);

app.controller('AccountController', ['$scope', '$http', '$locale', '$location', '$alert',
    function($scope, $http, $locale, $location, $alert) {
        $http({
            method: 'GET',
            url: $scope.config.api.base_url + '/api/v1/customer/',
            responseType: 'json',
            withCredentials: true
        }).then(function success(response) {
            $scope.customer = response.data;
        }, function error(response) {
            console.log(response.status);
        });
        $scope.submit = function updateCustomer() {
            $http({
                method: 'POST',
                url: $scope.config.api.base_url + '/api/v1/customer/',
                data: $scope.customer,
                responseType: 'json',
                withCredentials: true
            }).then(function success(response) {
				$alert({
					content: '<span faf-tr="account.success">' + $locale.translate('account.success') + '</span>',
					container: '#display-alert',
					type: 'success',
					html: true,
					show: true
				});
            }, function error(response) {
                // message has the form: 'msg1;msg2;...;msgN;'
                message = response.data.message.replace(/;/g, '<br />');
				$alert({
					content: '<span faf-tr="account.failed">' + message + '</span>',
					container: '#display-alert',
					type: 'danger',
					html: true,
					show: true
				});
            });
        }
    }

]);

app.controller('LanguageController', [ '$scope', '$locale',
	function($scope, $locale) {
		$scope.languagesList = [{
			text: "<span class=\"flag-icon flag-icon-gb\"></span> English",
			click: setLanguage('en')
		}, {
			divider: true
		}, {
			text: "<span class=\"flag-icon flag-icon-fr\"></span> Français",
			click: setLanguage('fr')
		}];

		function setLanguage(lang) {
			return function setLanguageInner() {
				$locale.set_lang(lang);
			}
		}
	}
]);

app.controller('AccountMenuController', [ '$scope', '$locale',
	function($scope, $locale) {
		$scope.accountMenu = [{
			fafTr: "navbar.account",
			text: "Account",
			href: "#/account"
		}, {
			divider: true
		}, {
			fafTr: "navbar.logout",
			text: "Logout",
			click: function() {
				$scope.disconnect();
			}
		}];

		$scope.$on('$localeChanged', function() {
			for (var i = 0, n = $scope.accountMenu.length; i < n; i++) {
				if (!('fafTr' in $scope.accountMenu[i]))
					continue;

				$scope.accountMenu[i].text = $locale.translate($scope.accountMenu[i].fafTr);
			}
		});
	}
]);

