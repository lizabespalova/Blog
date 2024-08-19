<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Description</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/edit_description.css">


</head>
<body>
<!-- Header Section -->
<?php include __DIR__ . '/../../views/authorized_users/profile_header.php'; ?>
<main>
    <div class="edit-container">
        <h1>Edit Description</h1>
        <form action="/submit_description" method="POST">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="10" cols="50" placeholder="Enter your description here..."></textarea>
            <br>
            <button type="submit" class="save-button">Save</button>
            <button type="button" class="back-button" onclick="window.location.href='/app/views/authorized_users/profile.php';">Back</button>

        </form>
    </div>
</main>
<!-- Footer Section -->
<?php include __DIR__ . '/../../views/authorized_users/profile_footer.php'; ?>
<script src="/js/menu.js"></script>
</body>
</html>