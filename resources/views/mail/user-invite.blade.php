<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Invitation</title>
</head>

<body>
    <h1>Hello, {{ $name }}</h1>
    <p>You have been invited to join our platform.</p>
    <ul>
        <li><strong>Email:</strong> {{ $email }}</li>
        @if(!empty($password))
            <li><strong>Password:</strong> {{ $password }}</li>
        @else
            <li><strong>Password:</strong> Use your existing account password or reset it if needed.</li>
        @endif
    </ul>
    <p>You can log in using the following link:</p>
    <a href="{{ $loginLink }}">{{ $loginLink }}</a>
    <p>We look forward to having you on board!</p>
</body>

</html>