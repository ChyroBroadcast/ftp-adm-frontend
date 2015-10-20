var app = angular.module('ftpAdmFrontend', ['ngAnimate', 'ngRoute', 'ngSanitize', 'mgcrea.ngStrap', 'ftpAdmFrontendDirv', 'ftpAdmFrontendService']);

app.config(['$routeProvider', function($routeProvider) {
	$routeProvider.when('/', {
		templateUrl: 'view/overview.html'
	});
	$routeProvider.when('/login', {
		templateUrl: 'view/login.html'
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
				url: '/ftp-adm-api/api/v1/auth/',
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
				$alert({
					content: '<span faf-tr="login.authentification.failed">' + $locale.translate('login.authentification.failed') + '</span>',
					container: '#display-alert',
					type: 'info',
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
			url: '/ftp-adm-api/api/v1/auth/',
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

		$scope.disconnect = function() {
			debugger;
			$http({
				method: 'DELETE',
				url: '/ftp-adm-api/api/v1/auth/',
				cache: false,
				responseType: "json",
				withCredentials: true
			}).success(function(data, status, headers, config) {
				$scope.connected = false;
				$scope.user = {
					fullname: ''
				};
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

		$scope.loadUserInfo = function(callback) {
			$http({
				method: 'GET',
				url: '/ftp-adm-api/api/v1/user/',
				cache: false,
				responseType: "json",
				withCredentials: true
			}).success(function(data, status, headers, config) {
				$scope.connected = true;
				$scope.user = data.user;

				if (callback)
					callback();
			}).error(function(data, status, headers, config) {
				$scope.connected = false;
				$scope.user = {
					fullname: ''
				};
				if ($location.path() != '/login')
					$location.path('/login');
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

