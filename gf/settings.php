<?php
require_once __DIR__.'/db.php';
function get_setting($key, $default=''){
    global $conn;
    $stmt = $conn->prepare('SELECT svalue FROM gf_settings WHERE skey=?');
    $stmt->bind_param('s',$key);
    $stmt->execute();
    $stmt->bind_result($val);
    $ok=$stmt->fetch();
    $stmt->close();
    return $ok?$val:$default;
}
function set_setting($key,$val){
    global $conn;
    $stmt=$conn->prepare('INSERT INTO gf_settings(skey,svalue) VALUES(?,?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)');
    $stmt->bind_param('ss',$key,$val);
    $stmt->execute();
    $stmt->close();
}
if($_SERVER['REQUEST_METHOD']==='POST'){
    set_setting('tone', $_POST['tone'] ?? 'gentle');
    set_setting('temperature', $_POST['temperature'] ?? '0.6');
    set_setting('lang', $_POST['lang'] ?? 'english');
    set_setting('theme', $_POST['theme'] ?? 'light');
    set_setting('sounds', isset($_POST['sounds'])?'on':'off');
    $saved=true;
}
$tone = get_setting('tone','gentle');
$temp = get_setting('temperature','0.6');
$lang = get_setting('lang','english');
$theme = get_setting('theme','light');
$sounds = get_setting('sounds','on');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Settings</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
<h1 class="mb-3">Settings</h1>
<?php if(!empty($saved)) echo '<div class="alert alert-success">Saved</div>'; ?>
<form method="post">
<div class="mb-3">
<label class="form-label">Tone</label>
<select name="tone" class="form-select">
<option value="gentle"<?= $tone=='gentle'?' selected':'' ?>>Gentle</option>
<option value="playful"<?= $tone=='playful'?' selected':'' ?>>Playful</option>
</select>
</div>
<div class="mb-3">
<label class="form-label">Creativity</label>
<input type="range" name="temperature" min="0.2" max="0.8" step="0.1" value="<?= htmlspecialchars($temp) ?>" class="form-range">
</div>
<div class="mb-3">
<label class="form-label">Language</label>
<select name="lang" class="form-select">
<option value="english"<?= $lang=='english'?' selected':'' ?>>English</option>
<option value="hinglish"<?= $lang=='hinglish'?' selected':'' ?>>Hinglish</option>
</select>
</div>
<div class="mb-3">
<label class="form-label">Theme</label>
<select name="theme" class="form-select">
<option value="light"<?= $theme=='light'?' selected':'' ?>>Light</option>
<option value="dark"<?= $theme=='dark'?' selected':'' ?>>Dark</option>
</select>
</div>
<div class="form-check mb-3">
<input class="form-check-input" type="checkbox" name="sounds" id="sounds"<?= $sounds=='on'?' checked':'' ?>>
<label class="form-check-label" for="sounds">Enable sounds</label>
</div>
<button class="btn btn-primary" type="submit">Save</button>
</form>
</body>
</html>
