<?= $this->extend('templates/dashboard') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <h1 class="visually-hidden"><?= esc($title) ?></h1>

    <div class="row">
        <?= $this->include('partials/startpage_form') ?>
    </div>

    <div id="response_holder" class="row d-none mb-3">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">Response</h5>
                <div id="response_html" class="card-body"></div>
                <div class="card-footer">
                    <button id="btn-clear-html-response" class="btn btn-outline-primary"><i class="bi bi-x-square-fill"></i> Clear</button>
                </div>
            </div>
        </div>
    </div>

    <nav>
        <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
            <a href="#" class="nav-link active" id="nav-shortcuts-tab" data-bs-toggle="tab" data-bs-target="#nav-shortcuts" role="tab" aria-controls="nav-shortcuts" aria-selected="true">
                <i class="bi bi-grid-3x3-gap-fill"></i><span class="d-none d-xl-inline"> Shortcuts</span>
            </a>
            <a href="#" class="nav-link" id="nav-redirects-tab" data-bs-toggle="tab" data-bs-target="#nav-redirects" role="tab" aria-controls="nav-redirects" aria-selected="false">
                <i class="bi bi-arrow-left-right"></i><span class="d-none d-xl-inline"> Redirects</span>
            </a>
            <a href="#" class="nav-link" id="nav-search-engines-tab" data-bs-toggle="tab" data-bs-target="#nav-search-engines" role="tab" aria-controls="nav-search-engines" aria-selected="false">
                <i class="bi bi-search"></i><span class="d-none d-xl-inline"> Search Engines</span>
            </a>
            <a href="#" class="nav-link" id="nav-commands-tab" data-bs-toggle="tab" data-bs-target="#nav-commands" role="tab" aria-controls="nav-commands" aria-selected="false">
                <i class="bi bi-slash-square"></i><span class="d-none d-xl-inline"> Commands</span>
            </a>
            <a href="#" class="nav-link" id="nav-history-tab" data-bs-toggle="tab" data-bs-target="#nav-history" role="tab" aria-controls="nav-history" aria-selected="false">
                <i class="bi bi-clock-history"></i><span class="d-none d-xl-inline"> History</span>
            </a>
        </div>
    </nav>

    <div class="tab-content" id="nav-tabContent">

        <div class="tab-pane fade show active" id="nav-shortcuts" role="tabpanel" aria-labelledby="nav-shortcuts-tab">
            <?= $this->include('partials/startpage_shortcuts') ?>
        </div> <!-- #nav-shortcuts -->

        <div class="tab-pane fade" id="nav-redirects" role="tabpanel" aria-labelledby="nav-redirects-tab">
            <?= $this->include('partials/startpage_redirects') ?>
        </div> <!-- #nav-redirects -->

        <div class="tab-pane fade" id="nav-search-engines" role="tabpanel" aria-labelledby="nav-search-engines-tab">
            <?= $this->include('partials/startpage_search') ?>
        </div> <!-- #nav-search-engines -->

        <div class="tab-pane fade" id="nav-commands" role="tabpanel" aria-labelledby="nav-commands-tab">
            <?= $this->include('partials/startpage_commands') ?>
        </div> <!-- #nav-commands -->

        <div class="tab-pane fade" id="nav-history" role="tabpanel" aria-labelledby="nav-history-tab">
            <?= $this->include('partials/startpage_history') ?>
        </div> <!-- #nav-history -->

    </div>

</div> <!-- /.container-fluid -->

<?= $this->endSection() ?>
