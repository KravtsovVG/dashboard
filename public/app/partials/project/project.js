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
            })
            .state('app.project.view', {
                url: '/:id/view',
                templateUrl: 'app/partials/project/view.html',
                controller: 'ViewEditProjectCtrl',
                resolve: {
                    Project: function (ProjectService, $stateParams) {
                        return ProjectService.viewProject($stateParams.id);
                    },
                }
            })

            .state('app.projectsetting', {
                url: '/:id/setting',
                templateUrl: 'app/partials/project/setting.html',
                controller: 'SettingProjectCtrl',
                resolve: {
                    Project: function (ProjectService, $stateParams) {
                        return ProjectService.editProject($stateParams.id);
                    },
                }
            })
            .state('app.projectsetting.add', {
                url: '/add',
                templateUrl: 'app/partials/project/invite.html',
                controller: 'invitePeopleCtrl',
                resolve: {
                    Users: function (ProjectService) {
                        return ProjectService.getUser();
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

Project.controller('AddProjectCtrl', function ($scope, ProjectService, $timeout) {

    $scope.project = {};

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

Project.controller('ViewEditProjectCtrl', function ($scope, $timeout, Project) {

    $timeout(function () {
        $(".projectView").modal('show');
        $('.projectView').on('hidden.bs.modal', function () {
            $scope.goTo('app.project');
        })
    }, true);
    $scope.project = {};

    if (Project.data.flag) {
        $scope.project = Project.data.data;
        $scope.prousers = $scope.project.users;
        delete $scope.project.users;
    } else {
        $(".projectView").modal('hide');
    }
});

Project.controller('SettingProjectCtrl', function ($scope, ProjectService, Project, $stateParams) {
    $scope.project = {};

    if (Project.data.flag) {
        $scope.project = Project.data.data;
        $scope.prousers = $scope.project.users;
        delete $scope.project.users;
    }

    $scope.projectViewFn = function () {
        ProjectService.viewProject($stateParams.id).success(function (res) {
            if (res.flag) {
                $scope.project = res.data;
                $scope.prousers = $scope.project.users;
                delete $scope.project.users;
//                $scope.setFlash('s', res.message);
            } else {
                $scope.setFlash('e', res.message);
            }
        })
    }

    $scope.makeProjectOwnerFn = function (id) {
        var obj = {
            project_id: $stateParams.id * 1,
            id: id
        }
        console.log(obj);
        ProjectService.makeProjectOwner(obj).success(function (res) {
            if (res.flag) {
                $scope.setFlash('s', res.message);
                $scope.projectViewFn();
            } else {
                $scope.setFlash('s', res.message);
            }
        })
    }

    $scope.deleteProjectUserFn = function (id) {
        var obj = {
            project_id: $stateParams.id * 1,
            id: id
        }
        console.log(obj);
        ProjectService.deleteProjectUser(obj).success(function (res) {
            if (res.flag) {
                $scope.setFlash('s', res.message);
                $scope.projectViewFn();
            } else {
                $scope.setFlash('s', res.message);
            }
        })
    }

    $scope.changeNameFn = function (data, id) {
        var obj = {
            name: data
        }
        ProjectService.updateProject(obj, id).success(function (res) {
            if (res.flag) {
                $scope.setFlash('s', res.message);
            } else {
                $scope.setFlash('s', res.message);
            }
        })
    }

});

Project.controller('invitePeopleCtrl', function ($scope, $timeout, Users, ProjectService) {
    $timeout(function () {
        $(".invitePeople").modal('show');
        $('.invitePeople').on('hidden.bs.modal', function () {
            $scope.goTo('app.projectsetting');
        })
    }, true);
    $scope.pro = {
        name: $scope.project.name
    };
    $scope.users = []
    if (Users.data.flag) {
        $scope.users = Users.data.data;
        var users = []
        _.each($scope.users, function (usr) {
            if (!_.findWhere($scope.prousers, {id: usr.id})) {
                users.push(usr);
            }
        })
        $scope.users = users;
    } else {
        $(".invitePeople").modal('hide');
    }

    $scope.updateProjectFn = function () {
        var obj = angular.copy($scope.pro);
        console.log(obj);
        ProjectService.updateProject(obj, $scope.project.id).success(function (res) {
            if (res.flag) {
                $(".invitePeople").modal('hide');
                $scope.projectViewFn();
                $scope.setFlash('s', res.message);
            } else {
                $scope.setFlash('e', res.message);
            }
        })
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
        editProject: function (id) {
            return $http.get('project/' + id + '/edit');
        },
        deleteProjectUser: function (obj) {
            return $http.post('delete-project-user', obj);
        },
        makeProjectOwner: function (obj) {
            return $http.post('make-project-owner', obj);
        },
        updateProject: function (obj, id) {
            return $http.put('project/' + id, obj);
        }
    }
});