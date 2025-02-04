<form id="personal-data-form">
    <label for="login">
        <?php echo $translations['login']; ?>
        <span class="info-icon" data-info="<?php echo $translations['current_login'] . htmlspecialchars($currentLogin); ?>">&#9432;</span>
    </label>
    <input type="text" id="login" name="login" placeholder="<?php echo $translations['enter_new_login']; ?>" />

    <label for="email">
        <?php echo $translations['email']; ?>
        <span class="info-icon" data-info="<?php echo $translations['current_email'] . htmlspecialchars($currentEmail); ?>">&#9432;</span>
    </label>
    <input type="email" id="email" name="email" placeholder="<?php echo $translations['enter_new_email']; ?>" />

    <label for="password"><?php echo $translations['password']; ?></label>
    <input type="password" id="password" name="password" placeholder="<?php echo $translations['password_placeholder']; ?>" required />

    <button type="submit" class="save-settings"><?php echo $translations['save_changes']; ?></button>
</form>
