<?php
require_once 'db.php';

function extract_video_id($url) {
    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|v/|shorts/))([\w-]{11})~x', $url, $matches)) {
        return $matches[1];
    }
    return '';
}

// Insert new short
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

// Fetch shorts
$shorts = [];
$result = $conn->query("SELECT id, link, likes_count, time FROM shorts ORDER BY time DESC");
if ($result) {
    while ($r = $result->fetch_assoc()) {
        $shorts[$r['id']] = $r;
        $shorts[$r['id']]['comments'] = []; // placeholder
    }
    $result->close();
}

// Fetch comments for all shorts
if (!empty($shorts)) {
    $ids = implode(',', array_keys($shorts));
    $cresult = $conn->query("SELECT * FROM comments WHERE short_id IN ($ids) ORDER BY time ASC");
    if ($cresult) {
        while ($c = $cresult->fetch_assoc()) {
            $shorts[$c['short_id']]['comments'][] = $c;
        }
        $cresult->close();
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Video Info Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  </head>
  <body class="bg-dark text-light p-3">
    <h1 class="mb-3">YouTube Video Info</h1>
    <form method="post" class="mb-3">
      <div class="row g-2">
        <div class="col">
          <input type="url" name="videoUrl" class="form-control" placeholder="YouTube video URL" required />
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-primary">Add</button>
        </div>
      </div>
    </form>

    <table class="table table-dark table-striped" id="videoTable">
      <thead>
        <tr>
          <th>Thumbnail</th>
          <th>Title</th>
          <th>Likes</th>
          <th>Comments</th>
          <th>Uploaded</th>
          <th>Link</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($shorts as $row): $vid = extract_video_id($row['link']); ?>
        <tr>
          <td><img src="https://img.youtube.com/vi/<?= htmlspecialchars($vid) ?>/default.jpg" alt="thumb" /></td>
          <td class="title"></td>
          <td><?= (int)$row['likes_count'] ?></td>
          <td>
            <?php if (!empty($row['comments'])): ?>
              <ul class="list-unstyled mb-0">
                <?php foreach ($row['comments'] as $c): ?>
                  <li><strong><?= htmlspecialchars($c['username']) ?>:</strong> <?= htmlspecialchars($c['comment']) ?> <small class="text-muted">(<?= $c['time'] ?>)</small></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <em>No comments</em>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($row['time']) ?></td>
          <td><a href="<?= htmlspecialchars($row['link']) ?>" target="_blank">Watch</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </body>
</html>
