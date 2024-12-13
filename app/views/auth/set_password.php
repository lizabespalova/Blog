<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Pending</title>
    <link rel="stylesheet" href="/css/authorization.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
<form action="/set_password" method="POST">
    <div class="inputbox">
        <input type="password" id="password" name="password" required>
        <label>Password</label>
        <i class="toggle-password bi bi-eye-slash" onclick="togglePasswordVisibility('password', this)"></i>
    </div>
    <div class="inputbox">
        <input type="password" id="password_confirmation" name="password_confirmation" required>
        <label for="password_confirmation">Confirm your password:</label>
        <i class="toggle-password bi bi-eye-slash" onclick="togglePasswordVisibility('password_confirmation', this)"></i>
    </div>

    <button type="submit">Save password</button>
</form>
<img id="women" src="/templates/images/women_at_the_computer.png" alt="Women">
<script src="/js/auth/toogle_eye_visibility.js"></script>

</body>
</html>
