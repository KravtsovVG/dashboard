<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h2>Welcome {{$user}}</h2>
        <p>{{$msg}}</p>
        <div>
            To accept the invitation click here: <a href="{{ URL::to('/#/acceptinvite', $code) }}">Accept Invitation.</a>.<br/>
        </div>
        <p>If click doesn't work then copy the below link and past it new tab to activate your account.</p>
        <p>{{ URL::to('/#/acceptinvite', $code) }}</p>
        <p>Thank you</p>

    </body>
</html>
