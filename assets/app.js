// JS seperlunya saja — semua logika data ada di PHP.
document.addEventListener("DOMContentLoaded", () => {
  // 1. Dropdown profil (buka/tutup, tutup saat klik di luar atau tekan Esc)
  const dropdown = document.querySelector(".user-dropdown");
  const toggle = dropdown && dropdown.querySelector("[data-dropdown]");
  if (toggle) {
    toggle.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("open");
    });
    document.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("open");
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") dropdown.classList.remove("open");
    });
  }

  // 2. Konfirmasi sebelum submit form yang punya data-confirm (hapus film)
  document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", (e) => {
      if (!confirm(form.dataset.confirm)) e.preventDefault();
    });
  });

  // 3. Poster gagal dimuat -> tampilkan latar gradien pengganti
  document.querySelectorAll("img.poster").forEach((img) => {
    const fail = () => img.closest(".poster-wrap").classList.add("no-poster");
    if (img.complete && img.naturalWidth === 0) fail();
    img.addEventListener("error", fail);
  });

  // 4. Preview poster langsung saat URL diubah (form edit admin)
  const posterInput = document.querySelector("[data-poster-input]");
  const preview = document.querySelector(".preview");
  if (posterInput && preview) {
    posterInput.addEventListener("input", () => {
      let img = preview.querySelector("img");
      if (!img) {
        img = document.createElement("img");
        img.className = "poster";
        img.alt = "Preview poster";
        preview.appendChild(img);
      }
      preview.classList.remove("no-poster");
      img.onerror = () => preview.classList.add("no-poster");
      img.src = posterInput.value.trim();
    });
  }
});
