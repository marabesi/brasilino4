'use strict';

var brasilino4 = angular.module('brasilino4', ['ionic', 'ngWebsocket', 'ngTouch']);

brasilino4.config(function ($stateProvider, $urlRouterProvider) {
    $stateProvider
        .state('brasilino4', {
            url: '/brasilino4',
            abstract: true,
            templateUrl: 'menu.html'
        })
        .state('brasilino4.main', {
            url: '/main',
            views: {
                'menuContent': {
                    templateUrl: 'partials/main.html',
                    controller: 'MainController'
                }
            }
        })

    $urlRouterProvider.otherwise('/brasilino4/main');
});

brasilino4.filter('trusted', ['$sce', function ($sce) {
    return function(url) {
        return $sce.trustAsResourceUrl(url);
    };
}]);

brasilino4.value('brasilino4Settings', {
  ipCamera: '192.168.0.105:8181/camera.php',
  ipSocket: '192.168.0.105:9002',
});
