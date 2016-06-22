<!DOCTYPE html>
<html lang="en" data-ng-app="Auth" ng-controller="AppCtrl">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!--<base href="http://blueholding.mx/fund/public/">-->
        <!--<link rel="shortcut icon" href="/app/client/images/favicon.ico">-->

        <title>Login</title>

        <!--Core CSS -->
        <link href="app/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

        <!--Ui-Block CSS-->
        <link href="app/bower_components/angular-block-ui/dist/angular-block-ui.min.css" rel="stylesheet">

        <!-- Theme style -->
        <link href="app/bower_components/AdminLTE/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>

        <!-- Custom styles for notification template -->
        <link href="app/css/ns-default.css" rel="stylesheet">
        <link href="app/css/ns-style-attached.css" rel="stylesheet">
    </head>

    <body class="hold-transition" ng-class="loginpanel ? 'login-page' : 'register-page'">
        <div  ui-view></div>

        <!--<div class="login-box" ui-view></div>-->
        <!-- jquery Js -->
        <script src="app/bower_components/AdminLTE/plugins/jQuery/jQuery-2.2.0.min.js" type="text/javascript"></script>
        <!-- Bootstrap 3.3.5 -->
        <script src="app/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

        <!-- Notification Js -->
        <script src="app/js/modernizr.custom.js"></script>
        <script src="app/js/classie.js"></script>
        <script src="app/js/notificationFx.js"></script>

        <!-- Angular Libraries -->
        <script src="app/bower_components/angular/angular.min.js"></script>
        <script src="app/bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>

        <!-- Social Login Library -->
        <script src="app/bower_components/satellizer/satellizer.js" type="text/javascript"></script>        

        <!--Ui-Block CSS-->
        <script src="app/bower_components/angular-block-ui/dist/angular-block-ui.min.js"></script>

        <!-- Auth Controller -->
        <script src="app/partials/auth/index.js"></script>

        <!-- Invitation Controller -->
        <script src="app/partials/invitation.js"></script>



    </body>

</html>
