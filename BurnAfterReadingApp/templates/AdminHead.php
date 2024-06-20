<!DOCTYPE html>
<html lang="<?php echo $data['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['app_name'] ?> - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.min.css">
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
</head>
<body>
    <?php if (isset($_SESSION['loggedin'])) : ?>
        <nav class="py-2 px-2 gap-2 mb-3">
            <a href="./"><?php echo $data['_']['new'] ?></a>
            <a href="./?logout"><?php echo $data['_']['logout'] ?></a>
        </nav>
    <?php endif ?>
    <main <?php echo (isset($data['main_class'])) ? 'class="' . $data['main_class'] .  '"' : '' ?>>