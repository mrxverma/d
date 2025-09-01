<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shorts Viewer</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <h1>Shorts</h1>
    <div class="nav-buttons">
        <button id="prev-btn" aria-label="Previous">↑</button>
        <button id="next-btn" aria-label="Next">↓</button>
    </div>
</header>
<main id="feed" aria-live="polite"></main>
<div id="sentinel"></div>
<template id="slide-template">
    <section class="slide" data-id="">
        <div class="video-wrap"></div>
        <div class="ui">
            <button class="volume-btn" aria-label="Toggle sound">🔇</button>
            <button class="like-btn" aria-label="Like"><span class="heart">❤️</span> <span class="like-count"></span></button>
            <button class="comment-btn" aria-label="Comments">💬</button>
        </div>
        <div class="comments" data-open="false" aria-expanded="false">
            <div class="comments-header">
                <span>Comments</span>
                <button class="comments-close" aria-label="Close">×</button>
            </div>
            <ul class="comment-list"></ul>
            <form class="comment-form">
                <input type="text" name="username" maxlength="40" placeholder="Name" required>
                <textarea name="comment" maxlength="300" placeholder="Comment" required></textarea>
                <button type="submit">Post</button>
            </form>
        </div>
    </section>
</template>
<script>
const API = {
    shorts: 'api/shorts_list.php',
    like: 'api/like.php',
    comments: 'api/comments_list.php',
    commentAdd: 'api/comment_add.php'
};
</script>
<script src="assets/app.js"></script>
</body>
</html>
