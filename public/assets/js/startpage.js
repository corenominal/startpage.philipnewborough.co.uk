document.addEventListener("DOMContentLoaded", function() {
    const sidebarLinks = document.querySelectorAll("#sidebar .nav-link");
    sidebarLinks.forEach(link => {
        if (link.getAttribute("href") === "/") {
            link.classList.remove("text-white-50");
            link.classList.add("active");
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('q')?.focus();
});

document.addEventListener('DOMContentLoaded', () => {
  const selectedIds = new Set();

  function updateDeleteButton() {
    document.getElementById('btn-history-delete').disabled = selectedIds.size === 0;
  }

  const historyTable = new DataTable('#table-history', {
    info:         true,
    lengthChange: true,
    ordering:     true,
    paging:       true,
    searching:    true,
    pageLength:   10,
    lengthMenu:   [10, 25, 50],
    order:        [[1, 'desc']],
    autoWidth: false,
    columnDefs: [
      { targets: 0, orderable: false, searchable: false, className: 'text-center', width: '2rem' },
      { targets: 1, type: 'date', width: '14rem' },
      { targets: 2, width: 'auto' },
      { targets: 3, type: 'num', className: 'text-end', width: '5rem' },
    ],
    drawCallback: function() {
      const api = new DataTable.Api(this);
      api.rows({ page: 'current' }).every(function() {
        const id       = this.node().dataset.id;
        const checkbox = this.node().querySelector('.row-select');
        const selected = selectedIds.has(id);
        if (checkbox) checkbox.checked = selected;
        this.node().classList.toggle('table-active', selected);
      });
      const selectAll = document.getElementById('history-select-all');
      if (selectAll) {
        const visibleIds = [];
        api.rows({ page: 'current' }).every(function() { visibleIds.push(this.node().dataset.id); });
        const n = visibleIds.filter(id => selectedIds.has(id)).length;
        selectAll.checked       = n > 0 && n === visibleIds.length;
        selectAll.indeterminate = n > 0 && n <  visibleIds.length;
      }
      updateDeleteButton();
    },
  });

  // Row checkbox
  document.querySelector('#table-history tbody').addEventListener('change', function(e) {
    if (!e.target.classList.contains('row-select')) return;
    const tr = e.target.closest('tr');
    const id = tr.dataset.id;
    if (e.target.checked) {
      selectedIds.add(id);
      tr.classList.add('table-active');
    } else {
      selectedIds.delete(id);
      tr.classList.remove('table-active');
    }
    const selectAll = document.getElementById('history-select-all');
    if (selectAll) {
      const visibleIds = [];
      historyTable.rows({ page: 'current' }).every(function() { visibleIds.push(this.node().dataset.id); });
      const n = visibleIds.filter(id => selectedIds.has(id)).length;
      selectAll.checked       = n > 0 && n === visibleIds.length;
      selectAll.indeterminate = n > 0 && n <  visibleIds.length;
    }
    updateDeleteButton();
  });

  // Select-all checkbox
  document.querySelector('#table-history thead').addEventListener('change', function(e) {
    if (e.target.id !== 'history-select-all') return;
    historyTable.rows({ page: 'current' }).every(function() {
      const id       = this.node().dataset.id;
      const checkbox = this.node().querySelector('.row-select');
      if (e.target.checked) {
        selectedIds.add(id);
        if (checkbox) checkbox.checked = true;
        this.node().classList.add('table-active');
      } else {
        selectedIds.delete(id);
        if (checkbox) checkbox.checked = false;
        this.node().classList.remove('table-active');
      }
    });
    updateDeleteButton();
  });

  // Delete modal
  const deleteModalEl = document.getElementById('modal-history-delete-confirm');
  const deleteModal   = new bootstrap.Modal(deleteModalEl, { focus: false });

  deleteModalEl.addEventListener('shown.bs.modal', function() {
    const closeBtn = deleteModalEl.querySelector('.btn-close');
    if (closeBtn) closeBtn.focus();
  });

  deleteModalEl.addEventListener('hide.bs.modal', function() {
    const focused = deleteModalEl.querySelector(':focus');
    if (focused) focused.blur();
    const btn = document.getElementById('btn-history-delete');
    if (btn && !btn.disabled) btn.focus();
  });

  document.getElementById('btn-history-delete').addEventListener('click', function() {
    document.getElementById('history-delete-modal-count').textContent = selectedIds.size;
    deleteModal.show();
  });

  document.getElementById('btn-history-delete-confirm').addEventListener('click', function() {
    const ids = Array.from(selectedIds);
    fetch('/start/history/delete', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ids }),
    })
    .then(res => res.json())
    .then(() => {
      deleteModal.hide();
      selectedIds.clear();
      updateDeleteButton();
      historyTable.rows(function(idx, data, node) {
        return ids.includes(node.dataset.id);
      }).remove().draw(false);
    })
    .catch(err => console.error('Delete failed:', err));
  });
});