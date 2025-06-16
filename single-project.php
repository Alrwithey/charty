<?php
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: projects.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: projects.php");
    exit();
}

$project = $result->fetch_assoc();
$settings = get_all_settings();

$page_title = !empty($project['meta_title']) ? e($project['meta_title']) : e($project['title']) . ' - ' . ($settings['site_name'] ?? '');
$meta_description = !empty($project['meta_description']) ? e($project['meta_description']) : e(mb_substr(strip_tags($project['content']), 0, 160));

include 'partials/header.php';
?>
<main class="main-content-area">
    <div class="container">
        <div class="full-page-content" data-aos="fade-up">
            <h1><?php echo e($project['title']); ?></h1>
            <?php if (!empty($project['image'])): ?>
                <img src="<?php echo BASE_URL; ?>/<?php echo e($project['image']); ?>" alt="<?php echo e($project['title']); ?>" class="article-image">
            <?php endif; ?>
            <div class="content-body">
                <?php echo $project['content']; // السماح بـ HTML لمحتوى المشاريع ?>
            </div>
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