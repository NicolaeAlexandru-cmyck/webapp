<?php
// gallery.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$pageTitle = "Wedding Gallery - Elegant Weddings";
$pageDescription = "Browse our portfolio of beautiful weddings and events we've planned.";

// Get gallery items from database
$galleryItems = getGalleryItems();
$categories = getGalleryCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content">
                <h1>Wedding Gallery</h1>
                <p>Browse through our portfolio of beautiful weddings we've had the honor to plan and execute</p>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <!-- Gallery Filters -->
            <div class="gallery-filters">
                <button class="filter-btn active" data-filter="all">All</button>
                <?php foreach ($categories as $category): ?>
                    <button class="filter-btn" data-filter="<?php echo $category['slug']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Gallery Grid -->
            <div class="gallery-grid" id="galleryGrid">
                <?php foreach ($galleryItems as $item): ?>
                <div class="gallery-item <?php echo $item['category']; ?>" data-category="<?php echo $item['category']; ?>">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($item['alt_text']); ?>" 
                         loading="lazy">
                    <div class="gallery-overlay">
                        <i class="fas fa-search-plus"></i>
                        <div class="gallery-info">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Load More Button -->
            <div class="text-center" id="loadMoreContainer">
                <button class="btn btn-primary" id="loadMoreBtn" data-page="1">
                    <i class="fas fa-plus"></i>
                    Load More Images
                </button>
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-content">
            <button class="lightbox-close" id="lightboxClose">
                <i class="fas fa-times"></i>
            </button>
            <img id="lightboxImage" src="" alt="">
            <div class="lightbox-info">
                <h3 id="lightboxTitle"></h3>
                <p id="lightboxDescription"></p>
            </div>
            <div class="lightbox-nav">
                <button class="lightbox-prev" id="lightboxPrev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="lightbox-next" id="lightboxNext">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/gallery.js"></script>
</body>
</html>
