<?php
require_once 'config.php';
$settings = get_all_settings();

// جلب جميع المشاريع من قاعدة البيانات مرتبة من الأحدث إلى الأقدم
$projects_result = $conn->query("SELECT * FROM projects ORDER BY created_at DESC");

$page_title = 'مشاريعنا - ' . ($settings['site_name'] ?? '');
$meta_description = 'اكتشف مشاريعنا الخيرية والتنموية التي نهدف من خلالها إلى خدمة المجتمع.';

include 'partials/header.php';
?>

<main class="main-content-area">
    <div class="page-header" style="background-image: url('<?php echo BASE_URL; ?>/images/projects-header-bg.jpg');">
        <div class="container">
            <h1>مشاريع الجمعية</h1>
        </div>
    </div>
    
    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
        <div class="card-grid full-page">
            <?php if ($projects_result && $projects_result->num_rows > 0): ?>
                <?php while($project = $projects_result->fetch_assoc()): ?>
                <div class="content-card" data-aos="fade-up">
                    <a href="<?php echo BASE_URL; ?>/single-project.php?id=<?php echo $project['id']; ?>" class="content-card-img-wrapper">
                        <img src="<?php echo BASE_URL; ?>/<?php echo e($project['image']); ?>" alt="<?php echo e($project['title']); ?>">
                    </a>
                    <div class="content-card-body">
                        <h3><a href="<?php echo BASE_URL; ?>/single-project.php?id=<?php echo $project['id']; ?>"><?php echo e($project['title']); ?></a></h3>
                        <p><?php echo e(mb_substr(strip_tags($project['content']), 0, 150)); ?>...</p>
                        <a href="<?php echo BASE_URL; ?>/single-project.php?id=<?php echo $project['id']; ?>" class="btn-link">عرض التفاصيل</a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; grid-column: 1 / -1; font-size: 1.2rem; padding: 40px;">لا توجد مشاريع متاحة حالياً.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
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