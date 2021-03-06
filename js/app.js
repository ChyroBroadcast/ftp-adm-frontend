var app = angular.module('ftpAdmFrontend', ['ngAnimate', 'ngRoute', 'ngSanitize', 'mgcrea.ngStrap', 'ftpAdmFrontendDirv', 'ftpAdmFrontendService']);

app.config(['$routeProvider', function($routeProvider) {
	$routeProvider.when('/', {
		templateUrl: 'view/overview.html'
	});
	$routeProvider.when('/account', {
		templateUrl: 'view/account.html',
        controller: 'AccountController'
	});
	$routeProvider.when('/ftp', {
		templateUrl: 'view/ftp.html',
		controller: 'FtpListController'
	});
	$routeProvider.when('/ftp/add', {
		templateUrl: 'view/ftp/add.html'
	});
	$routeProvider.when('/ftp/edit/:user_id', {
		templateUrl: 'view/ftp/edit.html'
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
				url: $scope.config.api.base_url + '/api/v1/auth/',
				data: {
					'login': $scope.login,
					'password': $scope.password
				},
				cache: false,
				responseType: "json",
			}).success(function(data, status, headers, config) {
				$scope.loadUserInfo(function() {
					$location.path('/');
				});
			}).error(function(data, status, headers, config) {
				if (status === 500 || status === -1) {
					message = '<span faf-tr="host.problem">' + $locale.translate('host.problem') + '</span>';
					type = 'danger';
				} else if (status === 401) {
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
			$rootScope.config = $scope.config = data;
			$http({
				method: 'GET',
				url: $scope.config.api.base_url + '/api/v1/auth/',
				cache: false,
				responseType: "json",
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

app.factory('FtpUserList', [ '$http',
	function(http) {
		var backendUrl = null;

		function getFtpUsers() {
			return http({
				method: 'GET',
				url: backendUrl + '/api/v1/users/'
			});
		}

		function setBackendUrl(url) {
			backendUrl = url;
		}

		return {
			getFtpUsers: getFtpUsers,
			setBackendUrl: setBackendUrl
		};
	}
]);

app.factory('FtpUserCache', [
	function() {
		var cached_user = {};

		function getUser() {
			return cached_user;
		}

		function resetCache() {
			cached_user = {};
		}

		function setUser(user) {
			cached_user = user;
		}

		return {
			getUser: getUser,
			resetCache: resetCache,
			setUser: setUser
		};
	}
]);

app.controller('FtpListController', [ '$scope', '$interval', '$locale', '$location', '$modal', 'FtpUserList', 'FtpUserCache',
	function($scope, $interval, $locale, $location, $modal, model, cache) {
		model.setBackendUrl($scope.config.api.base_url);

		$scope.add = function() {
			cache.resetCache();
			$location.path($location.path() + '/add');
		}

		$scope.delete = function(user) {
			$modal({
				placement: 'center',
				animation: 'am-fade-and-scale',
				templateUrl: 'view/ftp/delete.html',
				controller: 'FtpDeleteUserController',
				resolve: {
					update: function() {
						return fetch;
					},
					user: function() {
						return user;
					}
				},
				show: true
			});
		}

		$scope.edit = function(user) {
			cache.setUser(user);
			$location.path($location.path() + '/edit/' + user.email);
		}

		function fetch() {
			model.getFtpUsers().then(function(returned) {
				$scope.users = [];

				for (var i = 0, n = returned.data.length; i < n; i++) {
					var tmp_users = returned.data[i];
					$scope.users.push({
						id: tmp_users.id,
						uid: tmp_users.uid,
						gid: tmp_users.gid,
						email: tmp_users.email,
						fullname: tmp_users.fullname,
						is_active: tmp_users.is_active,
						is_admin: tmp_users.is_admin,
						access: {
							read: tmp_users.ftp_read,
							write: tmp_users.ftp_write
						},
						chroot: tmp_users.chroot,
						phone: tmp_users.phone,
						home_directory: tmp_users.directory,
						can_delete_user: tmp_users.id != $scope.user.id
					});
				}
			});
		}

		$scope.users = [];
		fetch();

		var interval = $interval(fetch, 20000);
		$scope.$on('$destroy', function() {
			if (angular.isDefined(interval)) {
				$interval.cancel(interval);
				interval = null;
			}
		});
	}
]);

app.controller('FtpDeleteUserController', [ '$scope', '$http', '$timeout', 'update', 'user',
	function($scope, $http, $timeout, update, user) {
		$scope.user_email = user.email;

		$scope.not_deleted = true;
		$scope.delete_failed = false;
		$scope.delete_ok = false;

		$scope.submit = function() {
			$http({
				method: 'DELETE',
				url: $scope.config.api.base_url + '/api/v1/users/?id=' + user.id,
				cache: false,
				responseType: "json",
			}).success(function(data, status, headers, config) {
				$scope.not_deleted = false;
				$scope.delete_ok = true;
				
				update();

				$timeout(function() {
					$scope.$hide();
				}, 5000);
			}).error(function(data, status, headers, config) {
				$scope.not_deleted = false;
				$scope.delete_failed = true;

				$timeout(function() {
					$scope.$hide();
				}, 5000);
			});
		}
	}
]);

app.controller('FtpEditUser', [ '$scope', '$alert', '$locale', '$http', '$timeout', '$location', 'FtpUserCache',
	function($scope, $alert, $locale, $http, $timeout, $location, cache) {
		var cached_user = cache.getUser();

		var default_user = {
			id: cached_user.id || null,
			email: cached_user.email || '',
			fullname: cached_user.fullname || '',
			password: '',
			confirm_password: '',
			phone: cached_user.phone || '',
			is_active: cached_user.is_active != null ? cached_user.is_active : true,
			is_admin: cached_user.is_admin != null ? cached_user.is_admin : false,
			ftp_read_access: cached_user.access != null ? !!cached_user.access.read : true,
			ftp_write_access: cached_user.access != null ? !!cached_user.access.write : true,
			chroot: cached_user.chroot != null ? cached_user.chroot : false,
			home_directory: cached_user.home_directory || '/',
		};

		$scope.user = angular.copy(default_user);

		$scope.reset = function() {
			$scope.user = angular.copy(default_user);
		}

		$scope.add = function() {
			if (check_info(true)) {
				$http({
					method: 'POST',
					url: $scope.config.api.base_url + '/api/v1/users/',
					data: {
						email: $scope.user.email,
						fullname: $scope.user.fullname,
						password: $scope.user.password,
						phone: $scope.user.phone,
						is_active: $scope.user.is_active,
						is_admin: $scope.user.is_admin,
						chroot: $scope.user.chroot,
						ftp_read: $scope.user.ftp_read_access,
						ftp_write: $scope.user.ftp_write_access,
						homedirectory: $scope.user.home_directory
					},
					cache: false,
					responseType: "json",
				}).then(function(response) {
					$alert({
						content: $locale.translate('ftp.form.message.user_creation_success'),
						container: '#display-alert',
						type: 'info',
						show: true
					});

					$timeout(function redirect() {
						$location.path('/ftp');
					}, 5000);
				}, function(response) {
					if (response.status === 500 || response.status === -1) {
						message = $locale.translate('host.problem');
						type = 'danger';
					} else if (response.status === 401) {
						message = $locale.translate('ftp.form.message.add_user_failed');
						type = 'warning';
					} 

					if (message) {
						$alert({
							content: message,
							container: '#display-alert',
							type: type,
							html: true,
							show: true
						});
					}
				});
			}
		}

		function check_info(create_user) {
			var ok = true;

			if (!$scope.user.email || $scope.user.email.length == 0) {
				var message = $locale.translate('ftp.form.message.invalid_email');
				ok = false;

				if ($scope.user.email && $scope.user.email.length == 0)
					message = $locale.translate('ftp.form.message.missing_email');

				$alert({
					content: message,
					container: '#email_message',
					type: 'danger',
					html: false,
					show: true
				});
			}

			if (!$scope.user.fullname || $scope.user.fullname.length == 0) {
				var message = $locale.translate('ftp.form.message.missing_fullname');
				ok = false;

				$alert({
					content: message,
					container: '#fullname_message',
					type: 'danger',
					html: false,
					show: true
				});
			}

			if (!create_user && !$scope.user.password && !$scope.user.confirm_password) {
			} else if (!$scope.user.password || !$scope.user.confirm_password) {
				var message = $locale.translate('ftp.form.message.missing_password');
				ok = false;

				if (!$scope.user.password) {
					$alert({
						content: message,
						container: '#password_message',
						type: 'danger',
						html: false,
						show: true
					});
				}
				if (!$scope.user.confirm_password) {
					$alert({
						content: message,
						container: '#confirm_password_message',
						type: 'danger',
						html: false,
						show: true
					});
				}
			} else if ($scope.user.password != $scope.user.confirm_password) {
				var message = $locale.translate('ftp.form.message.password_missmatch');
				ok = false;

				$alert({
					content: message,
					container: '#password_message',
					type: 'danger',
					html: false,
					show: true
				});
			}

			if (!$scope.user.home_directory || $scope.user.home_directory.length == 0) {
				var message = $locale.translate('ftp.form.message.missing_home_directory');
				ok = false;

				$alert({
					content: message,
					container: '#home_directory_message',
					type: 'danger',
					html: false,
					show: true
				});
			}

			return ok;
		}

		$scope.update = function() {
			if (check_info(false)) {
				$http({
					method: 'PUT',
					url: $scope.config.api.base_url + '/api/v1/users/',
					data: {
						id: $scope.user.id,
						uid: $scope.user.uid,
						gid: $scope.user.gid,
						email: $scope.user.email,
						fullname: $scope.user.fullname,
						password: $scope.user.password || undefined,
						phone: $scope.user.phone,
						is_active: $scope.user.is_active,
						is_admin: $scope.user.is_admin,
						chroot: $scope.user.chroot,
						ftp_read: $scope.user.ftp_read_access,
						ftp_write: $scope.user.ftp_write_access,
						homedirectory: $scope.user.home_directory
					},
					cache: false,
					responseType: "json",
				}).then(function(response) {
					$alert({
						content: $locale.translate('ftp.form.message.user_update_success'),
						container: '#display-alert',
						type: 'info',
						show: true
					});

					$timeout(function redirect() {
						$location.path('/ftp');
					}, 5000);
				}, function(response) {
					if (response.status === 500 || response.status === -1) {
						message = $locale.translate('host.problem');
						type = 'danger';
					} else if (response.status === 401) {
						message = $locale.translate('ftp.form.message.update_user_failed');
						type = 'warning';
					} 

					if (message) {
						$alert({
							content: message,
							container: '#display-alert',
							type: type,
							html: true,
							show: true
						});
					}
				});
			}
		}
	}
]);

