<div class="theme-toggle">
    <label><?= $translations['choose_theme'] ?></label>
    <div class="theme-switch-wrapper">
        <input type="radio" name="theme" id="light" value="light" class="theme-radio" <?= isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'light' ? 'checked' : '' ?>>
        <label for="light" class="theme-switch">
            <i class="fas fa-sun"></i>
            <span><?= $translations['light'] ?></span>
        </label>

        <input type="radio" name="theme" id="dark" value="dark" class="theme-radio" <?= isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'checked' : '' ?>>
        <label for="dark" class="theme-switch">
            <i class="fas fa-moon"></i>
            <span><?= $translations['dark'] ?></span>
        </label>
    </div>
</div>


<div class="font-settings">
    <label for="font-size"><?= $translations['font_size'] ?></label>
    <input type="range" id="font-size" min="12" max="24" value="<?= $_SESSION['settings']['font_size'] ?? '16' ?>">
    <span id="font-size-value"><?= $_SESSION['settings']['font_size'] ?? '16' ?>px</span>
</div>


<div class="font-style">
    <label for="font-style"><?= $translations['font_style'] ?></label>
    <select id="font-style">
        <option value="sans-serif" <?= (!isset($_SESSION['settings']['font_style']) || $_SESSION['settings']['font_style'] === 'sans-serif') ? 'selected' : '' ?>><?= $translations['sans_serif'] ?></option>
        <option value="serif" <?= (isset($_SESSION['settings']['font_style']) && $_SESSION['settings']['font_style'] === 'serif') ? 'selected' : '' ?>><?= $translations['serif'] ?></option>
        <option value="monospace" <?= (isset($_SESSION['settings']['font_style']) && $_SESSION['settings']['font_style'] === 'monospace') ? 'selected' : '' ?>><?= $translations['monospace'] ?></option>
        <option value="georgia" <?= (isset($_SESSION['settings']['font_style']) && $_SESSION['settings']['font_style'] === 'georgia') ? 'selected' : '' ?>><?= $translations['georgia'] ?></option>
        <option value="verdana" <?= (isset($_SESSION['settings']['font_style']) && $_SESSION['settings']['font_style'] === 'verdana') ? 'selected' : '' ?>><?= $translations['verdana'] ?></option>
    </select>
</div>
<!--<button id="themeToggle" class="save-settings">Save Changes</button>-->
