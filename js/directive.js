var FTPAdmDirv = angular.module('ftpAdmFrontendDirv', []);

FTPAdmDirv.directive('fafTr', [ '$locale', function($tr) {
        function set_value(scope, elt, attrs, need_eval) {
                var tr = $tr.translate(attrs.fafTr);

                if (need_eval)
                        tr = scope.$eval(tr);

                if (attrs.fafTr.length > 0) {
                        if ('placeholder' in attrs)
                                elt.prop('placeholder', tr);
                        else if ('title' in attrs)
                                elt.prop('title', tr);
                        else
                                elt.html(tr);
                }
        }

        return {
                restrict: 'A',
                link: function(scope, element, attrs) {
                        var eval = 'fafTrEval' in attrs;

                        scope.$on('$localeChanged', function() {
                                set_value(scope, element, attrs, eval);
                        });

                        if (eval) {
                                scope.$watch(function() {
                                        var tr = $tr.translate(attrs.fafTr);
                                        return scope.$eval(tr);
                                }, function() {
                                        set_value(scope, element, attrs, eval);
                                });
                        }

                        attrs.$observe('fafTr', function(value) {
                                set_value(scope, element, attrs, eval);
                        });

                        if (attrs.fafTr)
                                set_value(scope, element, attrs, eval);
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
				while (size >= 1024 && mult < 5) {
					mult++;
					size /= 1024;
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

