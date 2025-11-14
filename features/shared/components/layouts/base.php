<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($pageTitle ?? 'SulamProject'); ?> — SulamProject</title>
    <link rel="stylesheet" href="/sulamproject/features/shared/assets/css/variables.css">
    <link rel="stylesheet" href="/sulamproject/features/shared/assets/css/base.css">
    <?php if (isset($additionalStyles)): ?>
        <?php foreach ($additionalStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo e($style); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php echo $content ?? ''; ?>
    
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo e($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
