// Dropdown profil di navbar:
// klik tombol profil untuk buka/tutup, klik di tempat lain untuk menutup.
const profileBtn = document.getElementById("profileBtn");
const userDropdown = document.getElementById("userDropdown");

if (profileBtn) {
  profileBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    userDropdown.classList.toggle("open");
  });

  document.addEventListener("click", function () {
    userDropdown.classList.remove("open");
  });
}

// Carousel di halaman katalog:
// geser otomatis tiap 5 detik, berhenti saat kursor di atasnya / disentuh,
// bisa pakai tombol panah, titik, atau di-swipe di HP.
const carouselTrack = document.getElementById("carouselTrack");

if (carouselTrack && carouselTrack.children.length > 1) {
  const total = carouselTrack.children.length;
  const dots = document.querySelectorAll(".carousel-dot");
  let index = 0;
  let timer = null;

  function goTo(i) {
    index = (i + total) % total; // setelah slide terakhir kembali ke pertama
    carouselTrack.scrollTo({ left: index * carouselTrack.clientWidth, behavior: "smooth" });
  }

  // Perbarui titik aktif setiap track bergeser (termasuk saat di-swipe)
  carouselTrack.addEventListener("scroll", function () {
    index = Math.round(carouselTrack.scrollLeft / carouselTrack.clientWidth);
    dots.forEach(function (dot, n) {
      dot.classList.toggle("active", n === index);
    });
  });

  document.getElementById("carouselPrev").addEventListener("click", function () { goTo(index - 1); });
  document.getElementById("carouselNext").addEventListener("click", function () { goTo(index + 1); });
  dots.forEach(function (dot, n) {
    dot.addEventListener("click", function () { goTo(n); });
  });

  function start() { timer = setInterval(function () { goTo(index + 1); }, 5000); }
  function stop()  { clearInterval(timer); }

  const carousel = document.getElementById("carousel");
  carousel.addEventListener("mouseenter", stop);
  carousel.addEventListener("mouseleave", start);
  carousel.addEventListener("touchstart", stop, { passive: true });
  carousel.addEventListener("touchend", start);

  start();
}