<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TukTuk Rental - Your trusted tuk-tuk rental service in Sri Lanka">
    <meta name="keywords" content="tuk-tuk rental, Sri Lanka, vehicle rental, transportation">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - TukTuk Rental' : 'TukTuk Rental'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/scroll-to-top.css">
    <?php if (isset($pageSpecificCSS)): ?>
        <link rel="stylesheet" href="assets/css/<?php echo $pageSpecificCSS; ?>.css">
    <?php endif; ?>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <!-- Main Content Container -->
    <main class="main-content">
        <?php if (isset($showBreadcrumb) && $showBreadcrumb): ?>
        <div class="breadcrumb-container">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <?php if (isset($breadcrumbItems)): ?>
                            <?php foreach ($breadcrumbItems as $item): ?>
                                <li class="breadcrumb-item <?php echo $item['active'] ? 'active' : ''; ?>">
                                    <?php if (!$item['active']): ?>
                                        <a href="<?php echo $item['url']; ?>"><?php echo $item['text']; ?></a>
                                    <?php else: ?>
                                        <?php echo $item['text']; ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
        </div>
        <?php endif; ?> 