<head>
    <title>Hachi Social Network</title>
    <?php foreach($data['css'] as $cssPath): ?>
        <link rel="stylesheet" href="<?php echo $cssPath?>" >
    <?php endforeach?>

    <?php foreach($data['js'] as $jsPath): ?>
        <script type="text/javascript" src="<?php echo $jsPath?>"></script>
    <?php endforeach?>
</head>