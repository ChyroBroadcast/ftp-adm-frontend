var app = angular.module('ftpAdmFrontend', ['ngAnimate', 'ngRoute', 'ngSanitize', 'mgcrea.ngStrap', 'ftpAdmFrontendDirv', 'ftpAdmFrontendService']);

app.config(['$routeProvider', function($routeProvider) {
	$routeProvider.when('/', {
		templateUrl: 'view/overview.html'
	});
	$routeProvider.otherwise({
		redirectTo: '/'
	});
}]);

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
			href: "#/logout"
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

