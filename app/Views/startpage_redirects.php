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
        <table style="width:100%" id="table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Phrase</th>
                    <th>URL</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div> <!-- /.container-fluid -->

<?= $this->endSection() ?>
