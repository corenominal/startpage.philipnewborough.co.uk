<div class="text-end mb-2">
    <a href="/start/redirects" class="btn btn-outline-primary"><i class="bi bi-gear-fill"></i> Edit</a>
</div>

<h3 class="d-xl-none">Redirects</h3>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Phrase</th>
            <th>URL</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($redirects as $redirect): ?>
            <tr>
                <td><a class="anchor-redirect" href="#"><?= esc($redirect['phrase']) ?></a></td>
                <td><?= esc($redirect['url']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
