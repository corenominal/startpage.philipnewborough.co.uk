<?php if (empty($shortcut_categories)): ?>
    <p class="text-secondary"><em>No shortcuts yet.</em></p>
<?php else: ?>
    <?php foreach ($shortcut_categories as $category): ?>
        <?php if (empty($category['shortcuts'])): ?>
            <?php continue; ?>
        <?php endif; ?>
        <div class="mb-4">
            <h6 class="text-secondary text-uppercase fw-semibold mb-2 small"><?= esc($category['name']) ?></h6>
            <div class="d-flex flex-wrap gap-3">
                <?php foreach ($category['shortcuts'] as $shortcut): ?>
                    <a href="<?= esc($shortcut['url'], 'attr') ?>" class="d-flex flex-column align-items-center text-decoration-none text-white-50 shortcut-item" style="width:64px;" title="<?= esc($shortcut['name'], 'attr') ?>">
                        <?php if ($shortcut['icon_filename'] !== ''): ?>
                            <img src="/icons/<?= esc($shortcut['icon_filename'], 'attr') ?>" alt="<?= esc($shortcut['name'], 'attr') ?>" style="width:40px;height:40px;object-fit:contain;" class="mb-1">
                        <?php else: ?>
                            <i class="bi bi-link-45deg mb-1" style="font-size:2.5rem;"></i>
                        <?php endif; ?>
                        <span class="text-center lh-sm" style="font-size:0.7rem;word-break:break-word;"><?= esc($shortcut['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
