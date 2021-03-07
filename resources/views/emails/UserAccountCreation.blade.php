<!DOCTYPE html>
<html>
<head>
    <title>Account Credentials</title>
</head>
<body>
    <h4>Welcome {{ucwords($details['name'])}}</h4>
    <p>Your credentials to access the account is,</p>
    <p><strong>Login Id: {{$details['email']}}</strong></p>
    <p><strong>Password: {{$details['password']}}</strong></p>

    <p><i>Please dont share your credentials with anyone</i></p>


<p>Thank you</p>
</body>
</html>
