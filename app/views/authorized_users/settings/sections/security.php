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
<!--    <div class="form-group">-->
<!--        <label>-->
<!--            <input type="checkbox" name="two_factor_auth" id="two-factor-auth" />-->
<!--            Enable Two-Factor Authentication-->
<!--        </label>-->
<!--    </div>-->

    <!-- Кнопка сохранения -->
    <button type="submit" class="save-settings">Save Changes</button>
</form>

<!-- Управление активными сессиями -->
<div class="session-container">
    <!-- Current Session -->
    <?php if ($currentSession): ?>
        <div class="session-card current-session">
            <div class="session-header">
                <h4>Your Session (You are online)</h4>
                <span class="session-status online">🟢 Online</span>
            </div>
            <div class="session-body">
                <p><strong>Device:</strong> <?= htmlspecialchars($currentSession['user_agent']) ?></p>
                <p><strong>IP Address:</strong> <?= htmlspecialchars($currentSession['ip_address']) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($currentSession['location'] ?? 'Unknown') ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Other Active Sessions -->
    <?php if (!empty($otherSessions)): ?>
        <h3 class="session-list-title">Other Active Sessions</h3>
        <div class="session-list">
            <?php foreach ($otherSessions as $session): ?>
                <div class="session-card">
                    <div class="session-header">
                        <h4><?= htmlspecialchars($session['user_agent']) ?></h4>
                        <button class="logout-session" data-session-id="<?= $session['session_id'] ?>">Close</button>
                    </div>
                    <div class="session-body">
                        <p><strong>IP Address:</strong> <?= htmlspecialchars($session['ip_address']) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($session['location'] ?? 'Unknown') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-other-sessions">No other active sessions.</p>
    <?php endif; ?>
</div>


