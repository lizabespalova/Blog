<!-- Search Form -->
<div class="search-section">
    <form action="" method="GET" class="search-form">
        <input
                type="text"
                name="query"
                placeholder="Type your search..."
                class="searching-input"
                value="<?= htmlspecialchars($_GET['query'] ?? '') ?>"
        >
        <button type="button" class="searching-button" id="searchButton">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>
<!--Атрибут action="" указывает, что форма будет отправлять запрос на текущую страницу (это работает для серверного поиска).-->
