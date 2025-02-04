<label for="category"><?= $translations['category'] ?></label>
<select id="category" name="category" class="filter-input">
    <option value="" disabled selected><?= $translations['select_category'] ?></option>
    <option value="programming" <?= ($article['category'] ?? '') === 'programming' ? 'selected' : '' ?>>
        <?= $translations['programming'] ?>
    </option>
    <option value="ai" <?= ($article['category'] ?? '') === 'ai' ? 'selected' : '' ?>>
        <?= $translations['ai'] ?>
    </option>
    <option value="web_development" <?= ($article['category'] ?? '') === 'web_development' ? 'selected' : '' ?>>
        <?= $translations['web_development'] ?>
    </option>
    <option value="data_science" <?= ($article['category'] ?? '') === 'data_science' ? 'selected' : '' ?>>
        <?= $translations['data_science'] ?>
    </option>
    <option value="cyber_security" <?= ($article['category'] ?? '') === 'cyber_security' ? 'selected' : '' ?>>
        <?= $translations['cyber_security'] ?>
    </option>
    <option value="cloud_computing" <?= ($article['category'] ?? '') === 'cloud_computing' ? 'selected' : '' ?>>
        <?= $translations['cloud_computing'] ?>
    </option>
    <option value="machine_learning" <?= ($article['category'] ?? '') === 'machine_learning' ? 'selected' : '' ?>>
        <?= $translations['machine_learning'] ?>
    </option>
    <option value="it_news" <?= ($article['category'] ?? '') === 'it_news' ? 'selected' : '' ?>>
        <?= $translations['it_news'] ?>
    </option>
</select>
