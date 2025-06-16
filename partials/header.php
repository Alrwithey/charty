<?php
// جلب عناصر القائمة من قاعدة البيانات وترتيبها
$menu_query = $conn->query("SELECT * FROM menu_items ORDER BY parent_id, menu_order ASC");
$menu_items = [];
// بناء مصفوفة منظمة (شجرة)
while($item = $menu_query->fetch_assoc()){
    if($item['parent_id'] == 0){
        $menu_items[$item['id']] = $item;
        $menu_items[$item['id']]['children'] = [];
    } else {
        if(isset($menu_items[$item['parent_id']])){
            $menu_items[$item['parent_id']]['children'][] = $item;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo $page_title ?? e($settings['site_name'] ?? 'جمعية خيرية'); ?></title>
    <meta name="description" content="<?php echo $meta_description ?? e($settings['site_name'] ?? 'وصف عام للجمعية يظهر في الصفحة الرئيسية'); ?>">

    <!-- تضمين الخطوط من جوجل -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;700;800&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css?v=<?php echo time(); ?>">
    <style>
        :root {
            --primary-color: <?php echo e($settings['main_color'] ?? '#1a535c'); ?>;
            --secondary-color: <?php echo e($settings['accent_color'] ?? '#f7b538'); ?>;
        }
        body {
            font-family: <?php echo $settings['font_family_body'] ?? "'Tajawal', sans-serif"; ?> !important;
            font-size: <?php echo e($settings['font_size_body'] ?? '16px'); ?> !important;
        }
        h1, .hero-swiper .hero-content h1, .page-header h1, .full-page-content h1 { font-size: <?php echo e($settings['font_size_h1'] ?? '3rem'); ?> !important; }
        h2, .section-title h2 { font-size: <?php echo e($settings['font_size_h2'] ?? '2.5rem'); ?> !important; }
        h3, .content-card-body h3, .service-card h3, .tab-pane-content .text-content h3 { font-size: <?php echo e($settings['font_size_h3'] ?? '1.8rem'); ?> !important; }
        h1, h2, h3, h4, h5, h6 {
             font-family: <?php echo $settings['font_family_headings'] ?? "'Tajawal', sans-serif"; ?> !important;
        }
    </style>
</head>
<body>
    <header id="main-header">
        <div class="container">
            <a href="<?php echo BASE_URL; ?>" class="logo">
                <img src="<?php echo BASE_URL; ?>/<?php echo e($settings['site_logo'] ?? ''); ?>" alt="شعار <?php echo e($settings['site_name'] ?? ''); ?>" class="logo-light">
                <img src="<?php echo BASE_URL; ?>/<?php echo e($settings['site_logo_white'] ?? ''); ?>" alt="شعار <?php echo e($settings['site_name'] ?? ''); ?>" class="logo-dark">
            </a>
            <nav id="main-nav">
                <ul>
                    <?php foreach($menu_items as $item): ?>
                        <li class="<?php if(!empty($item['children'])) echo 'has-dropdown'; ?>">
                            <a href="<?php echo (strpos($item['link'], '#') === 0 || $item['link'] == '#') ? $item['link'] : BASE_URL . '/' . $item['link']; ?>">
                                <?php echo e($item['title']); ?>
                                <?php if(!empty($item['children'])): ?>
                                    <i class="fas fa-chevron-down fa-xs"></i>
                                <?php endif; ?>
                            </a>
                            <?php if(!empty($item['children'])): ?>
                                <ul class="dropdown-menu">
                                    <?php foreach($item['children'] as $child): ?>
                                        <li><a href="<?php echo BASE_URL . '/' . $child['link']; ?>"><?php echo e($child['title']); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <div class="header-buttons">
                 <a href="#" class="btn btn-donate">تبرع الآن</a>
                 <button id="mobile-menu-btn" class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                    <i class="fas fa-times"></i>
                 </button>
            </div>
        </div>
    </header>