<?php
require_once 'config.php';

if (!isset($_GET['slug'])) { header("Location: index.php"); exit(); }

$slug = $conn->real_escape_string($_GET['slug']);
$stmt = $conn->prepare("SELECT * FROM pages WHERE slug = ? LIMIT 1");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) { header("Location: index.php"); exit(); }

$page = $result->fetch_assoc();
$settings = get_all_settings();

$page_title = e($page['title']) . ' - ' . ($settings['site_name'] ?? 'جمعية خيرية');
$meta_description = !empty($page['meta_description']) ? e($page['meta_description']) : e(mb_substr(strip_tags($page['content'] ?? ''), 0, 160));

include 'partials/header.php';
?>
<main class="main-content-area">
    <div class="page-header"><div class="container"><h1><?php echo e($page['title']); ?></h1></div></div>
    
    <div class="container" style="padding-top: 50px; padding-bottom: 50px;">
        
        <?php if ($slug === 'board-of-directors'): ?>
            
            <div class="page-with-sidebar">
                <div class="main-page-content" data-aos="fade-left">
                    <?php 
                        $members_result = $conn->query("SELECT * FROM board_members WHERE member_type = 'board' ORDER BY member_order ASC");
                        if ($members_result && $members_result->num_rows > 0):
                    ?>
                        <div class="members-grid-new">
                            <?php while($member = $members_result->fetch_assoc()): ?>
                                <div class="member-card-new">
                                    <div class="member-photo-new"><img src="<?php echo BASE_URL . '/' . e($member['photo'] ?? 'uploads/members/placeholder.png'); ?>" alt="<?php echo e($member['name']); ?>"></div>
                                    <div class="member-info-new">
                                        <p class="member-position-new"><?php echo e($member['position']); ?></p>
                                        <h3 class="member-name-new"><?php echo e($member['name']); ?></h3>
                                        <div class="member-contact-new">
                                            <?php if(!empty($member['email'])): ?><span><i class="fas fa-envelope"></i> <?php echo e($member['email']); ?></span><?php endif; ?>
                                            <?php if(!empty($member['phone'])): ?><span><i class="fas fa-phone"></i> <?php echo e($member['phone']); ?></span><?php endif; ?>
                                        </div>
                                        <?php if(!empty($member['term_duration'])): ?><div class="member-term-new">المدة المتاحة في المجلس <?php echo e($member['term_duration']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>سيتم إضافة أسماء الأعضاء قريبًا.</p>
                    <?php endif; ?>
                </div>

                <aside class="page-sidebar" data-aos="fade-right">
                    <div class="sidebar-widget">
                        <h4>مدة دورة المجلس</h4>
                        <div class="sidebar-stat-circle">
                            <?php 
                                $term_parts = explode(' ', $settings['board_term'] ?? '4 سنوات');
                                echo '<span>' . e($term_parts[0] ?? '') . '</span><span>' . e($term_parts[1] ?? '') . '</span>';
                            ?>
                        </div>
                    </div>
                    <div class="sidebar-widget">
                        <h4>تاريخ انتهاء الدورة</h4>
                        <div class="sidebar-info-box">
                            <span><?php echo e($settings['board_expiry_date'] ?? 'غير محدد'); ?></span>
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                     <div class="sidebar-widget">
                        <h4>خطاب تهنئة وتشكيل المجلس</h4>
                         <?php if(!empty($settings['board_congratulations_file'])): ?>
                            <a href="<?php echo BASE_URL . '/' . e($settings['board_congratulations_file']); ?>" target="_blank" class="btn" style="width:100%;"><i class="fas fa-file-pdf"></i> عرض الملف المرفق</a>
                         <?php else: ?>
                            <p>لم يتم رفع الملف بعد.</p>
                         <?php endif; ?>
                    </div>
                </aside>
            </div>

        <?php elseif ($slug === 'general-assembly'): ?>
            <div class="full-page-content" data-aos="fade-up">
                 <?php 
                    $members_result = $conn->query("SELECT * FROM board_members WHERE member_type = 'assembly' ORDER BY member_order ASC");
                    if ($members_result && $members_result->num_rows > 0):
                ?>
                    <div class="members-grid-new">
                        <?php while($member = $members_result->fetch_assoc()): ?>
                            <div class="member-card-new">
                                <div class="member-photo-new"><img src="<?php echo BASE_URL . '/' . e($member['photo'] ?? 'uploads/members/placeholder.png'); ?>" alt="<?php echo e($member['name']); ?>"></div>
                                <div class="member-info-new">
                                    <p class="member-position-new"><?php echo e($member['position']); ?></p>
                                    <h3 class="member-name-new"><?php echo e($member['name']); ?></h3>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>سيتم إضافة أسماء الأعضاء قريبًا.</p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="full-page-content" data-aos="fade-up">
                <div class="content-body"><?php echo $page['content']; ?></div>
                <?php if ($slug === 'license' && !empty($settings['license_image'])): ?>
                    <div class="license-image-container"><img src="<?php echo BASE_URL . '/' . e($settings['license_image']); ?>" alt="صورة الترخيص"></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    </div>
</main>
<?php
include 'partials/footer.php';
?>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>/js/script.js?v=<?php echo time(); ?>"></script>
<script>AOS.init({ duration: 800, once: true, offset: 50 });</script>
</body>
</html>