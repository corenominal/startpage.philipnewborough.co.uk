document.addEventListener('DOMContentLoaded', function () {
    const sidebarLinks = document.querySelectorAll('#sidebar .nav-link');
    sidebarLinks.forEach(link => {
        if (link.getAttribute('href') === '/start/search') {
            link.classList.remove('text-white-50');
            link.classList.add('active');
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {

    // ── DataTable ──────────────────────────────────────────────────────────────
    const searchTable = new DataTable('#table-search', {
        info:         true,
        lengthChange: true,
        ordering:     true,
        paging:       true,
        searching:    true,
        processing:   true,
        serverSide:   true,
        pageLength:   25,
        lengthMenu:   [10, 25, 50, 100],
        order:        [[0, 'asc']],
        ajax: {
            url: '/start/search/datatable',
        },
        columns: [
            {
                name:      'phrase',
                data:      'phrase',
                title:     'Phrase',
                type:      'string',
                orderable: true,
                searchable: true,
                width:     '10rem',
            },
            {
                name:      'url',
                data:      'url',
                title:     'URL',
                type:      'string',
                orderable: true,
                searchable: true,
                width:     '',
            },
            {
                data:      'actions',
                title:     'Actions',
                orderable: false,
                searchable: false,
                width:     '7rem',
                className: 'text-center',
            },
        ],
    });

    // ── Form modal ─────────────────────────────────────────────────────────────
    const formModalEl = document.getElementById('modal-search-form');
    const formModal   = new bootstrap.Modal(formModalEl, { focus: false });

    formModalEl.addEventListener('shown.bs.modal', () => {
        document.getElementById('search-form-phrase').focus();
    });

    formModalEl.addEventListener('hide.bs.modal', () => {
        const focused = formModalEl.querySelector(':focus');
        if (focused) focused.blur();
    });

    function openAddModal() {
        document.getElementById('modal-search-form-label').textContent = 'New Search Engine';
        document.getElementById('search-form-id').value       = '';
        document.getElementById('search-form-phrase').value   = '';
        document.getElementById('search-form-url').value      = '';
        document.getElementById('search-form-comments').value = '';
        document.getElementById('search-form-error').classList.add('d-none');
        formModal.show();
    }

    function openEditModal(id, phrase, url, comments) {
        document.getElementById('modal-search-form-label').textContent = 'Edit Search Engine';
        document.getElementById('search-form-id').value       = id;
        document.getElementById('search-form-phrase').value   = phrase;
        document.getElementById('search-form-url').value      = url;
        document.getElementById('search-form-comments').value = comments;
        document.getElementById('search-form-error').classList.add('d-none');
        formModal.show();
    }

    document.getElementById('btn-add').addEventListener('click', openAddModal);

    document.getElementById('btn-search-form-save').addEventListener('click', () => {
        const id       = document.getElementById('search-form-id').value;
        const phrase   = document.getElementById('search-form-phrase').value.trim();
        const url      = document.getElementById('search-form-url').value.trim();
        const comments = document.getElementById('search-form-comments').value.trim();
        const errorEl  = document.getElementById('search-form-error');

        if (!phrase || !url) {
            errorEl.textContent = 'Phrase and URL are required.';
            errorEl.classList.remove('d-none');
            return;
        }

        const endpoint = id ? '/start/search/edit' : '/start/search/add';
        const body     = id
            ? { id: parseInt(id, 10), phrase, url, comments }
            : { phrase, url, comments };

        fetch(endpoint, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(body),
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                formModal.hide();
                searchTable.ajax.reload(null, false);
            } else {
                errorEl.textContent = data.message || 'An error occurred.';
                errorEl.classList.remove('d-none');
            }
        })
        .catch(() => {
            errorEl.textContent = 'A network error occurred.';
            errorEl.classList.remove('d-none');
        });
    });

    // Allow saving via Enter key in form fields
    formModalEl.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btn-search-form-save').click();
        }
    });

    // ── Delete modal ───────────────────────────────────────────────────────────
    const deleteModalEl = document.getElementById('modal-search-delete-confirm');
    const deleteModal   = new bootstrap.Modal(deleteModalEl, { focus: false });
    let pendingDeleteId = null;

    deleteModalEl.addEventListener('shown.bs.modal', () => {
        const closeBtn = deleteModalEl.querySelector('.btn-close');
        if (closeBtn) closeBtn.focus();
    });

    deleteModalEl.addEventListener('hide.bs.modal', () => {
        const focused = deleteModalEl.querySelector(':focus');
        if (focused) focused.blur();
    });

    document.getElementById('btn-search-delete-confirm').addEventListener('click', () => {
        if (!pendingDeleteId) return;

        fetch('/start/search/delete', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ ids: [pendingDeleteId] }),
        })
        .then(res => res.json())
        .then(() => {
            pendingDeleteId = null;
            deleteModal.hide();
            searchTable.ajax.reload(null, false);
        })
        .catch(err => console.error('Delete failed:', err));
    });

    // ── Table button delegation ────────────────────────────────────────────────
    document.querySelector('#table-search tbody').addEventListener('click', e => {
        const editBtn   = e.target.closest('.btn-edit');
        const deleteBtn = e.target.closest('.btn-delete-row');

        if (editBtn) {
            openEditModal(
                editBtn.dataset.id,
                editBtn.dataset.phrase,
                editBtn.dataset.url,
                editBtn.dataset.comments,
            );
        }

        if (deleteBtn) {
            pendingDeleteId = parseInt(deleteBtn.dataset.id, 10);
            deleteModal.show();
        }
    });

    // ── Refresh button ─────────────────────────────────────────────────────────
    document.getElementById('btn-refresh').addEventListener('click', () => {
        searchTable.ajax.reload(null, false);
    });
});
