<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create article</title>
    <link rel="stylesheet" href="/css/profile/profile_header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/profile/profile_footer.css">
    <link rel="stylesheet" href="/css/profile/article_form.css">
    <link rel="stylesheet" href="/css/cover_image_preview.css">
    <link rel="stylesheet" href="/css/profile/markdown.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <link rel="stylesheet" href="/css/settings/themes.css">
    <link rel="stylesheet" href="/css/settings/font-style.css">
    <link rel="stylesheet" href="/css/settings/font-size.css">

</head>
<body
        class="<?=
        isset($_SESSION['settings']['theme']) && $_SESSION['settings']['theme'] === 'dark' ? 'dark-mode' : '';
        ?>
    <?= isset($_SESSION['settings']['font_style']) ? htmlspecialchars($_SESSION['settings']['font_style']) : 'sans-serif'; ?>"
        style="font-size: <?= isset($_SESSION['settings']['font_size']) ? htmlspecialchars($_SESSION['settings']['font_size']) : '16' ?>px;">

<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

 <form action="/create-article" method="POST" enctype="multipart/form-data" class="article-form" id="articleForm" autocomplete="off">

     <label for="title"><?= $translations['title']; ?></label>
     <input type="text" id="title" name="title" placeholder="Title" required value="<?= !empty($title) ? htmlspecialchars($title) : '' ?>"><br>

     <label for="markdown-editor"><?= $translations['content']; ?></label>
     <!-- Для вывода контента статьи -->
     <textarea id="markdown-editor" name="content" placeholder="Write your article here...">  <?= !empty($content) ? htmlspecialchars($content) : '' ?></textarea>


     <!-- Модальное окно для таблиц -->
     <div id="tableModal" class="modal" style="display: none;">
         <div class="modal-content">
             <span class="close">&times;</span>
             <h2><?= $translations['create_table']; ?></h2>
             <label for="rows"><?= $translations['rows']; ?></label>
             <input type="number" id="rows" min="1" max="20" value="2">
             <label for="columns"><?= $translations['columns']; ?></label>
             <input type="number" id="columns" min="1" max="20" value="2">
             <button id="insertTableBtn" type="button"><?= $translations['insert_table']; ?></button>
         </div>
     </div>

     <?php include __DIR__ . '/../partials/categories.php'; ?>


     <!-- Поле выбора сложности -->
     <label for="difficulty"><?= $translations['difficulty'] ?></label>
     <select id="difficulty" name="difficulty" required>
         <option value=""><?= $translations['select_difficulty'] ?></option>
         <option value="beginner" <?= ($article['difficulty'] ?? '') === 'beginner' ? 'selected' : '' ?>>
             <?= $translations['beginner'] ?>
         </option>
         <option value="intermediate" <?= ($article['difficulty'] ?? '') === 'intermediate' ? 'selected' : '' ?>>
             <?= $translations['intermediate'] ?>
         </option>
         <option value="advanced" <?= ($article['difficulty'] ?? '') === 'advanced' ? 'selected' : '' ?>>
             <?= $translations['advanced'] ?>
         </option>
     </select><br>

     <!-- Поле ввода предполагаемого времени чтения -->
     <label for="read_time"><?= $translations['estimated_read_time'] ?></label>
     <input type="number" id="read_time" name="read_time" min="1" value="<?= htmlspecialchars($article['read_time'] ?? '') ?>" required><br>

     <!-- Поле ввода тегов -->
     <label for="tags"><?= $translations['tags'] ?></label>
     <div class="tag-container">
         <input type="text" id="tags-input" placeholder="<?= $translations['tags_placeholder'] ?>"
                value="<?= htmlspecialchars(implode(', ', (isset($article['tags']) && is_array($article['tags'])) ? $article['tags'] : explode(',', $article['tags'] ?? ''))) ?>">
         <span id="tag-warning" style="color: red; display: none; margin-left: 10px;">
        <?= $translations['tag_warning'] ?>
    </span>
     </div>
     <input type="hidden" id="tags" name="tags" value="<?= htmlspecialchars(implode(', ', (isset($article['tags']) && is_array($article['tags'])) ? $article['tags'] : explode(',', $article['tags'] ?? ''))) ?>">

     <!-- Поле ввода ссылки на YouTube -->
     <label for="youtube_link"><?= $translations['youtube_link'] ?></label>
     <input type="url" id="youtube_link" name="youtube_link" placeholder="<?= $translations['youtube_link_placeholder'] ?>" value="<?= htmlspecialchars($article['youtube_link'] ?? '') ?>">

     <!-- Загрузка обложки -->
     <label for="cover_image"><?= $translations['cover_image'] ?></label>
     <div class="image-preview-container">
         <input type="file" id="cover_image" name="cover_image" accept="image/*" >
         <img id="cover_image_preview" class="cover-image-preview" src="<?= htmlspecialchars($article['cover_image'] ?? '') ?>"
              alt="<?= $translations['cover_image_preview'] ?>" style="display: <?= !empty($article['cover_image']) ? 'block' : 'none' ?>;">
         <button id="remove_button" class="remove-button" style="display: <?= !empty($article['cover_image']) ? 'block' : 'none' ?>;">×</button>
     </div>



     <!-- Переключатель публикации -->
     <div class="switch-container">
         <label for="publish"><?= $translations['publish'] ?></label>
         <label class="switch">
             <input type="checkbox" id="is_published" name="is_published" value="1" <?= isset($article['is_published']) && $article['is_published'] ? 'checked' : '' ?>>
             <span class="slider"></span>
         </label>
     </div>



     <input type="hidden" name="article_id" value="<?= $article['id'] ?? '' ?>">
     <?php if (isset($article['slug'])): ?>
         <input type="hidden" name="slug" value="<?= htmlspecialchars($article['slug']) ?>">
     <?php endif; ?>
     <!-- Остальные поля формы, такие как заголовок, контент и т.д. -->
     <button type="submit" class="custom-submit-button">
         <?= isset($article['slug']) ? $translations['save_changes'] : $translations['create_article'] ?>
     </button>
<!--     Для userId в джс-->
     <input type="hidden" id="user_id" value="<?=$userId?>">
     <input type="hidden" id="article_id" value="<?= $article['id'] ?? '' ?>">

 </form>
 <?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
<script src="/js/authorized_users/files_uploads/file_upload.js"></script>
<script src="/js/authorized_users/files_uploads/show_preview.js"></script>
<script src="/js/authorized_users/files_uploads/add_avatar.js"></script>
<script src="/js/authorized_users/files_uploads/form_actions.js"></script>
<script src="/js/authorized_users/articles/add_markdown.js"></script>
<script src="/js/authorized_users/menu.js"></script>
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
</body>
</html>
