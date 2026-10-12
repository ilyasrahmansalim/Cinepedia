const MOVIES = [
  {
    id: "oppenheimer", title: "Oppenheimer", year: 2023, genres: ["Biography", "Drama", "History"], genreKeys: ["drama"], runtime: "3h 0min", director: "Christopher Nolan", rating: "8.9", votes: "712,450+",
    synopsis: "The story of American scientist J. Robert Oppenheimer and his pivotal role in developing the atomic bomb during World War II. Recruited to direct the Manhattan Project at Los Alamos, Oppenheimer confronts the monumental scientific, political, and moral shockwaves that permanently reshape humanity’s destiny.",
    poster: "https://image.tmdb.org/t/p/w500/ptpr0kGAckfQkJeJIt8st5dglvd.jpg", trailer: "https://www.youtube.com/watch?v=uYPbbksJxIg",
    blurb: "A physicist faces the world-changing consequences of his work on the atomic bomb.",
    reviews: [
      { name: "Arthur_V", date: "2 days ago", rating: 5, text: "Christopher Nolan delivers a towering historical masterwork. Cillian Murphy’s performance is utterly haunting and the tension during the Trinity sequence is legendary." },
      { name: "Sarah Jenkins", date: "1 week ago", rating: 4, text: "Exceptional editing and sound design. Robert Downey Jr. gives one of his finest performances. The final hour is surprisingly dense with politics but stays captivating." },
      { name: "Cinephile99", date: "2 weeks ago", rating: 5, text: "Saw this on 70mm IMAX and was blown away. Ludwig Göransson’s musical score vibrates right through your chest. An absolute triumph." },
      { name: "David Miller", date: "3 weeks ago", rating: 4, text: "Intense, dialogue-heavy and thoroughly uncompromising. Not an easy watch, but an unforgettable cinematic achievement." }
    ]
  },
  {
    id: "interstellar", title: "Interstellar", year: 2014, genres: ["Adventure", "Drama", "Sci-Fi"], genreKeys: ["drama", "sci-fi"], runtime: "2h 49min", director: "Christopher Nolan", rating: "8.7", votes: "2,250,000+",
    synopsis: "When Earth becomes increasingly uninhabitable, a team of explorers travels beyond this galaxy to discover whether mankind has a future among the stars. At its heart is a father’s promise to return home to his daughter.",
    poster: "https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg", trailer: "https://www.youtube.com/watch?v=zSWdZVtXT7E",
    blurb: "A team of explorers searches for a new home for humanity beyond the stars.",
    reviews: [
      { name: "OrbitWatcher", date: "3 days ago", rating: 5, text: "A moving science-fiction epic with breathtaking visuals and a powerful emotional core." },
      { name: "MiraK", date: "1 week ago", rating: 5, text: "The score, scale and father-daughter story work together beautifully. It stays with you long after it ends." },
      { name: "FrameByFrame", date: "2 weeks ago", rating: 4, text: "Ambitious and visually extraordinary, with ideas that make you want to watch it again." }
    ]
  },
  {
    id: "inception", title: "Inception", year: 2010, genres: ["Action", "Adventure", "Sci-Fi"], genreKeys: ["sci-fi", "action", "thriller"], runtime: "2h 28min", director: "Christopher Nolan", rating: "8.8", votes: "2,600,000+",
    synopsis: "A skilled thief who steals secrets through dream-sharing technology is offered a chance to have his criminal record erased. His new mission is not to steal an idea, but to plant one inside a target’s mind.",
    poster: "https://image.tmdb.org/t/p/w500/oYuLEt3zVCKq57qu2F8dT7NIa6f.jpg", trailer: "https://www.youtube.com/watch?v=YoHD9XEInc0",
    blurb: "A professional dream thief is hired for a seemingly impossible job: inception.",
    reviews: [
      { name: "DreamLogic", date: "yesterday", rating: 5, text: "A clever, layered thriller that balances its mind-bending premise with real emotional stakes." },
      { name: "NolanFan", date: "5 days ago", rating: 5, text: "The production design and editing make every dream level feel distinct and urgent." },
      { name: "ReelTalk", date: "2 weeks ago", rating: 4, text: "Dense on the first viewing, rewarding on the second. The ending still invites discussion." }
    ]
  },
  {
    id: "the-dark-knight", title: "The Dark Knight", year: 2008, genres: ["Action", "Crime", "Drama"], genreKeys: ["action", "drama", "thriller"], runtime: "2h 32min", director: "Christopher Nolan", rating: "9.0", votes: "2,900,000+",
    synopsis: "Batman raises the stakes in his war on crime with the help of Lieutenant Jim Gordon and District Attorney Harvey Dent. Their effort is tested by a criminal mastermind who wants to push Gotham into chaos.",
    poster: "https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg", trailer: "https://www.youtube.com/watch?v=EXeTwQWrcwY",
    blurb: "Batman faces a criminal mastermind who turns Gotham against itself.",
    reviews: [
      { name: "GothamFilm", date: "2 days ago", rating: 5, text: "A tense crime saga with outstanding performances and a villain who changes the entire mood of the film." },
      { name: "NerdAtHeart", date: "1 week ago", rating: 5, text: "The practical action, moral conflicts and pacing make this superhero film feel timeless." },
      { name: "CinemaNotes", date: "1 month ago", rating: 4, text: "Dark, confident filmmaking with several unforgettable sequences." }
    ]
  },
  {
    id: "dune-part-two", title: "Dune: Part Two", year: 2024, genres: ["Action", "Adventure", "Drama"], genreKeys: ["action", "drama"], runtime: "2h 46min", director: "Denis Villeneuve", rating: "8.5", votes: "680,000+",
    synopsis: "Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family. He must choose between the love of his life and the fate of the universe.",
    poster: "https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg", trailer: "https://www.youtube.com/watch?v=Way9Dexny3w",
    blurb: "Paul Atreides joins the Fremen as war and prophecy converge on Arrakis.",
    reviews: [
      { name: "DesertCinema", date: "4 days ago", rating: 5, text: "Massive in scale, but its strongest moments remain personal. The visuals are extraordinary." },
      { name: "ScreenNomad", date: "2 weeks ago", rating: 4, text: "An immersive, patient science-fiction epic with powerful sound and striking compositions." },
      { name: "FilmShelf", date: "1 month ago", rating: 4, text: "A confident continuation that makes Arrakis feel vast, dangerous and alive." }
    ]
  },
  {
    id: "spider-verse", title: "Across the Spider-Verse", year: 2023, genres: ["Animation", "Action", "Adventure"], genreKeys: ["action"], runtime: "2h 20min", director: "Joaquim Dos Santos, Kemp Powers, Justin K. Thompson", rating: "8.6", votes: "390,000+",
    synopsis: "Miles Morales reunites with Gwen Stacy and is launched across the Multiverse, where he encounters a team of Spider-People protecting its existence. A conflict over what it means to be a hero forces Miles to define his own path.",
    poster: "https://image.tmdb.org/t/p/w500/8Vt6mWEReuy4Of61Lnj5Xj704m8.jpg", trailer: "https://www.youtube.com/watch?v=shW9i6k8cB0",
    blurb: "Miles Morales swings into a multiverse adventure filled with new Spider-heroes.",
    reviews: [
      { name: "ColorTheory", date: "2 days ago", rating: 5, text: "A visual playground where each universe has its own personality and animation language." },
      { name: "WebHead", date: "1 week ago", rating: 5, text: "Energetic, heartfelt and inventive. Every frame feels crafted rather than generic." },
      { name: "MovieMixer", date: "3 weeks ago", rating: 4, text: "A spectacular chapter with a cliffhanger that leaves you wanting the next part." }
    ]
  },
  {
    id: "arrival", title: "Arrival", year: 2016, genres: ["Drama", "Mystery", "Sci-Fi"], genreKeys: ["drama", "sci-fi", "thriller"], runtime: "1h 56min", director: "Denis Villeneuve", rating: "7.9", votes: "760,000+",
    synopsis: "When mysterious spacecraft arrive around the world, a linguist is recruited to communicate with the visitors. As she searches for a way to understand them, she uncovers a discovery that changes her view of time and humanity.",
    poster: "https://image.tmdb.org/t/p/w500/x2FJsf1ElAgr63Y3PNPtJrcmpoe.jpg", trailer: "https://www.youtube.com/results?search_query=Arrival+2016+official+trailer",
    blurb: "A linguist attempts to communicate with mysterious visitors from beyond Earth.",
    reviews: [
      { name: "QuietFrames", date: "6 days ago", rating: 5, text: "A thoughtful first-contact story built around language, grief and the limits of understanding." },
      { name: "MossAndStars", date: "2 weeks ago", rating: 4, text: "Subtle, patient and beautifully composed, with a reveal that reshapes the whole experience." },
      { name: "SciFiLibrary", date: "1 month ago", rating: 4, text: "More reflective than explosive, and all the better for it." }
    ]
  },
  {
    id: "whiplash", title: "Whiplash", year: 2014, genres: ["Drama", "Music"], genreKeys: ["drama"], runtime: "1h 47min", director: "Damien Chazelle", rating: "8.5", votes: "1,000,000+",
    synopsis: "A young and ambitious jazz drummer enrolls at a cutthroat music conservatory, where his pursuit of greatness is pushed to the limit by an instructor whose teaching methods are both demanding and abusive.",
    poster: "https://image.tmdb.org/t/p/w500/7fn624j5lj3xTme2SgiLCeuedmO.jpg", trailer: "https://www.youtube.com/results?search_query=Whiplash+2014+official+trailer",
    blurb: "A promising drummer is pushed to his limits by a ruthless music instructor.",
    reviews: [
      { name: "TempoKeeper", date: "3 days ago", rating: 5, text: "The sound editing turns every rehearsal into a nerve-racking set piece." },
      { name: "LateShow", date: "2 weeks ago", rating: 5, text: "A compact, intense drama about ambition, obsession and the cost of greatness." },
      { name: "CelluloidKid", date: "1 month ago", rating: 4, text: "The performances are explosive and the final sequence is unforgettable." }
    ]
  }
];

const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];

function setCurrentYear() {
  const year = $("#current-year");
  if (year) year.textContent = new Date().getFullYear();
}

function fallbackPoster(img, title) {
  const frame = img.closest(".poster-frame");
  img.addEventListener("error", () => {
    img.hidden = true;
    const fallback = frame?.querySelector(".poster-fallback");
    if (fallback) {
      const fallbackTitle = fallback.querySelector("strong");
      if (fallbackTitle) fallbackTitle.textContent = title.toUpperCase();
    }
  }, { once: true });
}

function getSavedReviews(movieId) {
  try {
    const value = JSON.parse(localStorage.getItem(`cinepedia-reviews-${movieId}`) || "[]");
    return Array.isArray(value) ? value : [];
  } catch {
    return [];
  }
}

function getSavedRating(movieId) {
  try {
    const rating = Number(localStorage.getItem(`cinepedia-rating-${movieId}`));
    return Number.isInteger(rating) && rating >= 1 && rating <= 5 ? rating : 0;
  } catch {
    return 0;
  }
}

function renderDetail() {
  const query = new URLSearchParams(window.location.search);
  const requestedMovieId = query.get("movie") || "oppenheimer";
  const movie = MOVIES.find(item => item.id === requestedMovieId) || MOVIES[0];

  document.title = `${movie.title} — Cinepedia Movie Detail`;
  $("#movie-title").textContent = movie.title.toUpperCase();
  $("#movie-year").textContent = `(${movie.year})`;
  $("#movie-runtime").textContent = movie.runtime;
  $("#movie-director").textContent = movie.director;
  $("#movie-rating").textContent = movie.rating;
  $("#movie-votes").textContent = movie.votes;
  $("#movie-synopsis").textContent = movie.synopsis;
  $("#poster-fallback-title").textContent = movie.title.toUpperCase();

  const poster = $("#movie-poster");
  poster.src = movie.poster;
  poster.alt = `${movie.title} (${movie.year}) movie poster`;
  fallbackPoster(poster, movie.title);
  $("#trailer-link").href = movie.trailer;
  $("#movie-genres").innerHTML = movie.genres.map(genre => `<span class="genre-tag">${genre}</span>`).join("");

  let selectedRating = getSavedRating(movie.id);
  const starButtons = $$(".rating-star");
  const prompt = $("#rating-prompt");
  const form = $("#review-form");
  const nameInput = $("#review-name");
  const reviewInput = $("#review-text");
  const status = $("#review-form-status");
  const reviewsList = $("#reviews-list");
  const reviewCount = $("#review-count");

  function refreshRatingStars(hoverRating = 0) {
    starButtons.forEach(button => {
      const value = Number(button.dataset.rating);
      button.classList.toggle("is-selected", value <= selectedRating);
      button.classList.toggle("is-hover", hoverRating > 0 && value <= hoverRating);
      button.setAttribute("aria-pressed", String(value === selectedRating));
    });
    prompt.textContent = selectedRating ? `YOUR RATING: ${selectedRating} / 5 STARS` : "CLICK TO RATE (0 / 5 STARS)";
  }

  function renderReviews() {
    const userReviews = getSavedReviews(movie.id);
    const reviews = [...userReviews, ...movie.reviews];
    reviewsList.innerHTML = "";
    reviewCount.textContent = `${reviews.length} ${reviews.length === 1 ? "REVIEW" : "REVIEWS"}`;

    if (!reviews.length) {
      const empty = document.createElement("p");
      empty.className = "no-reviews";
      empty.textContent = "No reviews yet. Be the first to share your thoughts.";
      reviewsList.appendChild(empty);
      return;
    }

    reviews.forEach(review => {
      const article = document.createElement("article");
      article.className = "review-card";
      const top = document.createElement("div");
      top.className = "review-card-top";
      const author = document.createElement("h3");
      author.className = "review-author";
      author.textContent = review.name;
      const stars = document.createElement("span");
      stars.className = "review-stars";
      const userRating = Math.max(0, Math.min(5, Number(review.rating) || 0));
      stars.textContent = "★".repeat(userRating) + "☆".repeat(5 - userRating);
      top.append(author, stars);

      const date = document.createElement("p");
      date.className = "review-date";
      date.textContent = review.date || "Just now";
      const body = document.createElement("p");
      body.className = "review-body";
      body.textContent = review.text;
      article.append(top, date, body);
      reviewsList.appendChild(article);
    });
  }

  starButtons.forEach(button => {
    button.addEventListener("mouseenter", () => refreshRatingStars(Number(button.dataset.rating)));
    button.addEventListener("focus", () => refreshRatingStars(Number(button.dataset.rating)));
    button.addEventListener("mouseleave", () => refreshRatingStars());
    button.addEventListener("blur", () => refreshRatingStars());
    button.addEventListener("click", () => {
      selectedRating = Number(button.dataset.rating);
      try { localStorage.setItem(`cinepedia-rating-${movie.id}`, String(selectedRating)); }
      catch { /* The chosen rating still remains active for this page view. */ }
      refreshRatingStars();
      status.textContent = "Your personal rating has been saved in this browser.";
      status.className = "review-form-status is-success";
    });
  });

  [nameInput, reviewInput].forEach(input => input.addEventListener("input", () => {
    input.removeAttribute("aria-invalid");
    if (status.classList.contains("is-error")) {
      status.textContent = "";
      status.className = "review-form-status";
    }
  }));

  form.addEventListener("submit", event => {
    event.preventDefault();
    status.textContent = "";
    status.className = "review-form-status";
    nameInput.removeAttribute("aria-invalid");
    reviewInput.removeAttribute("aria-invalid");

    let valid = true;
    const name = nameInput.value.trim();
    const text = reviewInput.value.trim();
    if (!name) { nameInput.setAttribute("aria-invalid", "true"); valid = false; }
    if (!text) { reviewInput.setAttribute("aria-invalid", "true"); valid = false; }

    if (!selectedRating) {
      status.textContent = "Please select a star rating before submitting your review.";
      status.className = "review-form-status is-error";
      valid = false;
    } else if (!valid) {
      status.textContent = "Please enter your name and review.";
      status.className = "review-form-status is-error";
    }
    if (!valid) return;

    const newReview = { name: name.slice(0, 40), date: "Just now", rating: selectedRating, text: text.slice(0, 500) };
    const saved = getSavedReviews(movie.id);
    saved.unshift(newReview);
    let savedSuccessfully = true;
    try { localStorage.setItem(`cinepedia-reviews-${movie.id}`, JSON.stringify(saved)); }
    catch { savedSuccessfully = false; }
    renderReviews();
    form.reset();
    nameInput.removeAttribute("aria-invalid");
    reviewInput.removeAttribute("aria-invalid");
    status.textContent = savedSuccessfully ? "Your review has been added in this browser." : "Your review is visible now, but browser storage is unavailable.";
    status.className = "review-form-status is-success";
  });

  refreshRatingStars();
  renderReviews();
}

function setupHeaderSearch() {
  const form = $("#detail-search-form");
  const input = $("#detail-search");
  const feedback = $("#search-feedback");
  if (!form || !input || !feedback) return;

  form.addEventListener("submit", event => {
    event.preventDefault();
    const query = input.value.trim().toLowerCase();
    if (!query) {
      feedback.textContent = "Type a movie title or director to search.";
      feedback.classList.add("is-visible");
      return;
    }
    const exactOrPartialTitle = MOVIES.find(movie => movie.title.toLowerCase() === query)
      || MOVIES.find(movie => movie.title.toLowerCase().includes(query));
    const directorMatch = MOVIES.find(movie => movie.director.toLowerCase().includes(query));
    const match = exactOrPartialTitle || directorMatch;

    if (match) {
      window.location.href = `movie-detail.html?movie=${encodeURIComponent(match.id)}`;
      return;
    }
    feedback.textContent = "No matching film found in this detail page's movie data.";
    feedback.classList.add("is-visible");
  });

  input.addEventListener("input", () => {
    feedback.textContent = "";
    feedback.classList.remove("is-visible");
  });
}

document.addEventListener("DOMContentLoaded", () => {
  setCurrentYear();
  renderDetail();
  setupHeaderSearch();
});
