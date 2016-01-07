var FTPAdmDirv = angular.module('ftpAdmFrontendDirv', []);

FTPAdmDirv.directive('fafTr', [ '$locale', function($tr) {
	function format_string(formatted, params) {
		for (var i = 0; i < params.length; i++) {
			var regexp = new RegExp('\\{' + i + '\\}', 'gi');
			formatted = formatted.replace(regexp, params[i]);
		}
		return formatted;
	}

	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
			var need_eval = 'fafTrEval' in attrs;
			var has_params = 'fafTrParams' in attrs;

			scope.$on('$localeChanged', function() {
					set_value();
			});

			if (need_eval) {
				scope.$watch(function() {
					var tr = $tr.translate(attrs.fafTr);
					return scope.$eval(tr);
				}, function() {
					set_value();
				});
			}

			attrs.$observe('fafTr', function(value) {
				set_value();
			});

			if (attrs.fafTr)
				set_value();

			function set_value() {
				var tr = $tr.translate(attrs.fafTr);

				if (need_eval)
					tr = scope.$eval(tr);

				if (has_params)
					tr = format_string(tr, scope.$eval(attrs.fafTrParams));

				if (attrs.fafTr.length > 0) {
					if ('placeholder' in attrs)
						element.prop('placeholder', tr);
					else if ('title' in attrs)
						element.prop('title', tr);
					else
						element.html(tr);
				}
			}
		},
	};
}]);

FTPAdmDirv.directive('fafSize', [ '$locale', function($tr) {
	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
			function convertSize(size) {
				var mult = 0;
				var type;
				while (size >= 1000 && mult < 5) {
					mult++;
					size /= 1000;
				}

				var width = 0;
				if (size < 10)
					width = 2;
				else if (size < 100)
					width = 1;

				switch (mult) {
					case 0:
						type = $tr.translate('unit.B');
						break;
					case 1:
						type = $tr.translate('unit.kiB');
						break;
					case 2:
						type = $tr.translate('unit.MiB');
						break;
					case 3:
						type = $tr.translate('unit.GiB');
						break;
					case 4:
						type = $tr.translate('unit.TiB');
						break;
					default:
						type = $tr.translate('unit.PiB');
						break;
				}

				element.html(size.toFixed(width) + type);
			}

			scope.$on('$localeChanged', function() {
				convertSize(attrs.fafSize);
			});

			attrs.$observe('fafSize', function(value) {
				convertSize(value);
			});

			if (attrs.fafSize)
				convertSize(attrs.fafSize);
		},
	};
}]);

