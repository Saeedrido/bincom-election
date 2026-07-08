document.addEventListener('DOMContentLoaded', () => {

  // Sidebar toggle
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const mainContent = document.getElementById('mainContent');
  const topbar = document.getElementById('topbar');

  const toggleSidebar = () => {
    if (window.innerWidth <= 991.98) {
      sidebar.classList.toggle('mobile-open');
      sidebarOverlay.classList.toggle('show');
    } else {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
      topbar.classList.toggle('collapsed');
      localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }
  };

  if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
  if (sidebarCollapseBtn) {
    sidebarCollapseBtn.addEventListener('click', (e) => {
      e.preventDefault();
      toggleSidebar();
    });
  }
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', () => {
      sidebar.classList.remove('mobile-open');
      sidebarOverlay.classList.remove('show');
    });
  }

  if (window.innerWidth > 991.98 && localStorage.getItem('sidebarCollapsed') === 'true') {
    sidebar.classList.add('collapsed');
    mainContent.classList.add('expanded');
    topbar.classList.add('collapsed');
  }

  // Dynamic wards: fetch via AJAX when LGA changes
  const lgaSelect = document.getElementById('lga_id');
  const wardSelect = document.getElementById('ward_id');
  const wardLoader = document.getElementById('wardLoader');

  if (lgaSelect && wardSelect) {
    lgaSelect.addEventListener('change', () => {
      const lgaId = lgaSelect.value;
      wardSelect.innerHTML = '<option value="">— Select Ward —</option>';
      wardSelect.disabled = true;

      if (!lgaId) {
        if (wardLoader) wardLoader.style.display = 'none';
        return;
      }

      if (wardLoader) wardLoader.style.display = 'inline-block';

      fetch('index.php?page=get-wards&lga_id=' + lgaId)
        .then(r => r.json())
        .then(wards => {
          if (wardLoader) wardLoader.style.display = 'none';
          if (wards.length > 0) {
            wards.forEach(w => {
              const opt = document.createElement('option');
              opt.value = w.uniqueid;
              opt.textContent = w.ward_name;
              wardSelect.appendChild(opt);
            });
            wardSelect.disabled = false;
          } else {
            wardSelect.innerHTML = '<option value="">No wards found</option>';
          }
        })
        .catch(() => {
          if (wardLoader) wardLoader.style.display = 'none';
          wardSelect.innerHTML = '<option value="">Error loading wards</option>';
        });
    });

    if (lgaSelect.value) lgaSelect.dispatchEvent(new Event('change'));
  }

  // Client-side form validation for Add Result
  const addResultForm = document.getElementById('addResultForm');
  if (addResultForm) {
    addResultForm.addEventListener('submit', (e) => {
      let isValid = true;

      const puName = document.getElementById('polling_unit_name');
      const puNumber = document.getElementById('polling_unit_number');
      const stateSelect = document.getElementById('state_id');
      const lga = document.getElementById('lga_id');
      const ward = document.getElementById('ward_id');

      const mark = (el, valid) => {
        el.classList.toggle('is-invalid', !valid);
        if (!valid) isValid = false;
      };

      mark(puName, puName.value.trim() !== '');
      mark(puNumber, puNumber.value.trim() !== '');
      mark(stateSelect, !!stateSelect.value);
      mark(lga, !!lga.value);
      mark(ward, !!ward.value);

      const scores = document.querySelectorAll('.party-score');
      let hasScore = false;
      scores.forEach(input => {
        if (input.value.trim() !== '') {
          const score = parseInt(input.value, 10);
          if (isNaN(score) || score < 0) {
            input.classList.add('is-invalid');
            isValid = false;
          } else {
            input.classList.remove('is-invalid');
            hasScore = true;
          }
        }
      });

      if (!hasScore) {
        if (scores.length > 0) scores[0].classList.add('is-invalid');
        isValid = false;
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger';
        alertDiv.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Please enter at least one party score.';
        addResultForm.prepend(alertDiv);
      }

      if (!isValid) {
        e.preventDefault();
        const first = addResultForm.querySelector('.is-invalid');
        if (first) first.focus({ preventScroll: true });
      } else {
        const btn = document.getElementById('submitBtn');
        if (btn) {
          btn.disabled = true;
          btn.innerHTML = '<div class="spinner spinner-sm"></div> Submitting...';
        }
      }
    });

    addResultForm.querySelectorAll('.form-control, .form-select').forEach(el => {
      el.addEventListener('input', function () { this.classList.remove('is-invalid'); });
      el.addEventListener('change', function () { this.classList.remove('is-invalid'); });
    });
  }

  // Real-time polling unit search in the topbar
  const searchInput = document.getElementById('searchPollingUnit');
  const searchResults = document.getElementById('searchResults');
  let searchTimeout;

  if (searchInput && searchResults) {
    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      const query = searchInput.value.trim();
      if (query.length < 2) {
        searchResults.innerHTML = '';
        searchResults.classList.remove('show');
        return;
      }
      searchTimeout = setTimeout(() => {
        fetch('index.php?page=search&q=' + encodeURIComponent(query))
          .then(r => r.json())
          .then(data => {
            searchResults.innerHTML = '';
            if (data.length > 0) {
              data.forEach(item => {
                const a = document.createElement('a');
                a.href = 'index.php?page=polling-result&pu_id=' + item.uniqueid;
                a.className = 'search-result-item';
                a.innerHTML = '<i class="bi bi-geo-alt"></i><div><div class="result-title">' +
                  item.polling_unit_name + ' (' + item.polling_unit_number + ')</div>' +
                  '<div class="result-sub">' + item.ward_name + ', ' + item.lga_name + '</div></div>';
                searchResults.appendChild(a);
              });
            } else {
              searchResults.innerHTML = '<div style="padding:16px;text-align:center;color:var(--color-muted);font-size:13px;">No results found</div>';
            }
            searchResults.classList.add('show');
          })
          .catch(() => {});
      }, 300);
    });

    document.addEventListener('click', (e) => {
      if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.classList.remove('show');
      }
    });
  }

  // Auto-dismiss alerts
  document.querySelectorAll('.alert-dismissible').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.3s ease';
      alert.style.opacity = '0';
      setTimeout(() => {
        if (alert.parentNode) alert.parentNode.removeChild(alert);
      }, 300);
    }, 5000);
  });

});
