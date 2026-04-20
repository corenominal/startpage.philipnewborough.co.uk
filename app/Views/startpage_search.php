<?= $this->extend('templates/dashboard') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><?= esc($title) ?></h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <button id="btn-add" class="btn btn-outline-primary"><i class="bi bi-plus-circle-fill"></i> New</button>
                <button id="btn-refresh" class="btn btn-outline-primary"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table style="width:100%" id="table-search" class="table table-bordered table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Phrase</th>
                    <th>URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div> <!-- /.container-fluid -->

<!-- Add / Edit modal -->
<div class="modal fade" id="modal-search-form" tabindex="-1" aria-labelledby="modal-search-form-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-search-form-label">New Search Engine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="search-form-id" value="">
                <div class="mb-3">
                    <label for="search-form-phrase" class="form-label">Phrase</label>
                    <input type="text" class="form-control" id="search-form-phrase" placeholder="e.g. g">
                    <div class="form-text">The trigger phrase used on the start page.</div>
                </div>
                <div class="mb-3">
                    <label for="search-form-url" class="form-label">URL</label>
                    <input type="text" class="form-control" id="search-form-url" placeholder="e.g. https://www.google.com/search?q=%s">
                    <div class="form-text">Use <code>%s</code> as a placeholder for the search query.</div>
                </div>
                <div class="mb-3">
                    <label for="search-form-comments" class="form-label">Comments <span class="text-muted">(optional)</span></label>
                    <input type="text" class="form-control" id="search-form-comments" placeholder="">
                </div>
                <div id="search-form-error" class="alert alert-danger d-none" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-search-form-save">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="modal-search-delete-confirm" tabindex="-1" aria-labelledby="modal-search-delete-confirm-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-search-delete-confirm-label">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this search engine? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-search-delete-confirm">Delete</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
