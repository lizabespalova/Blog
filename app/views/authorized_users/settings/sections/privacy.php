<form id="privacy-settings-form">
    <div class="form-group">
        <label for="profile-visibility"><?= $translations['profile_visibility'] ?></label>
        <select id="profile-visibility" name="profile_visibility">
            <option value="public" <?php echo ($profileVisibility == 'public') ? 'selected' : ''; ?>>
                <?= $translations['public'] ?>
            </option>
            <option value="private" <?php echo ($profileVisibility == 'private') ? 'selected' : ''; ?>>
                <?= $translations['private'] ?>
            </option>
        </select>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="show_last_seen" id="show-last-seen" <?php echo ($showLastSeen) ? 'checked' : ''; ?> />
            <?= $translations['show_last_seen'] ?>
        </label>
    </div>
</form>
