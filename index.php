<?php
require_once 'config.php';
$settings = get_all_settings();

// جلب كل البيانات اللازمة للصفحة الرئيسية
$sliders_result = $conn->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY slide_order ASC");
$services_result = $conn->query("SELECT * FROM services ORDER BY service_order ASC");
$stats_result = $conn->query("SELECT * FROM stats ORDER BY stat_order ASC");
$news_result = $conn->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 3");
$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 3");
$tabs_result = $conn->query("SELECT * FROM about_tabs WHERE is_active = 1 ORDER BY tab_order ASC");
$manager_data = $conn->query("SELECT * FROM executive_manager WHERE id=1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <?php 
    $page_title = e($settings['site_name'] ?? 'جمعية خيرية');
    include 'partials/header.php'; 
    ?>
</head>
<body>
    <!-- الهيدر يتم جلبه من الملف المشترك -->

    <!-- ================== Hero Slider Section ================== -->
    <?php if ($sliders_result && $sliders_result->num_rows > 0): ?>
    <section class="hero-slider-section">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <?php while($slide = $sliders_result->fetch_assoc()): ?>
                <div class="swiper-slide" style="background-image: url('<?php echo BASE_URL . '/' . e($slide['image_path']); ?>');">
                    <div class="hero-content" data-aos="fade-up">
                        <h1><?php echo e($slide['title']); ?></h1>
                        <p><?php echo e($slide['text']); ?></p>
                        <a href="<?php echo e($slide['button_link']); ?>" class="btn"><?php echo e($slide['button_text']); ?></a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ================== About Section (Tabs) ================== -->
    <?php if ($tabs_result && $tabs_result->num_rows > 0): ?>
    <section id="about" class="about-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <span>نبذة عن الجمعية</span>
                <h2>تعريف بالجمعية</h2>
            </div>
            <div class="tabs-nav" data-aos="fade-up" data-aos-delay="100">
                <?php 
                $tabs_array = [];
                $tabs_result->data_seek(0);
                while($tab = $tabs_result->fetch_assoc()) { $tabs_array[] = $tab; }
                $first = true; 
                foreach ($tabs_array as $tab): ?>
                <button class="tab-btn <?php if($first) { echo 'active'; $first = false; } ?>" data-tab="tab-<?php echo $tab['id']; ?>"><?php echo e($tab['title']); ?></button>
                <?php endforeach; ?>
            </div>
            <div class="tabs-content" data-aos="fade-up" data-aos-delay="200">
                <?php 
                $first = true;
                foreach ($tabs_array as $tab): ?>
                    <div class="tab-pane <?php if($first) { echo 'active'; $first = false; } ?>" id="tab-<?php echo $tab['id']; ?>">
                        <?php if($tab['tab_type'] === 'normal'): ?>
                            <div class="tab-pane-content">
                                <div class="text-content">
                                    <h3><?php echo e($tab['content_title']); ?></h3>
                                    <p><?php echo nl2br(e($tab['content_text'])); ?></p>
                                </div>
                                <div class="icon-content">
                                    <?php if(!empty($tab['icon_image'])): ?>
                                        <img src="<?php echo BASE_URL; ?>/<?php echo e($tab['icon_image']); ?>" alt="<?php echo e($tab['title']); ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif($tab['tab_type'] === 'manager' && $manager_data): ?>
                            <div class="manager-profile">
                                <img src="<?php echo BASE_URL; ?>/<?php echo e($manager_data['photo']); ?>" alt="<?php echo e($manager_data['name']); ?>" class="manager-photo">
                                <h3><?php echo e($manager_data['name']); ?></h3>
                                <span><?php echo e($manager_data['position']); ?></span>
                                <div class="manager-details-wrapper">
                                    <div class="manager-details">
                                        <div class="detail-item"><div class="detail-icon"><i class="fa-solid fa-graduation-cap"></i></div><div class="detail-title">الدرجة العلمية</div><div class="detail-value"><?php echo e($manager_data['degree']); ?></div></div>
                                        <div class="detail-item"><div class="detail-icon"><i class="fa-solid fa-briefcase"></i></div><div class="detail-title">سنوات الخبرة</div><div class="detail-value"><?php echo e($manager_data['experience']); ?></div></div>
                                        <div class="detail-item"><div class="detail-icon"><i class="fa-solid fa-phone"></i></div><div class="detail-title">رقم الجوال</div><div class="detail-value"><?php echo e($manager_data['phone']); ?></div></div>
                                        <div class="detail-item"><div class="detail-icon"><i class="fa-solid fa-envelope"></i></div><div class="detail-title">البريد الإلكتروني</div><div class="detail-value"><?php echo e($manager_data['email']); ?></div></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ================== Services Section ================== -->
    <?php if ($services_result && $services_result->num_rows > 0): ?>
    <section id="services" class="services-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>خدمات الجمعية</h2>
            </div>
            <div class="services-grid">
                <?php while($service = $services_result->fetch_assoc()): ?>
                <div class="service-card" data-aos="fade-up" data-aos-delay="<?php echo e($service['service_order'] * 100); ?>">
                    <div class="service-icon"><i class="<?php echo e($service['icon_class']); ?>"></i></div>
                    <h3><?php echo e($service['title']); ?></h3>
                    <p><?php echo e($service['description']); ?></p>
                    <a href="#" class="btn-link"><?php echo e($service['button_text']); ?></a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ================== Projects Section ================== -->
    <?php if ($projects_result && $projects_result->num_rows > 0): ?>
    <section class="projects-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>أحدث المشاريع</h2>
            </div>
            <div class="card-grid">
                <?php while($project = $projects_result->fetch_assoc()): ?>
                <div class="content-card" data-aos="fade-up">
                    <a href="<?php echo BASE_URL; ?>/single-project.php?id=<?php echo $project['id']; ?>" class="content-card-img-wrapper">
                        <img src="<?php echo BASE_URL; ?>/<?php echo e($project['image']); ?>" alt="<?php echo e($project['title']); ?>">
                    </a>
                    <div class="content-card-body">
                        <h3><a href="<?php echo BASE_URL; ?>/single-project.php?id=<?php echo $project['id']; ?>"><?php echo e($project['title']); ?></a></h3>
                        <p><?php echo e(mb_substr(strip_tags($project['content']), 0, 100)); ?>...</p>
                        <a href="<?php echo BASE_URL; ?>/single-project.php?id=<?php echo $project['id']; ?>" class="btn-link">عرض التفاصيل</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
             <div class="text-center" style="margin-top: 40px;">
                <a href="<?php echo BASE_URL; ?>/projects.php" class="btn">كل المشاريع</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

        <!-- ================== Statistics Section ================== -->
    <?php if ($stats_result && $stats_result->num_rows > 0): ?>
    <section class="stats-section-new">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>أرقام وإحصائيات الجمعية</h2>
            </div>
            <div class="stats-grid-new">
                 <?php while($stat = $stats_result->fetch_assoc()): ?>
                    <div class="stat-item-new" data-aos="fade-up" data-aos-delay="<?php echo e($stat['stat_order'] * 100); ?>">
                        <div class="stat-circle-svg-wrapper">
                            <svg class="stat-circle-svg" width="180" height="180" viewBox="0 0 120 120">
                                <!-- الدائرة الداخلية المفرغة -->
                                <circle class="circle-inner-stroke" cx="60" cy="60" r="39" stroke-width="8"></circle>

                                <!-- الدائرة الأمامية (الملونة) التي ستتحرك -->
                                <circle class="circle-progress" cx="60" cy="60" r="54" stroke-width="8" transform="rotate(-90 60 60)"></circle>
                            </svg>
                            <div class="stat-number-new">
                                <span class="counter" data-target="<?php echo e($stat['value']); ?>">0</span>+
                            </div>
                        </div>
                        <h4 class="stat-title-new"><?php echo e($stat['title']); ?></h4>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- ================== News Section ================== -->
    <?php if ($news_result && $news_result->num_rows > 0): ?>
    <section class="news-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>آخر الأخبار</h2>
            </div>
            <div class="card-grid">
                <?php while($article = $news_result->fetch_assoc()): ?>
                <div class="content-card" data-aos="fade-up">
                    <a href="<?php echo BASE_URL; ?>/single-news.php?id=<?php echo $article['id']; ?>" class="content-card-img-wrapper">
                        <img src="<?php echo BASE_URL; ?>/<?php echo e($article['image']); ?>" alt="<?php echo e($article['title']); ?>">
                    </a>
                    <div class="content-card-body">
                        <span class="content-card-date"><?php echo date('d M, Y', strtotime($article['created_at'])); ?></span>
                        <h3><a href="<?php echo BASE_URL; ?>/single-news.php?id=<?php echo $article['id']; ?>"><?php echo e($article['title']); ?></a></h3>
                        <p><?php echo e(mb_substr(strip_tags($article['content']), 0, 100)); ?>...</p>
                        <a href="<?php echo BASE_URL; ?>/single-news.php?id=<?php echo $article['id']; ?>" class="btn-link">اقرأ المزيد</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="text-center" style="margin-top: 40px;">
                <a href="<?php echo BASE_URL; ?>/news.php" class="btn">كل الأخبار</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php include 'partials/partners-slider.php'; ?>

    <?php include 'partials/footer.php'; ?>
    
    <!-- ================== JS Scripts ================== -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/js/script.js?v=<?php echo time(); ?>"></script>
    <script>
        AOS.init({ duration: 800, once: true, offset: 50 });
    </script>
</body>
</html>