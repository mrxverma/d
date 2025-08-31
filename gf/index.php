<?php
// Chat UI
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aira</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/gf.css">
</head>
<body class="d-flex flex-column vh-100">
<header class="navbar navbar-light bg-light px-3">
  <div class="d-flex align-items-center">
    <img src="assets/logo.svg" alt="logo" width="32" height="32" class="me-2">
    <span class="fw-bold">Aira</span>
    <span class="ms-2 text-success" id="status-dot">●</span>
  </div>
  <button class="btn btn-sm btn-outline-secondary" id="themeToggle">Theme</button>
</header>
<main id="chat" class="flex-grow-1 overflow-auto p-3" aria-live="polite"></main>
<form id="composer" class="p-3 border-top">
  <div class="input-group">
    <textarea id="message" class="form-control" rows="1" placeholder="Type..." required></textarea>
    <button class="btn btn-primary" id="sendBtn" type="submit">Send</button>
  </div>
</form>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/gf.js"></script>
</body>
</html>
