<form id="preferences-settings-form">
    <!-- Настройки предпочтений -->
    <div class="form-group">
        <label for="language"><?= $translations['preferred_language'] ?></label>
        <div class="custom-select">
            <div class="selected-option" id="selected-language">
                <img class="flag" id="selected-flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/<?= $selectedFlag ?>.svg" alt="Flag">
                <?= ucfirst($language) ?>
            </div>

            <div class="options" id="language-options">
                <?php foreach ($flags as $lang => $flag) : ?>
                    <div class="option" data-value="<?= $lang ?>" data-flag="<?= $flag ?>">
                        <img class="flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/<?= $flag ?>.svg" alt="Flag">
                        <?= ucfirst($lang) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <input type="hidden" id="language" name="language" value="<?= $language ?>" />
        </div>
    </div>

    <div class="form-group">
        <label for="language"><?= $translations['timezone'] ?></label>
        <input type="text" id="timezone" name="timezone" placeholder="Enter timezone" required />
    </div>

    <button type="submit" class="save-settings"><?= $translations['save_changes'] ?></button>
</form>