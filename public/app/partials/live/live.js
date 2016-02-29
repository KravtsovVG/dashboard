/**
 * Created by CIS1 on 05-02-2015.
 */

var Live = angular.module('Live', []);
modules.push('Live');


Live.config(function ($stateProvider) {


    $stateProvider
            .state('app.live', {
                url: '/live',
                templateUrl: 'app/partials/live/live.html',
                controller: 'LiveCtrl',
            })
});


Live.controller('LiveCtrl', function ($scope, $state, $rootScope, LiveService) {
    $scope.live = {};
});

Live.service('LiveService', function ($http) {
    return{
    }
});