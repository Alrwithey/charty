<?php
require_once 'config.php';
$settings = get_all_settings();

// جلب جميع الأخبار من قاعدة البيانات مرتبة من الأحدث إلى الأقدم
$news_result = $conn->query("SELECT * FROM news ORDER BY created_at DESC");

// تحديد عنوان الصفحة الذي سيظهر في تبويب المتصفح
$page_title = 'أخبار الجمعية - ' . ($settings['site_name'] ?? '');
$meta_description = 'تابع آخر أخبار وفعاليات جمعيتنا.';

// تضمين ملف الهيدر الذي يحتوي على كل التنسيقات والقائمة العلوية
include 'partials/header.php';
?>

<main class="main-content-area">
    <div class="page-header" style="background-image: url('<?php echo BASE_URL; ?>/images/news-header-bg.jpg');">
        <div class="container">
            <h1>أخبار الجمعية</h1>
        </div>
    </div>
    
    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="card-grid full-page">
            <?php if ($news_result && $news_result->num_rows > 0): ?>
                <?php while($article = $news_result->fetch_assoc()): ?>
                <div class="content-card" data-aos="fade-up">
                    <a href="<?php echo BASE_URL; ?>/single-news.php?id=<?php echo $article['id']; ?>" class="content-card-img-wrapper">
                        <img src="<?php echo BASE_URL; ?>/<?php echo e($article['image']); ?>" alt="<?php echo e($article['title']); ?>">
                    </a>
                    <div class="content-card-body">
                        <span class="content-card-date"><?php echo date('d M, Y', strtotime($article['created_at'])); ?></span>
                        <h3><a href="<?php echo BASE_URL; ?>/single-news.php?id=<?php echo $article['id']; ?>"><?php echo e($article['title']); ?></a></h3>
                        <p><?php echo e(mb_substr(strip_tags($article['content']), 0, 150)); ?>...</p>
                        <a href="<?php echo BASE_URL; ?>/single-news.php?id=<?php echo $article['id']; ?>" class="btn-link">اقرأ المزيد</a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; grid-column: 1 / -1; font-size: 1.2rem; padding: 40px;">لا توجد أخبار متاحة حالياً.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// تضمين ملف الفوتر
include 'partials/footer.php';
?>
<!-- استدعاء ملفات JS في نهاية الصفحة -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/script.js?v=<?php echo time(); ?>"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 50 });
</script>
</body>
</html>