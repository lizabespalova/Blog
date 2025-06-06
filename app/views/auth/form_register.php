<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/authorization.css">
    <link rel="stylesheet" href="/css/google/google_services.css">
    <title>Register</title>
</head>
<body>
<section>
    <form action="/register" method="POST">
        <h1>Registration</h1>
        <div class="inputbox">
            <i class="bi bi-person"></i>
            <input type="text" name="login" required>
            <label>Username</label>
        </div>
        <div class="inputbox">
            <i class="bi bi-envelope"></i>
            <input type="email" name="email" required>
            <label>Email</label>
        </div>
        <div class="inputbox">
            <i class="bi bi-lock"></i>
            <input type="password" name="password" required>
            <label>Password</label>
        </div>
        <div class="forget">
<!--            <label>-->
<!--                <input type="checkbox" name="agree">I agree with rules-->
<!--            </label>-->
        </div>
        <button type="submit" name="register">Sign in</button>
        <div class="register">
            <p>Have an account?
                <a href="/login">Log in</a>
            </p>
        </div>
        <a href="/google-register" class="google-signin-button">
            <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo">
            <span>Sign in with Google</span>
        </a>

    </form>
</section>
<img id="women" src="/templates/images/women_at_the_computer.png" alt="Women">
</body>
</html>
