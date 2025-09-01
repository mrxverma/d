<?php
$md = file_get_contents(__DIR__ . '/personality.md');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Aira Personality</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h1 class="mb-4">Personality</h1>
  <div id="md"></div>
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script>
    const md = <?php echo json_encode($md); ?>;
    document.getElementById('md').innerHTML = marked.parse(md);
  </script>
</body>
</html>
