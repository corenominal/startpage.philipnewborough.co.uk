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
        <table style="width:100%" id="table-redirects" class="table table-bordered table-striped table-hover align-middle">
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
<div class="modal fade" id="modal-redirects-form" tabindex="-1" aria-labelledby="modal-redirects-form-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-redirects-form-label">New Redirect</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="redirects-form-id" value="">
                <div class="mb-3">
                    <label for="redirects-form-phrase" class="form-label">Phrase</label>
                    <input type="text" class="form-control" id="redirects-form-phrase" placeholder="e.g. gh">
                    <div class="form-text">The trigger phrase used on the start page.</div>
                </div>
                <div class="mb-3">
                    <label for="redirects-form-url" class="form-label">URL</label>
                    <input type="text" class="form-control" id="redirects-form-url" placeholder="e.g. https://github.com">
                    <div class="form-text">The URL to redirect to when the phrase is entered.</div>
                </div>
                <div class="mb-3">
                    <label for="redirects-form-comments" class="form-label">Comments <span class="text-muted">(optional)</span></label>
                    <input type="text" class="form-control" id="redirects-form-comments" placeholder="">
                </div>
                <div id="redirects-form-error" class="alert alert-danger d-none" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-redirects-form-save">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="modal-redirects-delete-confirm" tabindex="-1" aria-labelledby="modal-redirects-delete-confirm-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-redirects-delete-confirm-label">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this redirect? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btn-redirects-delete-confirm">Delete</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
