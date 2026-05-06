/**
 * Admin JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
  // Mobile sidebar toggle
  const sidebarToggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.admin-sidebar');
  
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }
  
  // Confirm delete actions
  document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      if (!confirm(btn.dataset.confirm || 'Êtes-vous sûr ?')) {
        e.preventDefault();
      }
    });
  });
  
  // Dynamic filiere select based on level
  const levelSelect = document.querySelector('[data-filiere-level]');
  const filiereSelect = document.querySelector('[data-filiere-target]');
  
  if (levelSelect && filiereSelect) {
    levelSelect.addEventListener('change', async function() {
      const levelId = this.value;
      if (!levelId) return;
      
      try {
        const response = await fetch(`?level_id=${levelId}`);
        const data = await response.json();
        filiereSelect.innerHTML = '<option value="">Non applicable</option>';
        data.forEach(f => {
          const opt = document.createElement('option');
          opt.value = f.id;
          opt.textContent = f.name;
          filiereSelect.appendChild(opt);
        });
      } catch(e) {}
    });
  }
});
