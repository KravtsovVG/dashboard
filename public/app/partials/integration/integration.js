/**
 * Created by CIS1 on 05-02-2015.
 */

var Integration = angular.module('Integration', []);
modules.push('Integration');


Integration.config(function ($stateProvider) {


    $stateProvider
            .state('app.integration', {
                url: '/integration',
                templateUrl: 'app/partials/integration/integration.html',
                controller: 'IntegrationCtrl',
            })
});


Integration.controller('IntegrationCtrl', function ($scope, $state, $rootScope, IntegrationService) {
    $scope.integration = {};
});

Integration.service('IntegrationService', function ($http) {
    return{       
    }
});