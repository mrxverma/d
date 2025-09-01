<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shorts Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="bg-dark text-light">
<header class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Shorts</span>
        <div>
            <button id="prev-btn" class="btn btn-outline-light me-2" aria-label="Previous">↑</button>
            <button id="next-btn" class="btn btn-outline-light" aria-label="Next">↓</button>
        </div>
    </div>
</header>
<main id="feed" class="pt-5" aria-live="polite"></main>
<div id="sentinel"></div>
<template id="slide-template">
    <section class="slide" data-id="">
        <div class="video-wrap"></div>
        <div class="ui">
            <button class="volume-btn btn btn-light mb-2" aria-label="Toggle sound">🔇</button>
            <button class="like-btn btn btn-light mb-2" aria-label="Like"><span class="heart">❤️</span> <span class="like-count"></span></button>
            <button class="comment-btn btn btn-light" data-bs-toggle="offcanvas" data-bs-target="#commentsCanvas" aria-label="Comments">💬</button>
        </div>
    </section>
</template>

<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="commentsCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Comments</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="comment-list list-unstyled mb-3"></ul>
    <form class="comment-form">
      <div class="mb-3">
        <input type="text" name="username" maxlength="40" class="form-control" placeholder="Name" required>
      </div>
      <div class="mb-3">
        <textarea name="comment" maxlength="300" class="form-control" placeholder="Comment" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Post</button>
    </form>
  </div>
</div>

<script>
const API = {
    shorts: 'api/shorts_list.php',
    like: 'api/like.php',
    comments: 'api/comments_list.php',
    commentAdd: 'api/comment_add.php'
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/app.js"></script>
</body>
</html>
