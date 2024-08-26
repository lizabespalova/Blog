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
<?php include __DIR__ . '/profile_header.php'; ?>

<main>
    <div class="edit-container">
        <h1>Edit Description</h1>
        <form action="" method="POST">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="10" cols="50" placeholder="Enter your description here..."></textarea>
            <br>
        </form>
        <div class="form-actions">
            <button type="submit" class="save-button">Save</button>
            <form action="/profile" method="GET" style="margin: 0;">
                <button type="submit" class="back-button">Back</button>
            </form>
        </div>
    </div>
</main>

<!-- Footer Section -->
<?php include __DIR__ . '/profile_footer.php'; ?>
<script src="/js/authorized_users/menu.js"></script>
</body>
</html>
