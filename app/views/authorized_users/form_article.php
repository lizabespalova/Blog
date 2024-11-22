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
    <link rel="stylesheet" href="/css/profile/markdown.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">

</head>
<body>
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

 <form action="/create-article" method="POST" enctype="multipart/form-data" class="article-form" id="articleForm">

     <label for="title">Title:</label>
     <input type="text" id="title" name="title" placeholder="Title" required value="<?= !empty($title) ? htmlspecialchars($title) : '' ?>"><br>

     <label for="markdown-editor">Content:</label>
     <!-- Для вывода контента статьи -->
     <textarea id="markdown-editor" name="content" placeholder="Write your article here..."><?= isset($content) ? htmlspecialchars($content) : '' ?></textarea>
     <!-- Модальное окно для таблиц -->
     <div id="tableModal" class="modal" style="display: none;">
         <div class="modal-content">
             <span class="close">&times;</span>
             <h2>Create table</h2>
             <label for="rows">Rows:</label>
             <input type="number" id="rows" min="1" max="20" value="2" >
             <label for="columns">Columns:</label>
             <input type="number" id="columns" min="1" max="20" value="2">
             <button id="insertTableBtn" type="button">Create table</button>
         </div>
     </div>


     <?php include __DIR__ . '/../partials/categories.php'; ?>


     <label for="difficulty">Difficulty:</label>
     <select id="difficulty" name="difficulty" required>
         <option value="">Select difficulty</option>
         <option value="beginner" <?= ($article['difficulty'] ?? '') === 'beginner' ? 'selected' : '' ?>>Beginner</option>
         <option value="intermediate" <?= ($article['difficulty'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
         <option value="advanced" <?= ($article['difficulty'] ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced</option>
     </select><br>


     <label for="read_time">Estimated Read Time (in minutes):</label>
     <input type="number" id="read_time" name="read_time" min="1" value="<?= htmlspecialchars($article['read_time'] ?? '') ?>" required><br>

     <label for="tags">Tags such as IT languages (comma-separated):</label>
     <div class="tag-container">
         <input type="text" id="tags-input" placeholder="Tag1, Tag2, Tag3" value="<?= htmlspecialchars(implode(', ', is_array($article['tags']) ? $article['tags'] : explode(',', $article['tags'] ?? ''))) ?>">
         <span id="tag-warning" style="color: red; display: none; margin-left: 10px;">❗ No more than 10 tags!</span>
     </div>
     <input type="hidden" id="tags" name="tags">

     <label for="youtube_link">YouTube Link:</label>
     <input type="url" id="youtube_link" name="youtube_link" placeholder="https://www.youtube.com/embed/xyz" value="<?= htmlspecialchars($article['youtube_link'] ?? '') ?>">

     <label for="cover_image">Upload cover image:</label>
     <div class="image-preview-container">
         <input type="file" id="cover_image" name="cover_image" accept="image/*">
         <img id="cover_image_preview" class="cover-image-preview" src="<?= htmlspecialchars($coverImage ?? '') ?>" alt="Cover Image Preview" style="display:<?= !empty($coverImage) ? 'block' : 'none' ?>;">
         <button id="remove_button" class="remove-button" style="display: <?= !empty($article['cover_image']) ? 'block' : 'none' ?>;">×</button>
     </div>


     <!--          <input type="file" name="article_images[]" id="fileInput" multiple>-->
     <input type="hidden" name="article_id" value="<?= $article['id'] ?? '' ?>">
     <?php if (isset($article['slug'])): ?>
         <input type="hidden" name="slug" value="<?= htmlspecialchars($article['slug']) ?>">
     <?php endif; ?>
     <!-- Остальные поля формы, такие как заголовок, контент и т.д. -->
     <button type="submit" class="custom-submit-button">
         <?= isset($article['slug']) ? 'Save Changes' : 'Create Article' ?>
     </button>

 </form>
 <?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
 <script src="/js/authorized_users/files_uploads/file_upload.js"></script>
 <script src="/js/authorized_users/files_uploads/show_preview.js"></script>
 <script src="/js/authorized_users/files_uploads/add_avatar.js"></script>
<script src="/js/authorized_users/files_uploads/form_actions.js"></script>
 <script src="/js/authorized_users/articles/add_markdown.js"></script>
 <script src="/js/authorized_users/menu.js"></script>
 <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

</body>
</html>
