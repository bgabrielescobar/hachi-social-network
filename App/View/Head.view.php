<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hachi Social Network</title>
    <?php foreach($data['css'] as $cssPath): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath) ?>" >
    <?php endforeach?>

    <?php foreach($data['js'] as $jsPath): ?>
        <script type="text/javascript" src="<?php echo htmlspecialchars($jsPath) ?>" defer></script>
    <?php endforeach?>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🐶</text></svg>">
</head>
<body>
