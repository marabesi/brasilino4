'use strict';

var brasilino4 = angular.module('brasilino4', ['ionic', 'ngWebsocket']);

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