    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>عن الجمعية</h4>
                    <p>جمعية أهلية متخصصة في مجال رعاية مرضى العيون المحتاجين للعلاج بمنطقة المدينة المنورة.</p>
                    <div class="social-icons">
                        <a href="<?php echo e($settings['twitter_url'] ?? '#'); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="<?php echo e($settings['facebook_url'] ?? '#'); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo e($settings['instagram_url'] ?? '#'); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>روابط سريعة</h4>
                    <ul>
                        <?php foreach($menu_items as $item): ?>
                            <?php if(empty($item['children'])): ?>
                                <li><a href="<?php echo (strpos($item['link'], '#') === 0 || $item['link'] == '#') ? $item['link'] : BASE_URL . '/' . $item['link']; ?>"><?php echo e($item['title']); ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>تواصل معنا</h4>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo e($settings['contact_address'] ?? ''); ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo e($settings['contact_phone'] ?? ''); ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo e($settings['contact_email'] ?? ''); ?></p>
                </div>
                <div class="footer-col">
                    <h4>موقعنا</h4>
                    <?php echo $settings['google_map_embed'] ?? ''; ?>
                </div>
            </div>
            <div class="footer-bottom">
                <p>جميع الحقوق محفوظة © <span id="year"></span> لـ<?php echo e($settings['site_name'] ?? ''); ?>.</p>
            </div>
        </div>
    </footer>

    <!-- !! زر العودة للأعلى الجديد !! -->
    <a href="#" class="back-to-top-btn" id="backToTopBtn" title="العودة للأعلى"><i class="fas fa-arrow-up"></i></a>