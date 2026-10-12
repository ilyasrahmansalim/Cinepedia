document.addEventListener("DOMContentLoaded", () => {
  // 1. Toggle Dropdown User Profile
  const dropdownBtn = document.getElementById("userDropdownBtn");
  const dropdownMenu = document.getElementById("userDropdownMenu");
  const userDropdown = dropdownBtn ? dropdownBtn.closest(".user-dropdown") : null;

  if (dropdownBtn && dropdownMenu) {
    // Buka / tutup dropdown saat tombol profil diklik
    dropdownBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle("show");
      userDropdown.classList.toggle("active");
    });

    // Tutup dropdown jika mengklik di luar area dropdown
    document.addEventListener("click", (e) => {
      if (!userDropdown.contains(e.target)) {
        dropdownMenu.classList.remove("show");
        userDropdown.classList.remove("active");
      }
    });
  }

  // 2. Filter / Search Sederhana untuk Tabel Movie
  const searchInput = document.getElementById("searchInput");
  const tableRows = document.querySelectorAll("#movieTableBody tr");

  if (searchInput) {
    searchInput.addEventListener("keyup", (e) => {
      const searchTerm = e.target.value.toLowerCase();

      tableRows.forEach((row) => {
        const textContent = row.textContent.toLowerCase();
        if (textContent.includes(searchTerm)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });
  }
});