/**
 * Created by Gaurnag Ghinaiya
 */

var Invitation = angular.module('Invitation', []);
modules.push('Invitation');


Invitation.config(function ($stateProvider) {


    $stateProvider
            .state('acceptinvite', {
                url: '/acceptinvite/:code',
                templateUrl: 'app/partials/acceptinvite.html',
                controller: 'InvitationCtrl',
                resolve: {
                    Invitation: function (InvitationService, $stateParams) {
                        return InvitationService.acceptInvitation($stateParams.code);
                    },
                }
            })
});


Invitation.controller('InvitationCtrl', function ($scope, Invitation) {
    if (Invitation.data.flag) {
        $scope.message = Invitation.data.message
    } else {
        $scope.message = Invitation.data.message
    }

    $scope.clickFn = function () {
        window.location = '/';
    }
});

Invitation.service('InvitationService', function ($http) {
    return{
        acceptInvitation: function (code) {
            return $http.get('/acceptInvitation/' + code);
        },
    }
});