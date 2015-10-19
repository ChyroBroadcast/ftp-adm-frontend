var app = angular.module('ftpAdmFrontend', ['ngAnimate', 'ngSanitize', 'mgcrea.ngStrap']);

app.controller('LanguageController', [ '$scope',
	function($scope) {
		$scope.languagesList = [{
			text: "<span class=\"flag-icon flag-icon-gb\"></span> English",
			href: "#/en"
		}, {
			divider: true
		}, {
			text: "<span class=\"flag-icon flag-icon-fr\"></span> Français",
			href: "#/fr"
		}];

	}
]);

app.controller('AccountMenuController', [ '$scope',
	function($scope) {
		$scope.accountMenu = [{
			text: "Account",
			href: "#/account"
		}, {
			divider: true
		}, {
			text: "Logout",
			href: "#/logout"
		}];
	}
]);

