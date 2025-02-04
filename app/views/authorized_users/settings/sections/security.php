<form id="security-settings-form">
    <!-- Изменение пароля -->
    <div class="form-group">
        <label for="current-password"><?= $translations['current_password'] ?></label>
        <input type="password" id="current-password" name="current_password" placeholder="<?= $translations['placeholder_current_password'] ?>" required />
    </div>

    <div class="form-group">
        <label for="new-password"><?= $translations['new_password'] ?></label>
        <input type="password" id="new-password" name="new_password" placeholder="<?= $translations['placeholder_new_password'] ?>" required />
    </div>

    <div class="form-group">
        <label for="confirm-password"><?= $translations['confirm_password'] ?></label>
        <input type="password" id="confirm-password" name="confirm_password" placeholder="<?= $translations['placeholder_confirm_password'] ?>" required />
    </div>



    <!-- Кнопка сохранения -->
    <button type="submit" class="save-settings"><?= $translations['save_changes'] ?></button>
</form>

<!-- Управление активными сессиями -->
<div class="session-container">
    <!-- Current Session -->
    <?php if ($currentSession): ?>
        <div class="session-card current-session">
            <div class="session-header">
                <h4><?= $translations['your_session'] ?></h4>
                <span class="session-status online"><?= $translations['online'] ?></span>
            </div>
            <div class="session-body">
                <p><strong><?= $translations['device'] ?>:</strong> <?= htmlspecialchars($currentSession['user_agent']) ?></p>
                <p><strong><?= $translations['ip_address'] ?>:</strong> <?= htmlspecialchars($currentSession['ip_address']) ?></p>
                <p><strong><?= $translations['location'] ?>:</strong> <?= htmlspecialchars($currentSession['location'] ?? $translations['unknown']) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Other Active Sessions -->
    <?php if (!empty($otherSessions)): ?>
        <h3 class="session-list-title"><?= $translations['other_active_sessions'] ?></h3>
        <div class="session-list">
            <?php foreach ($otherSessions as $session): ?>
                <div class="session-card">
                    <div class="session-header">
                        <h4><?= htmlspecialchars($session['user_agent']) ?></h4>
                        <button class="logout-session" data-session-id="<?= $session['session_id'] ?>"><?= $translations['close_session'] ?></button>
                    </div>
                    <div class="session-body">
                        <p><strong><?= $translations['ip_address'] ?>:</strong> <?= htmlspecialchars($session['ip_address']) ?></p>
                        <p><strong><?= $translations['location'] ?>:</strong> <?= htmlspecialchars($session['location'] ?? $translations['unknown']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="no-other-sessions"><?= $translations['no_other_sessions'] ?></p>
    <?php endif; ?>
</div>


