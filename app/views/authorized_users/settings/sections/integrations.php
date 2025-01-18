<form id="integration-settings-form">
    <!-- Настройки интеграции -->
    <div class="form-group">
        <label for="integration-name">Integration Name</label>
        <input type="text" id="integration-name" name="integration_name" placeholder="Enter integration name" required />
    </div>

    <div class="form-group">
        <label for="api-key">API Key</label>
        <input type="text" id="api-key" name="api_key" placeholder="Enter API key" required />
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="enable_integration" id="enable-integration" />
            Enable Integration
        </label>
    </div>

    <!-- Кнопка сохранения -->
    <button type="submit" class="save-settings">Save Changes</button>
</form>
