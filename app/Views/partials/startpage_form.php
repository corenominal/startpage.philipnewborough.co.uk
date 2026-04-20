<form id="form-q" class="col-md-12 mb-3">
    <div class="input-group input-group-lg">
        <span id="q-icon" class="input-group-text"><i class="bi bi-chevron-right"></i></span>
        <input id="q" list="q-history" type="text" class="form-control" placeholder="" autocomplete="off" required>
        <datalist id="q-history">
            <?php foreach ($history as $item): ?>
                <option value="<?= esc($item['q']) ?>"></option>
            <?php endforeach; ?>
        </datalist>
        <button type="submit" class="btn btn-dark"><i class="bi bi-arrow-return-left"></i></button>
    </div>
</form>
