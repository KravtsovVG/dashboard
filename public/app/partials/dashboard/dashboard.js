/**
 * Created by CIS1 on 05-02-2015.
 */

var Dashboard = angular.module('Dashboard', []);
modules.push('Dashboard');


Dashboard.config(function ($stateProvider) {


    $stateProvider
            .state('app.dashboard', {
                url: '/dashboard',
                templateUrl: 'app/partials/dashboard/dashboard.html',
                controller: 'DashboardCtrl',
                resolve: {
                    Events: function (DashboardService) {
                        return DashboardService.getRecentEvent();
                    },
                }
            })
});


Dashboard.controller('DashboardCtrl', function ($scope, Events) {
    $scope.dashboard = {};
    $scope.recentEvents = []
    if (Events.data) {
        $scope.recentEvents = Events.data.data;
    }
});

Dashboard.service('DashboardService', function ($http) {
    return{
        getRecentEvent: function () {
            return $http.get('/get-recent-event');
        },
    }
});