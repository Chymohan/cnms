// js/main.js

document.addEventListener("DOMContentLoaded", () => {
  // Mobile sidebar toggle
  const menuBtn = document.getElementById("mobile-menu-btn");
  const closeBtn = document.getElementById("close-sidebar");
  const sidebar = document.getElementById("mobile-sidebar");
  const overlay = document.getElementById("sidebar-overlay");
  const menuIconPath = document.getElementById("menu-icon-path");

  function openSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove("translate-x-full");
    if (overlay) overlay.classList.remove("hidden");
    document.body.classList.add("overflow-hidden");
    if (menuIconPath) {
      menuIconPath.setAttribute("d", "M6 18L18 6M6 6l12 12");
    }
  }

  function closeSidebar() {
    if (!sidebar) return;
    sidebar.classList.add("translate-x-full");
    if (overlay) overlay.classList.add("hidden");
    document.body.classList.remove("overflow-hidden");
    if (menuIconPath) {
      menuIconPath.setAttribute("d", "M4 6h16M4 12h16M4 18h16");
    }
  }

  if (menuBtn) menuBtn.addEventListener("click", openSidebar);
  if (closeBtn) closeBtn.addEventListener("click", closeSidebar);
  if (overlay) overlay.addEventListener("click", closeSidebar);

  // Close sidebar when clicking any link inside it (mobile)
  if (sidebar) {
    sidebar.querySelectorAll("a").forEach((link) => {
      link.addEventListener("click", closeSidebar);
    });
  }

  // Active link highlighting (works on all pages)
  const currentPage = window.location.pathname.split("/").pop() || "index.html";
  document.querySelectorAll(".nav-link").forEach((link) => {
    const href = link.getAttribute("href");
    if (href === currentPage || (currentPage === "" && href === "index.html")) {
      link.classList.remove("text-gray-800", "text-gray-600", "text-gray-700", "text-gray-800");

      link.classList.add("underline", "text-blue-600", "font-semibold");
      // Optional: highlight style for mobile sidebar
      if (window.innerWidth < 768) {
        link.classList.add("bg-blue-100");
      }
    }
  });
});
