var modules = ['ui.router', 'blockUI', 'satellizer'];
var Auth = angular.module('Auth', modules)

Auth.config(function ($stateProvider, $urlRouterProvider, blockUIConfig, $authProvider) {
    blockUIConfig.message = '';
    $urlRouterProvider
            .otherwise('/signin');

    $stateProvider
            .state('signin', {
                url: '/signin',
                templateUrl: 'app/partials/auth/login.html',
                controller: 'LoginCtrl'
            })
            .state('registration', {
                url: '/registration',
                templateUrl: 'app/partials/auth/registration.html',
                controller: 'RegistrationCtrl'
            })

    $authProvider.google({
        clientId: '703686035138-od835fb6tnjh8p31kq4qn29poo69s532.apps.googleusercontent.com',
    });
});

Auth.controller('AppCtrl', function ($scope, $rootScope, $auth, AuthServices) {
    $rootScope.login = false;
    $scope.setFlash = function (mtype, msg, time) {

        var type;
        switch (mtype) {

            case 's' :
                type = 'success';
                break;
            case 'e' :
                type = 'error';
                break;
            case 'w' :
                type = 'warning';
                break;
            case 'n' :
                type = 'notice';
                break;
        }

        var notification = new NotificationFx({
            message: '<i class="glyphicon"></i><p>' + msg + '</p>',
            layout: 'attached',
            effect: 'bouncyflip',
            type: type,
            ttl: time || 10000
        });
        notification.show();
    }


    $scope.authenticate = function (provider) {
        $auth.authenticate(provider).then(function (res) {
            if (res.data.data) {
                var d = res.data.data;
                $scope.googlereg = {
                    name: d.name,
                    email: d.email,
                    google: d.sub,
                };
                $(".googleReg").modal('show');
            } else {
                $scope.setFlash('s', 'Login successfully');
                window.location.reload();
            }

        });
    };

    $scope.googleRegistrationFn = function () {
        var obj = angular.copy($scope.googlereg);
        console.log(obj);
        AuthServices.doRegistration(obj).success(function (res) {
            if (res.flag) {
                window.location.reload();
                $scope.setFlash('s', res.message)
            } else {
                $scope.setFlash('e', res.message);
            }
        })
    }
})

Auth.controller('LoginCtrl', function ($scope, AuthServices, $timeout, $auth, $rootScope) {
    $rootScope.login = true;
    $scope.login = {};
    $scope.doLoginFn = function () {
        AuthServices.doLogin($scope.login).success(function (res) {
            if (res.flag) {
                $timeout(function () {
                    window.location.reload();
                }, 200);
            }
            else {
                $scope.setFlash('e', res.message)
            }
        })
    }



})

Auth.controller('RegistrationCtrl', function ($scope, AuthServices) {
    $scope.reg = {};
    $scope.doRegistrationFn = function () {
        var obj = angular.copy($scope.reg);
        if (obj.password != obj.cpass) {
            return $scope.setFlash('e', 'Password mismatch');
        }
        console.log(obj);
        AuthServices.doRegistration(obj).success(function (res) {
            if (res.flag) {
                window.location.reload();
                $scope.setFlash('s', res.message)
            } else {
                $scope.setFlash('e', res.message)
            }
        })
    }
})

Auth.factory('AuthServices', function ($http) {
    return{
        doLogin: function (obj) {
            return $http.post('auth/login', obj);
        },
        doRegistration: function (obj) {
            return $http.post('auth/signup', obj);
        },
        googleRegistration: function (obj) {
            return $http.post('auth/google-signup', obj);
        },
    }
});