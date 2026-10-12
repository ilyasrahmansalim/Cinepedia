
/* =========================
   CINEPEDIA - MOVIE DATA
   Nantinya bisa diganti dengan data PHP/MySQL
========================= */

const movies = [
    {
        title: "Dune: Part Two",
        year: 2024,
        rating: 8.8,
        genre: ["Sci-Fi", "Adventure"],
        duration: "2h 46m",
        poster: "https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg",
        trending: true
    },
    {
        title: "Civil War",
        year: 2024,
        rating: 8.4,
        genre: ["Action", "Drama"],
        duration: "1h 49m",
        poster: "https://image.tmdb.org/t/p/w500/sh7Rg8Er3tFcN9BpKIPOMvALgZd.jpg",
        trending: true
    },
    {
        title: "Furiosa: A Mad Max Saga",
        year: 2024,
        rating: 8.1,
        genre: ["Action", "Sci-Fi"],
        duration: "2h 28m",
        poster: "https://image.tmdb.org/t/p/w500/iADOJ8Zymht2JPMoy3R7xceZprc.jpg",
        trending: true
    },
    {
        title: "Oppenheimer",
        year: 2023,
        rating: 8.9,
        genre: ["Biography", "Drama"],
        duration: "3h 00m",
        poster: "https://image.tmdb.org/t/p/w500/ptpr0kGAckfQkJeJIt8st5dglvd.jpg",
        trending: false
    },
    {
        title: "Poor Things",
        year: 2023,
        rating: 8.5,
        genre: ["Comedy", "Drama"],
        duration: "2h 21m",
        poster: "https://image.tmdb.org/t/p/w500/kCGlIMHnOm8JPXq3rxffnJCDzq5.jpg",
        trending: true
    },
    {
        title: "The Godfather",
        year: 1972,
        rating: 9.2,
        genre: ["Crime", "Drama"],
        duration: "2h 55m",
        poster: "https://image.tmdb.org/t/p/w500/3bhkrj58Vtu7enYsRolD1fZdja1.jpg",
        trending: false
    },
    {
        title: "The Dark Knight",
        year: 2008,
        rating: 9.0,
        genre: ["Action", "Crime"],
        duration: "2h 32m",
        poster: "https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg",
        trending: false
    },
    {
        title: "Interstellar",
        year: 2014,
        rating: 8.7,
        genre: ["Sci-Fi", "Adventure"],
        duration: "2h 49m",
        poster: "https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6Mnf7f6hS.jpg",
        trending: false
    },
    {
        title: "Pulp Fiction",
        year: 1994,
        rating: 8.9,
        genre: ["Crime", "Drama"],
        duration: "2h 34m",
        poster: "https://image.tmdb.org/t/p/w500/d5iIlFn5s0ImszYzBPb8JPIfbXD.jpg",
        trending: false
    },
    {
        title: "Fight Club",
        year: 1999,
        rating: 8.8,
        genre: ["Drama", "Thriller"],
        duration: "2h 19m",
        poster: "https://image.tmdb.org/t/p/w500/pB8BM7pdSp6B6Ih7QZ4Dr3PmJK.jpg",
        trending: false
    },
    {
        title: "John Wick: Chapter 4",
        year: 2023,
        rating: 8.7,
        genre: ["Action", "Thriller"],
        duration: "2h 49m",
        poster: "https://image.tmdb.org/t/p/w500/vZloFAK7NmvMGKE7VkF5UHaz0I.jpg",
        trending: true
    },
    {
        title: "The Batman",
        year: 2022,
        rating: 8.2,
        genre: ["Crime", "Drama"],
        duration: "2h 56m",
        poster: "https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg",
        trending: false
    },
    {
        title: "Blade Runner 2049",
        year: 2017,
        rating: 8.5,
        genre: ["Sci-Fi", "Mystery"],
        duration: "2h 44m",
        poster: "https://image.tmdb.org/t/p/w500/gajva2L0rPYkEWjzgFlBXCAVBE5.jpg",
        trending: false
    },
    {
        title: "Mad Max: Fury Road",
        year: 2015,
        rating: 8.9,
        genre: ["Action", "Adventure"],
        duration: "2h 00m",
        poster: "https://image.tmdb.org/t/p/w500/hA2ple9q4qnwxp3hKVNhroipsir.jpg",
        trending: false
    },
    {
        title: "Godzilla Minus One",
        year: 2023,
        rating: 8.7,
        genre: ["Sci-Fi", "Action"],
        duration: "2h 04m",
        poster: "https://image.tmdb.org/t/p/w500/hkxxMIGaiCTmrEArK7J56JTKUlB.jpg",
        trending: true
    }
];

/* =========================
   ELEMENTS
========================= */

const movieGrid = document.getElementById("movieGrid");
const searchInput = document.getElementById("searchInput");
const clearSearch = document.getElementById("clearSearch");
const emptyState = document.getElementById("emptyState");
const filterButtons = document.querySelectorAll(".filter-btn");

let activeFilter = "all";

/* =========================
   DISPLAY MOVIES
========================= */

function displayMovies(data) {
    movieGrid.innerHTML = "";

    data.forEach(movie => {
        const card = document.createElement("article");
        card.className = "movie-card";

        const posterWrap = document.createElement("div");
        posterWrap.className = "poster-wrap";

        const poster = document.createElement("img");
        poster.className = "poster";
        poster.src = movie.poster;
        poster.alt = `Poster ${movie.title}`;
        poster.loading = "lazy";

        // Jika poster gagal dimuat, tampilkan pengganti sederhana.
        poster.addEventListener("error", function () {
            this.removeAttribute("src");
            this.alt = `Poster ${movie.title} tidak tersedia`;
            this.style.display = "none";
            posterWrap.style.background =
                "linear-gradient(145deg, #31080d, #111111)";
        }, { once: true });

        const rating = document.createElement("span");
        rating.className = "rating";
        rating.textContent = `★ ${movie.rating.toFixed(1)}`;

        const year = document.createElement("span");
        year.className = "year";
        year.textContent = movie.year;

        posterWrap.append(poster, rating, year);

        const info = document.createElement("div");
        info.className = "movie-info";

        const title = document.createElement("h2");
        title.className = "movie-title";
        title.textContent = movie.title;
        title.title = movie.title;

        const meta = document.createElement("div");
        meta.className = "movie-meta";

        const genre = document.createElement("span");
        genre.className = "movie-genre";
        genre.textContent = movie.genre.join(", ");

        const duration = document.createElement("span");
        duration.className = "movie-duration";
        duration.textContent = movie.duration;

        meta.append(genre, duration);
        info.append(title, meta);
        card.append(posterWrap, info);
        movieGrid.appendChild(card);
    });

    emptyState.hidden = data.length > 0;
    movieGrid.hidden = data.length === 0;
}

/* =========================
   SEARCH AND FILTER
========================= */

function filterMovies() {
    const keyword = searchInput.value.trim().toLowerCase();

    let filteredMovies = movies.filter(movie => {
        const searchableText = [
            movie.title,
            movie.year.toString(),
            ...movie.genre
        ].join(" ").toLowerCase();

        const matchesSearch = searchableText.includes(keyword);

        let matchesFilter = true;

        if (activeFilter === "trending") {
            matchesFilter = movie.trending;
        } else if (activeFilter === "top-rated") {
            matchesFilter = movie.rating >= 8.5;
        } else if (activeFilter !== "all") {
            matchesFilter = movie.genre.includes(activeFilter);
        }

        return matchesSearch && matchesFilter;
    });

    // Urutkan rating tertinggi pada tab Top Rated.
    if (activeFilter === "top-rated") {
        filteredMovies.sort((a, b) => b.rating - a.rating);
    }

    displayMovies(filteredMovies);
    clearSearch.style.display = keyword ? "block" : "none";
}

// Pencarian berdasarkan judul, tahun, dan genre.
searchInput.addEventListener("input", filterMovies);

// Filter kategori.
filterButtons.forEach(button => {
    button.addEventListener("click", function () {
        activeFilter = this.dataset.filter;

        filterButtons.forEach(item => {
            item.classList.toggle("active", item === this);
        });

        filterMovies();
    });
});

// Hapus kata kunci pencarian.
clearSearch.addEventListener("click", function () {
    searchInput.value = "";
    filterMovies();
    searchInput.focus();
});

// Kembali ke seluruh daftar film.
document.getElementById("resetSearch").addEventListener("click", function () {
    searchInput.value = "";
    activeFilter = "all";

    filterButtons.forEach(button => {
        button.classList.toggle(
            "active",
            button.dataset.filter === "all"
        );
    });

    filterMovies();
});

// LOGIN sengaja tidak diberi event atau navigasi.

displayMovies(movies);
