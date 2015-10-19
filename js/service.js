var FTPAdmSrv = angular.module('ftpAdmFrontendService', []);

FTPAdmSrv.config(['$provide', function($provide) {
	$provide.decorator('$locale', ['$window', '$delegate', '$rootScope', '$http', function($window, $delegate, $root, $http) {
		var available_lang = ['en', 'fr'];
		var lang = $window.navigator.language in available_lang ? $window.navigator.language : 'en';
		var datas = {};

		function download() {
			$http.get('lang/' + lang + '.json').success(function(data) {
				datas = data;

				update_locale();

				$root.$broadcast('$localeChanged');
			});
		}
		download();

		var obj = {
			name: 'ftp-adm-frontend translate',

			get: function() {
				return lang;
			},

			get_available_languages: function() {
				return available_lang.clone();
			},

			set_lang: function(new_lang) {
				if (available_lang.indexOf(new_lang) < 0 || lang == new_lang)
					return;

				lang = new_lang;
				download();
			},

			translate: function(id) {
				if (id in datas)
					return datas[id];
				return id;
			}
		};

		var PLURAL_CATEGORY = {
			ZERO: "zero",
			ONE: "one",
			TWO: "two",
			FEW: "few",
			MANY: "many",
			OTHER: "other"
		};

		function update_locale() {
			function getDecimals(n) {
				n = n + '';
				var i = n.indexOf('.');
				return (i == -1) ? 0 : n.length - i - 1;
			}

			function getVF(n, opt_precision) {
				var v = opt_precision;

				if (undefined === v) {
					v = Math.min(getDecimals(n), 3);
				}

				var base = Math.pow(10, v);
				var f = ((n * base) | 0) % base;
				return {v: v, f: f};
			}

			switch (lang) {
				case 'en':
					obj.DATETIME_FORMATS = {
						AMPMS: ["AM", "PM"],
						DAY: ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
						ERANAMES: ["Before Christ", "Anno Domini"],
						ERAS: ["BC", "AD"],
						FIRSTDAYOFWEEK: 6,
						MONTH: ["January","February","March","April","May","June","July","August","September","October","November","December"],
						SHORTDAY: ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
						SHORTMONTH: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
						WEEKENDRANGE: [5, 6],
						fullDate: "EEEE, MMMM d, y",
						longDate: "MMMM d, y",
						medium: "MMM d, y h:mm:ss a",
						mediumDate: "MMM d, y",
						mediumTime: "h:mm:ss a",
						short: "M/d/yy h:mm a",
						shortDate: "M/d/yy",
						shortTime: "h:mm a"
					};
					obj.NUMBER_FORMATS = {
						CURRENCY_SYM: "$",
						DECIMAL_SEP: ".",
						GROUP_SEP: ",",
						PATTERNS: [
							{
								gSize: 3,
								lgSize: 3,
								maxFrac: 3,
								minFrac: 0,
								minInt: 1,
								negPre: "-",
								negSuf: "",
								posPre: "",
								posSuf: ""
							}, {
								gSize: 3,
								lgSize: 3,
								maxFrac: 2,
								minFrac: 2,
								minInt: 1,
								negPre: "-\u00a4",
								negSuf: "",
								posPre: "\u00a4",
								posSuf: ""
							}
						]
					};
					obj.pluralCat = function(n, opt_precision) {
						var i = n | 0;
						var vf = getVF(n, opt_precision);
						if (i == 1 && vf.v == 0)
							return PLURAL_CATEGORY.ONE;
						return PLURAL_CATEGORY.OTHER;
					};
					obj.id = "en";
					break;

				case 'fr':
					obj.DATETIME_FORMATS = {
						AMPMS: ["AM", "PM"],
						DAY: ["dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi"],
						ERANAMES: ["avant J\u00e9sus-Christ", "apr\u00e8s J\u00e9sus-Christ"],
						ERAS: ["av. J.-C.", "ap. J.-C."],
						FIRSTDAYOFWEEK: 0,
						MONTH: ["janvier", "f\u00e9vrier", "mars", "avril", "mai", "juin", "juillet", "ao\u00fbt", "septembre", "octobre", "novembre", "d\u00e9cembre"],
						SHORTDAY: ["dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam."],
						SHORTMONTH: ["janv.", "f\u00e9évr.", "mars", "avr.", "mai", "juin", "juil.", "ao\u00fbt", "sept.", "oct.", "nov.", "d\u00e9éc."],
						WEEKENDRANGE: [5, 6],
						fullDate: "EEEE d MMMM y",
						longDate: "d MMMM y",
						medium: "d MMM y HH:mm:ss",
						mediumDate: "d MMM y",
						mediumTime: "HH:mm:ss",
						short: "dd/MM/yy HH:mm",
						shortDate: "dd/MM/yy",
						shortTime: "HH:mm"
					};
					obj.NUMBER_FORMATS = {
						CURRENCY_SYM: "€",
						DECIMAL_SEP: ",",
						GROUP_SEP: " ",
						PATTERNS: [
							{
								gSize: 3,
								lgSize: 3,
								maxFrac: 3,
								minFrac: 0,
								minInt: 1,
								negPre: "-",
								negSuf: "",
								posPre: "",
								posSuf: ""
							}, {
								gSize: 3,
								lgSize: 3,
								maxFrac: 2,
								minFrac: 2,
								minInt: 1,
								negPre: "-",
								negSuf: "\u00a0\u00a4",
								posPre: "",
								posSuf: "\u00a0\u00a4"
							}
						]
					};
					obj.pluralCat = function(n, opt_precision) {
						var i = n | 0;
						if (i == 0 || i == 1)
							return PLURAL_CATEGORY.ONE;
						else
							return PLURAL_CATEGORY.OTHER;
					};
					obj.id = "fr";
					break;
			}
		}

		update_locale();

		return obj;
	}]);
}]);

