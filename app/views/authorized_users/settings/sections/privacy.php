<form id="privacy-settings-form">
    <div class="form-group">
        <label for="profile-visibility">Profile Visibility</label>
        <select id="profile-visibility" name="profile_visibility">
            <option value="public" <?php echo ($profileVisibility == 'public') ? 'selected' : ''; ?>>Public</option>
            <option value="private" <?php echo ($profileVisibility == 'private') ? 'selected' : ''; ?>>Private</option>
        </select>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="show_last_seen" id="show-last-seen" <?php echo ($showLastSeen) ? 'checked' : ''; ?> />
            Show last seen to others
        </label>
    </div>
</form>
