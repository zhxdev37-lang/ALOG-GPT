/**
 * Dashboard JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
  // Mobile sidebar toggle
  const sidebarToggle = document.querySelector('.sidebar-toggle');
  const sidebar = document.querySelector('.dashboard-sidebar');
  
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }
  
  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (window.innerWidth >= 992) return;
    if (!sidebar?.contains(e.target) && !sidebarToggle?.contains(e.target)) {
      sidebar?.classList.remove('show');
    }
  });
});
