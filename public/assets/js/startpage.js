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
  const spinnerHtml = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
  const iconHtml    = '<i class="bi bi-chevron-right"></i>';

  const toastEl   = document.getElementById('command-toast');
  const toastBody = document.getElementById('command-toast-body');
  const toast     = toastEl ? new bootstrap.Toast(toastEl, { delay: 4000 }) : null;

  function showNotification(message) {
    if (toast && toastBody) {
      toastBody.textContent = message;
      toast.show();
    }
  }

  function resetForm() {
    const qIcon  = document.getElementById('q-icon');
    const qInput = document.getElementById('q');
    if (qIcon)  qIcon.innerHTML = iconHtml;
    if (qInput) {
      qInput.removeAttribute('disabled');
      qInput.value = '';
      qInput.focus();
    }
  }

  document.getElementById('form-q')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const qInput = document.getElementById('q');
    const q      = qInput?.value.trim() ?? '';
    if (!q) return;

    document.getElementById('q-icon').innerHTML = spinnerHtml;
    qInput.setAttribute('disabled', 'disabled');

    fetch('/command', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ q }),
    })
      .then(res => res.json())
      .then(data => {
        if (data.url) {
          window.location.href = data.url;
          return;
        }
        if (data.html) {
          document.getElementById('response_html').innerHTML = data.html;
          document.getElementById('response_holder').classList.remove('d-none');
          resetForm();
          return;
        }
        if (data.notification) {
          showNotification(data.notification);
          resetForm();
          return;
        }
        resetForm();
      })
      .catch(() => resetForm());
  });

  // Clear HTML response
  document.getElementById('btn-clear-html-response')?.addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('response_html').innerHTML = '';
    document.getElementById('response_holder').classList.add('d-none');
    document.getElementById('q')?.focus();
  });

  // Populate search input from a redirect anchor click
  document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('anchor-redirect')) return;
    e.preventDefault();
    const qInput = document.getElementById('q');
    if (qInput) {
      qInput.value = e.target.textContent.trim();
      document.getElementById('form-q')?.dispatchEvent(new Event('submit'));
    }
  });

  // Pre-fill search input with search-engine phrase on anchor click
  document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('anchor-search-engine')) return;
    e.preventDefault();
    const qInput = document.getElementById('q');
    if (qInput) {
      qInput.value = e.target.textContent.trim() + ' ';
      qInput.focus();
    }
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const selectedIds = new Set();

  function updateDeleteButton() {
    document.getElementById('btn-history-delete').disabled = selectedIds.size === 0;
  }

  // Collect history queries before DataTable reorders the DOM
  const historyQueries = Array.from(
    document.querySelectorAll('#table-history tbody tr td:nth-child(3) a')
  ).map(a => a.textContent.trim());

  // Up/down arrow navigation through history on the #q input
  const qNavInput = document.getElementById('q');
  if (qNavInput && historyQueries.length > 0) {
    let historyNavIndex = -1;
    let historyNavSaved = '';

    qNavInput.addEventListener('keydown', function(e) {
      if (e.key !== 'ArrowUp' && e.key !== 'ArrowDown') return;
      const typeahead = document.getElementById('q-typeahead');
      if (typeahead && !typeahead.classList.contains('d-none')) return;
      e.preventDefault();

      if (historyNavIndex === -1) {
        historyNavSaved = qNavInput.value;
      }

      if (e.key === 'ArrowUp') {
        historyNavIndex = Math.min(historyNavIndex + 1, historyQueries.length - 1);
      } else {
        historyNavIndex = Math.max(historyNavIndex - 1, -1);
      }

      qNavInput.value = historyNavIndex === -1 ? historyNavSaved : historyQueries[historyNavIndex];
    });

    qNavInput.addEventListener('input', function() {
      historyNavIndex = -1;
    });

    document.getElementById('form-q')?.addEventListener('submit', function() {
      historyNavIndex = -1;
    });
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

document.addEventListener('DOMContentLoaded', () => {
  const qInput = document.getElementById('q');
  if (!qInput) return;

  const inputGroup = qInput.closest('.input-group');
  if (!inputGroup) return;

  inputGroup.style.position = 'relative';

  const dropdown = document.createElement('ul');
  dropdown.id = 'q-typeahead';
  dropdown.className = 'q-typeahead d-none';
  dropdown.setAttribute('role', 'listbox');
  inputGroup.appendChild(dropdown);

  let activeIndex = -1;
  let debounceTimer = null;
  let currentSuggestions = [];

  function showDropdown(suggestions) {
    currentSuggestions = suggestions;
    activeIndex = -1;
    dropdown.innerHTML = '';

    if (suggestions.length === 0) {
      hideDropdown();
      return;
    }

    suggestions.forEach((text, i) => {
      const li = document.createElement('li');
      li.className = 'q-typeahead__item';
      li.setAttribute('role', 'option');
      li.dataset.index = i;
      li.textContent = text;

      li.addEventListener('mousedown', (e) => {
        e.preventDefault();
        selectSuggestion(text);
      });

      dropdown.appendChild(li);
    });

    dropdown.classList.remove('d-none');
  }

  function hideDropdown() {
    dropdown.classList.add('d-none');
    activeIndex = -1;
    currentSuggestions = [];
  }

  function setActiveItem(index) {
    dropdown.querySelectorAll('.q-typeahead__item').forEach((item, i) => {
      item.classList.toggle('active', i === index);
    });
    activeIndex = index;
  }

  function selectSuggestion(text) {
    qInput.value = text;
    hideDropdown();
    qInput.focus();
  }

  qInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const q = qInput.value.trim();

    if (q.length === 0) {
      hideDropdown();
      return;
    }

    debounceTimer = setTimeout(() => {
      fetch('/start/history/suggestions?q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => showDropdown(Array.isArray(data) ? data : []))
        .catch(() => hideDropdown());
    }, 200);
  });

  qInput.addEventListener('keydown', (e) => {
    if (dropdown.classList.contains('d-none')) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      setActiveItem(Math.min(activeIndex + 1, currentSuggestions.length - 1));
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      setActiveItem(Math.max(activeIndex - 1, -1));
    } else if (e.key === 'Enter' && activeIndex >= 0) {
      e.preventDefault();
      selectSuggestion(currentSuggestions[activeIndex]);
    } else if (e.key === 'Escape') {
      hideDropdown();
    }
  });

  qInput.addEventListener('blur', () => {
    setTimeout(() => hideDropdown(), 150);
  });

  document.getElementById('form-q')?.addEventListener('submit', () => {
    hideDropdown();
  });
});