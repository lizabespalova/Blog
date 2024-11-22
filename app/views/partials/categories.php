<label for="category">Category:</label>
<select id="category" name="category" class="filter-input">
    <option value="" disabled selected>Select a category</option>
    <option value="programming" <?= ($article['category'] ?? '') === 'programming' ? 'selected' : '' ?>>Programming</option>
    <option value="ai" <?= ($article['category'] ?? '') === 'ai' ? 'selected' : '' ?>>Artificial Intelligence</option>
    <option value="web_development" <?= ($article['category'] ?? '') === 'web_development' ? 'selected' : '' ?>>Web Development</option>
    <option value="data_science" <?= ($article['category'] ?? '') === 'data_science' ? 'selected' : '' ?>>Data Science</option>
    <option value="cyber_security" <?= ($article['category'] ?? '') === 'cyber_security' ? 'selected' : '' ?>>Cyber Security</option>
    <option value="cloud_computing" <?= ($article['category'] ?? '') === 'cloud_computing' ? 'selected' : '' ?>>Cloud Computing</option>
    <option value="machine_learning" <?= ($article['category'] ?? '') === 'machine_learning' ? 'selected' : '' ?>>Machine Learning</option>
    <option value="it_news" <?= ($article['category'] ?? '') === 'it_news' ? 'selected' : '' ?>>IT News</option>
</select>