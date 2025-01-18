<form id="security-settings-form">
    <!-- Изменение пароля -->
    <div class="form-group">
        <label for="current-password">Current Password</label>
        <input type="password" id="current-password" name="current_password" placeholder="Enter current password" required />
    </div>

    <div class="form-group">
        <label for="new-password">New Password</label>
        <input type="password" id="new-password" name="new_password" placeholder="Enter new password" required />
    </div>

    <div class="form-group">
        <label for="confirm-password">Confirm New Password</label>
        <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm new password" required />
    </div>

    <!-- Двухфакторная аутентификация -->
    <div class="form-group">
        <label>
            <input type="checkbox" name="two_factor_auth" id="two-factor-auth" />
            Enable Two-Factor Authentication
        </label>
    </div>

    <!-- Кнопка сохранения -->
    <button type="submit" class="save-settings">Save Changes</button>
</form>

<!-- Управление активными сессиями -->
<div class="active-sessions">
    <h3>Active Sessions</h3>
    <p>You are logged in on the following devices:</p>
    <ul>
        <li>Device: Chrome on Windows 10 <button class="logout-session">Log Out</button></li>
        <li>Device: Safari on iPhone <button class="logout-session">Log Out</button></li>
    </ul>
</div>
