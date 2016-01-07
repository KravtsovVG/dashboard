/**
 * Created by Garuang Ghinaiya on 01-01-2016.
 */

var Project = angular.module('Project', []);
modules.push('Project');
Project.config(function ($stateProvider) {

    $stateProvider
            .state('app.project', {
                url: '/project',
                templateUrl: 'app/partials/project/project.html',
                controller: 'ProjectCtrl',
                resolve: {
                    Projects: function (ProjectService) {
                        return ProjectService.getProject();
                    },
                }
            })
            .state('app.project.add', {
                url: '/add',
                templateUrl: 'app/partials/project/add.html',
                controller: 'AddProjectCtrl',
                resolve: {
                    Users: function (ProjectService) {
                        return ProjectService.getUser();
                    },
                }
            })
            .state('app.project.view', {
                url: '/:id/view',
                templateUrl: 'app/partials/project/view.html',
                controller: 'ViewEditProjectCtrl',
                resolve: {
                    Users: function (ProjectService) {
                        return ProjectService.getUser();
                    },
                    Project: function (ProjectService, $stateParams) {
                        return ProjectService.viewProject($stateParams.id);
                    },
                }
            })
});

Project.controller('ProjectCtrl', function ($scope, ProjectService, Projects) {
    $scope.projects = [];
    if (Projects.data.flag) {
        $scope.projects = Projects.data.data;
    }

    $scope.getProjectFn = function () {
        ProjectService.getProject().success(function (res) {
            if (res.flag) {
                $scope.projects = res.data;
            }
        })
    }

});

Project.controller('AddProjectCtrl', function ($scope, ProjectService, $timeout, Users) {

    $scope.project = {};
    $scope.users = [];
    if (Users.data.flag) {
        $scope.users = Users.data.data;
    }

    $timeout(function () {
        $(".projectAdd").modal('show');
        $('.projectAdd').on('hidden.bs.modal', function () {
            $scope.goTo('app.project');
        })
    }, true);
    $scope.addProjectFn = function () {
        var obj = angular.copy($scope.project);
        ProjectService.addProject(obj).success(function (res) {
            if (res.flag) {
                $scope.projects.push(res.data);
                $scope.setFlash('s', res.message);
                $(".projectAdd").modal('hide');
            } else {
                $scope.setFlash('e', res.message);
            }
        })
    }
});

Project.controller('ViewEditProjectCtrl', function ($scope, ProjectService, Users, $timeout, Project, $stateParams) {

    $timeout(function () {
        $(".projectView").modal('show');
        $('.projectView').on('hidden.bs.modal', function () {
            $scope.goTo('app.project');
        })
    }, true);

    $scope.users = [];
    $scope.project = {};

    if (Users.data.flag) {
        $scope.users = Users.data.data;
    }
    $scope.userListFn = function () {
        $scope.users = Users.data.data;
        var users = []
        _.each($scope.users, function (usr) {
            if (!_.findWhere($scope.prousers, {id: usr.id})) {
                users.push(usr);
            }
        })
        $scope.users = users;
        console.log($scope.prousers);
        console.log($scope.users);
    }

    if (Project.data.flag) {
        $scope.project = Project.data.data;
        $scope.prousers = $scope.project.users;
        delete $scope.project.users;
        $scope.userListFn();
    } else {
        $(".projectView").modal('hide');
    }

    $scope.viewProjectFn = function () {
        ProjectService.viewProject($stateParams.id).success(function (res) {
            if (res.flag) {
                $scope.project = res.data;
                $scope.prousers = $scope.project.users;
                delete $scope.project.users;
                $scope.userListFn();
            } else {
                $(".projectView").modal('hide');
            }
        })
    }

    $scope.updateProjectFn = function () {
        var obj = angular.copy($scope.project);
        delete obj.owner;
        console.log(obj);
        ProjectService.updateProject(obj, obj.id).success(function (res) {
            if (res.flag) {
                $(".projectView").modal('hide');
                $scope.setFlash('s', res.message);
                $scope.getProjectFn();
            } else {
                $scope.setFlash('e', res.message);
            }
        })
    }

    $scope.deleteProjectUserFn = function (id, proId) {
        if (id) {
            var obj = {
                id: id,
                project_id: proId,
            }
            ProjectService.deleteProjectUser(obj).success(function (res) {
                if (res.flag) {
                    _.each($scope.prousers, function (u, index) {
                        if (u && u.pid == id) {
                            $scope.prousers.splice(index, 1)
                        }
                    })
                    $scope.userListFn();
                    $scope.setFlash('s', res.message);
                } else {
                    $scope.setFlash('e', res.message);
                }
            })
        } else {
            $scope.setFlash('e', 'you can not remove this user');
        }
    }

    $scope.makeProjectOwnerFn = function (id, proId) {
        if (id) {
            var obj = {
                id: id,
                project_id: proId,
            }
            console.log(obj);
            ProjectService.makeProjectOwner(obj).success(function (res) {
                if (res.flag) {
                    $scope.viewProjectFn();
                    $scope.setFlash('s', res.message);
                } else {
                    $scope.setFlash('e', res.message);
                }
            })
        } else {
            $scope.setFlash('e', 'you can not authorize to do it.');
        }
    }


});

Project.service('ProjectService', function ($http) {
    return{
        getProject: function () {
            return $http.get('project');
        },
        getUser: function () {
            return $http.get('user');
        },
        addProject: function (obj) {
            return $http.post('project', obj);
        },
        viewProject: function (id) {
            return $http.get('project/' + id);
        },
        deleteProjectUser: function (obj) {
            return $http.post('delete-project-user', obj);
        },
        makeProjectOwner: function (obj) {
            return $http.post('make-project-owner', obj);
        },
        updateProject: function (obj, id) {
            return $http.put('project/' + id, obj);
        },
    }
});