<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'] === 'en' ? 'en' : 'bn';
}

$lang = $_SESSION['lang'] ?? 'bn';

if (!isset($path_prefix)) {
    $current_script = $_SERVER['SCRIPT_NAME'];
    $src_pos = strrpos($current_script, '/src/');
    if ($src_pos !== false) {
        $sub_path = substr($current_script, $src_pos + 5);
        $slash_count = substr_count($sub_path, '/');
        $path_prefix = str_repeat('../', $slash_count);
    } else {
        $slash_count = substr_count(ltrim($current_script, '/'), '/');
        $path_prefix = str_repeat('../', $slash_count);
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jibika</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $path_prefix; ?>assets/css/style.css">
</head>
<body>