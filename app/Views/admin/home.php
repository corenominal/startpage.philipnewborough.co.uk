<?= $this->extend('templates/dashboard') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="border-bottom border-1 mb-4 pb-4">
                <h2 class="mb-0">Dashboard</h2>
            </div>

            <!-- Stat cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="/admin/shortcuts" class="text-decoration-none">
                        <div class="card h-100 bg-dark-subtle">
                            <div class="card-body text-center">
                                <div class="fs-2 fw-bold text-primary"><?= esc($stats['shortcuts']) ?></div>
                                <div class="text-secondary small mt-1"><i class="bi bi-bookmark-fill me-1"></i>Shortcuts</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="/admin/shortcuts" class="text-decoration-none">
                        <div class="card h-100 bg-dark-subtle">
                            <div class="card-body text-center">
                                <div class="fs-2 fw-bold text-primary"><?= esc($stats['categories']) ?></div>
                                <div class="text-secondary small mt-1"><i class="bi bi-folder-fill me-1"></i>Categories</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="/start/redirects" class="text-decoration-none">
                        <div class="card h-100 bg-dark-subtle">
                            <div class="card-body text-center">
                                <div class="fs-2 fw-bold text-primary"><?= esc($stats['redirects']) ?></div>
                                <div class="text-secondary small mt-1"><i class="bi bi-arrow-left-right me-1"></i>Redirects</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="/start/search" class="text-decoration-none">
                        <div class="card h-100 bg-dark-subtle">
                            <div class="card-body text-center">
                                <div class="fs-2 fw-bold text-primary"><?= esc($stats['search']) ?></div>
                                <div class="text-secondary small mt-1"><i class="bi bi-search me-1"></i>Search Engines</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="/start/history" class="text-decoration-none">
                        <div class="card h-100 bg-dark-subtle">
                            <div class="card-body text-center">
                                <div class="fs-2 fw-bold text-primary"><?= esc($stats['history']) ?></div>
                                <div class="text-secondary small mt-1"><i class="bi bi-clock-history me-1"></i>History Entries</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Top searches -->
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="card bg-dark-subtle">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span class="fw-semibold"><i class="bi bi-bar-chart-fill me-2"></i>Top Searches</span>
                            <a href="/start/history" class="btn btn-sm btn-outline-secondary">View all</a>
                        </div>
                        <?php if (empty($top_searches)): ?>
                        <div class="card-body text-secondary">No search history yet.</div>
                        <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($top_searches as $row): ?>
                            <li class="list-group-item bg-transparent d-flex align-items-center justify-content-between gap-2">
                                <a href="/?q=<?= esc(urlencode($row['q'])) ?>" class="text-truncate text-decoration-none text-body">
                                    <?= esc($row['q']) ?>
                                </a>
                                <span class="badge text-bg-secondary flex-shrink-0"><?= esc($row['count']) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick links -->
                <div class="col-12 col-lg-6">
                    <div class="card bg-dark-subtle">
                        <div class="card-header fw-semibold">
                            <i class="bi bi-lightning-fill me-2"></i>Quick Links
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-transparent">
                                <a href="/admin/shortcuts" class="text-decoration-none text-body d-flex align-items-center gap-2">
                                    <i class="bi bi-grid-3x3-gap-fill text-secondary"></i> Manage Shortcuts
                                </a>
                            </li>
                            <li class="list-group-item bg-transparent">
                                <a href="/start/redirects" class="text-decoration-none text-body d-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-left-right text-secondary"></i> Manage Redirects
                                </a>
                            </li>
                            <li class="list-group-item bg-transparent">
                                <a href="/start/search" class="text-decoration-none text-body d-flex align-items-center gap-2">
                                    <i class="bi bi-search text-secondary"></i> Manage Search Engines
                                </a>
                            </li>
                            <li class="list-group-item bg-transparent">
                                <a href="/start/history" class="text-decoration-none text-body d-flex align-items-center gap-2">
                                    <i class="bi bi-clock-history text-secondary"></i> View Search History
                                </a>
                            </li>
                            <li class="list-group-item bg-transparent">
                                <a href="/admin/import-export" class="text-decoration-none text-body d-flex align-items-center gap-2">
                                    <i class="bi bi-arrow-down-up text-secondary"></i> Import / Export
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>