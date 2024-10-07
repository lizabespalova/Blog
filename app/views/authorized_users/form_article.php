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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
</head>
<body>
<?php include __DIR__ . '/../../views/base/profile_header.php'; ?>

 <form action="/create-article" method="POST" enctype="multipart/form-data" class="article-form" id="articleForm">

     <label for="title">Title:</label>
     <input type="text" id="title" name="title" placeholder="Title" required><br>

     <label for="markdown-editor">Content:</label>
     <textarea id="markdown-editor" name="content" placeholder="Write your article here..."></textarea>
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

     <label for="cover_image">Upload cover image:</label>
     <div class="image-preview-container">
         <input type="file" id="cover_image" name="cover_image" accept="image/*">
         <img id="cover_image_preview" class="cover-image-preview" src="" alt="Cover Image Preview" style="display:none;">
         <button id="remove_button" class="remove-button" style="display:none;">×</button>
     </div>

     <label for="youtube_link">YouTube Link:</label>
     <input type="url" id="youtube_link" name="youtube_link" placeholder="https://www.youtube.com/embed/xyz">
     <input type="file" name="article_images[]" id="fileInput" multiple>
     <button type="submit" class="custom-submit-button">Save Article</button>
 </form>
 <?php include __DIR__ . '/../../views/base/profile_footer.php'; ?>
 <script src="/js/authorized_users/files_uploads/file_upload.js"></script>
 <script src="/js/authorized_users/files_uploads/show_preview.js"></script>
 <script src="/js/authorized_users/files_uploads/add_avatar.js"></script>
 <script src="/js/authorized_users/add_markdown.js"></script>
 <script src="/js/authorized_users/menu.js"></script>
 <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>

</body>
</html>
