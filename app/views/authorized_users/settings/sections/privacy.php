<form id="privacy-settings-form">
    <div class="form-group">
        <label for="profile-visibility">Profile Visibility</label>
        <select id="profile-visibility" name="profile_visibility">
            <option value="public">Public</option>
            <option value="friends">Friends Only</option>
            <option value="private">Private</option>
        </select>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="allow_emails" id="allow-emails" checked />
            Allow receiving promotional emails
        </label>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="show_last_seen" id="show-last-seen" />
            Show last seen to others
        </label>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="data_sharing" id="data-sharing" />
            Allow data sharing with partners
        </label>
    </div>

    <button type="submit" class="save-settings">Save Changes</button>
</form>

