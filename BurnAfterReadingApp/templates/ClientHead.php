<!DOCTYPE html>
<html lang="<?php echo $data['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['app_name'] ?></title>
    <link rel="stylesheet" href="./assets/css/style.min.css">
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
</head>
<body>
    <main <?php echo (isset($data['main_class'])) ? 'class="' . $data['main_class'] .  '"' : '' ?>>