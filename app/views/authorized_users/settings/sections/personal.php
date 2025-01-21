<form id="personal-data-form">
    <label for="login">
        Login
        <span class="info-icon" data-info="Current login: <?php echo htmlspecialchars($currentLogin); ?>">&#9432;</span>
    </label>
    <input type="text" id="login" name="login" placeholder="Enter a new login" />

    <label for="email">
        Email
        <span class="info-icon" data-info="Current email: <?php echo htmlspecialchars($currentEmail); ?>">&#9432;</span>
    </label>
    <input type="email" id="email" name="email" placeholder="Enter your new email" />

    <label for="password">Password</label>
    <input type="password" id="password" name="password" placeholder="Enter your current password, in order to change settings" required />

    <button type="submit" class="save-settings">Save Changes</button>
</form>
