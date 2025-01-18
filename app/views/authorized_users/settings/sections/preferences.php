<form id="preferences-settings-form">
    <!-- Настройки предпочтений -->
    <div class="form-group">
        <label for="language">Preferred Language</label>
        <div class="custom-select">
            <div class="selected-option" id="selected-language">
                <img class="flag" id="selected-flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/gb.svg" alt="Flag"> English
            </div>

            <div class="options" id="language-options">
                <div class="option" data-value="en" data-flag="gb">
                    <img class="flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/gb.svg" alt="Flag"> English
                </div>
                <div class="option" data-value="ru" data-flag="ru">
                    <img class="flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/ru.svg" alt="Flag"> Russian
                </div>
                <div class="option" data-value="de" data-flag="de">
                    <img class="flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/de.svg" alt="Flag"> German
                </div>
                <div class="option" data-value="ua" data-flag="ua">
                    <img class="flag" src="https://cdn.jsdelivr.net/gh/lipis/flag-icons/flags/4x3/ua.svg" alt="Flag"> Ukrainian
                </div>
            </div>


            <input type="hidden" id="language" name="language" value="en" />
        </div>
    </div>

    <div class="form-group">
        <label for="timezone">Timezone</label>
        <input type="text" id="timezone" name="timezone" placeholder="Enter timezone" required />
    </div>

    <button type="submit" class="save-settings">Save Changes</button>
</form>
