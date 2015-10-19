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
