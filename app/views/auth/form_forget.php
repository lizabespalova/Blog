<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/css/authorization.css">
    <title>Forget</title>
</head>
<body>
<section>
    <form action="/forget" method="POST">
        <h1>Forget</h1>
        <p>Change the password via email</p>

        <div class="inputbox">
            <i class="bi bi-envelope"></i>
            <input type="email" name="email" required>
            <label>Email</label>
        </div>
        <button type="submit" name="reset">Send email</button>
    </form>
</section>
<img id="women" src="/templates/images/women_at_the_computer.png" alt="Women">
</body>
</html>
