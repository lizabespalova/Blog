<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">-->
   <link rel="stylesheet" href="/css/authorization.css">
    <title>Login</title>
</head>
<body>
<section>
    <form action="/login" method="POST">
        <h1>Login</h1>
        <div class="inputbox">
            <i class="bi bi-person"></i>
            <input type="text" name="login" required>
            <label>Login</label>
        </div>
        <div class="inputbox">
            <i class="bi bi-lock"></i>
            <input type="password" name="password" required>
            <label>Password</label>
        </div>
        <div class="forget">
            <a href="/forget" class="forget-link">Forgot password?</a>
        </div>
        <button type="submit" name="submit">Log in</button>
        <div class="register">
            <p>Don't have an account?
                <a href="/register">Sign in</a>
            </p>
        </div>
    </form>
</section>
<img id="women" src="/templates/images/women_at_the_computer.png" alt="Women">
</body>
</html>
