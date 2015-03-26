'use strict';

var hackpi = angular.module('hackpi', ['ionic']);

hackpi.config(function ($stateProvider, $urlRouterProvider) {
    $stateProvider
        .state('hackpi', {
            url: '/hackpi',
            abstract: true,
            templateUrl: 'menu.html'
        })
        .state('hackpi.main', {
            url: '/main',
            views: {
                'menuContent': {
                    templateUrl: 'partials/main.html',
                    controller: 'MainController'
                }
            }
        })

    $urlRouterProvider.otherwise('/hackpi/main');
});