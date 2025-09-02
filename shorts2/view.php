<?php
require_once 'db.php';

function extract_video_id($url) {
    if (preg_match('/(?:v=|\.be\/|shorts\/)([\w-]{11})/', $url, $m)) {
        return $m[1];
    }
    return '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['videoUrl'] ?? '');
    if ($url) {
        $stmt = $conn->prepare('INSERT INTO shorts (link, likes_count, time) VALUES (?, 0, NOW())');
        $stmt->bind_param('s', $url);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: view.php');
    exit;
}

$rows = [];
$result = $conn->query('SELECT id, link FROM shorts ORDER BY time DESC');
if ($result) {
    while ($r = $result->fetch_assoc()) {
        $rows[] = $r;
    }
    $result->close();
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Video Info Table</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
  </head>
  <body class="bg-dark text-light p-3">
    <h1 class="mb-3">YouTube Video Info</h1>
    <form method="post" class="mb-3">
      <div class="row g-2">
        <div class="col">
          <input
            type="url"
            name="videoUrl"
            class="form-control"
            placeholder="YouTube video URL"
            required
          />
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </div>
    </form>
    <table class="table table-dark table-striped" id="videoTable">
      <thead>
        <tr>
          <th scope="col">Thumbnail</th>
          <th scope="col">Title</th>
          <th scope="col">Likes</th>
          <th scope="col">Comments</th>
          <th scope="col">Uploaded</th>
          <th scope="col">Link</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): $vid = extract_video_id($row['link']); ?>
        <tr data-video-id="<?= htmlspecialchars($vid) ?>">
          <td><img src="https://img.youtube.com/vi/<?= htmlspecialchars($vid) ?>/default.jpg" alt="thumb" /></td>
          <td class="title"></td>
          <td class="likes"></td>
          <td class="comments"></td>
          <td class="uploaded"></td>
          <td><a href="<?= htmlspecialchars($row['link']) ?>" class="video-link" target="_blank">Watch</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <script src="assets/video-table.js"></script>
  </body>
</html>
