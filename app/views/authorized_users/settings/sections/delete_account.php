<div class="delete-account-section">
    <p><?= $translations['delete_account_description'] ?></p>

    <form id="delete-account-form" method="POST">
        <div class="form-group">
            <label for="password"><?= $translations['confirm_password'] ?></label>
            <input type="password" id="password" name="password" required placeholder="<?= $translations['enter_password'] ?>" />
        </div>

        <button type="submit" class="save-settings"><?= $translations['delete_account_button'] ?></button>
    </form>

    <p><?= $translations['delete_account_warning'] ?></p>
</div>
