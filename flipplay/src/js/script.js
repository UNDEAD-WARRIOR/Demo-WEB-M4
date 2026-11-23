// ======================================================
// FlipPlay: satu file JavaScript untuk semua halaman
// Bagian: 1) Navbar  2) Form kontak  3) Vibe Grid
// Tiap bagian hanya jalan jika elemennya ada di halaman.
// ======================================================

// ---------- 1. NAVBAR (mobile toggle) ----------
const navToggle = document.querySelector("#nav-toggle");
const navMenu = document.querySelector("#nav-menu");

if (navToggle && navMenu) {
  navToggle.addEventListener("click", () => {
    const isHidden = navMenu.classList.toggle("hidden");
    navToggle.setAttribute("aria-expanded", String(!isHidden));
  });
}

// ---------- 2. FORM KONTAK (index.html) ----------
const contactForm = document.querySelector("#contact-form");

if (contactForm) {
  const status = document.querySelector("#form-status");

  const rules = {
    name: (v) => (v.trim() === "" ? "Nama wajib diisi." : ""),
    email: (v) =>
      v.trim() === ""
        ? "Email wajib diisi."
        : !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)
          ? "Format email belum benar, contoh: nama@mail.com."
          : "",
    message: (v) => (v.trim().length < 10 ? "Pesan minimal 10 karakter." : ""),
  };

  const showError = (field, text) => {
    document.querySelector(`[data-error-for="${field}"]`).textContent = text;
    document.querySelector(`#${field}`).setAttribute("aria-invalid", String(text !== ""));
  };

  // Hapus pesan error saat user mulai memperbaiki isian
  Object.keys(rules).forEach((field) => {
    document.querySelector(`#${field}`).addEventListener("input", () => showError(field, ""));
  });

  contactForm.addEventListener("submit", (event) => {
    event.preventDefault();
    status.textContent = "";

    const errors = Object.keys(rules).map((field) => {
      const message = rules[field](document.querySelector(`#${field}`).value);
      showError(field, message);
      return message;
    });

    if (errors.some((message) => message !== "")) return;

    status.textContent = "Pesan terkirim. Terima kasih sudah menghubungi FlipPlay.";
    contactForm.reset();
  });
}

// ---------- 3. VIBE GRID (vibe-grid.html) ----------
const grid = document.querySelector("#media-grid");

if (grid) {
  // --- 3a. Data Aura ---
  // item.genres berupa array nama genre, misalnya ["Action", "Sci-Fi"]
  // "glow" = warna cahaya latar halaman saat aura dipilih
  // Class Tailwind ditulis lengkap agar terbaca saat compile
  const hasGenre = (item, names) => item.genres.some((genre) => names.includes(genre));

  const auras = {
    all: {
      label: "Semua",
      icon: "✦",
      glow: "#ff5d8f",
      active: "bg-mist text-plum border-mist",
      hint: "Menampilkan semua judul.",
      match: () => true,
    },
    energy: {
      label: "High Energy",
      icon: "⚡",
      glow: "#ff7a45",
      active: "bg-[#ff7a45] text-plum border-[#ff7a45]",
      hint: "High Energy: genre Action, Adventure, Sports, atau Sci-Fi.",
      match: (item) => hasGenre(item, ["Action", "Adventure", "Sports", "Sci-Fi"]),
    },
    melancholy: {
      label: "Melancholy",
      icon: "☾",
      glow: "#8b8cff",
      active: "bg-iris text-plum border-iris",
      hint: "Melancholy: genre Drama, Romance, Tragedy, Psychological, atau Mystery.",
      match: (item) =>
        hasGenre(item, ["Drama", "Romance", "Tragedy", "Psychological", "Mystery", "Suspense"]),
    },
    binge: {
      label: "Binge Mode",
      icon: "∞",
      glow: "#ffc857",
      active: "bg-amber text-plum border-amber",
      hint: "Binge Mode: serial dengan 24 episode atau lebih.",
      match: (item) => (item.episodes ?? 0) >= 24,
    },
  };

  // --- 3b. Fetch API (Kitsu utama, Jikan cadangan) ---
  const KITSU_URL = "https://kitsu.io/api/edge/anime";
  const JIKAN_URL =
    "https://api.jikan.moe/v4/anime?limit=24&order_by=score&sort=desc&min_score=7";

  const fetchJSON = async (url) => {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`status ${response.status}`);
    }
    return response.json();
  };

  // Ubah format Kitsu (JSON:API) menjadi bentuk data yang seragam
  const normalizeKitsu = (json) => {
    const genreNames = new Map(
      (json.included ?? [])
        .filter((entry) => entry.type === "genres")
        .map((genre) => [genre.id, genre.attributes.name])
    );

    return json.data.map((item) => {
      const attr = item.attributes;
      return {
        id: `kitsu-${item.id}`,
        title: attr.canonicalTitle,
        image: attr.posterImage?.small ?? "",
        score: attr.averageRating ? (Number(attr.averageRating) / 10).toFixed(1) : null,
        episodes: attr.episodeCount ?? null,
        genres: (item.relationships?.genres?.data ?? [])
          .map((genre) => genreNames.get(genre.id))
          .filter(Boolean),
      };
    });
  };

  // Ubah format Jikan menjadi bentuk data yang seragam
  const normalizeJikan = (json) =>
    json.data.map((item) => ({
      id: `jikan-${item.mal_id}`,
      title: item.title,
      image: item.images?.jpg?.image_url ?? "",
      score: item.score ?? null,
      episodes: item.episodes ?? null,
      genres: item.genres.map((genre) => genre.name),
    }));

  const getFromKitsu = async () => {
    const offsets = [0, 20]; // Kitsu maksimal 20 data per request
    const pages = await Promise.all(
      offsets.map((offset) =>
        fetchJSON(
          `${KITSU_URL}?page[limit]=20&page[offset]=${offset}&sort=popularityRank&include=genres`
        )
      )
    );
    return pages.flatMap(normalizeKitsu);
  };

  const getFromJikan = async () => normalizeJikan(await fetchJSON(JIKAN_URL));

  const getAnime = async () => {
    try {
      return { items: await getFromKitsu(), source: "Kitsu API" };
    } catch (kitsuError) {
      try {
        return { items: await getFromJikan(), source: "Jikan API" };
      } catch (jikanError) {
        throw new Error(
          `Kitsu (${kitsuError.message}) dan Jikan (${jikanError.message}) tidak merespons`
        );
      }
    }
  };

  // --- 3c. Tampilan: search, filter, render ---
  const searchInput = document.querySelector("#search-input");
  const auraContainer = document.querySelector("#aura-buttons");
  const auraHint = document.querySelector("#aura-hint");
  const sourceInfo = document.querySelector("#source-info");
  const resultCount = document.querySelector("#result-count");
  const loadingMessage = document.querySelector("#loading-message");
  const errorBox = document.querySelector("#error-box");
  const errorMessage = document.querySelector("#error-message");
  const retryBtn = document.querySelector("#retry-btn");

  let mediaData = [];
  let activeAura = "all";
  let keyword = "";

  // Cegah XSS: ubah teks jadi aman sebelum masuk innerHTML
  const escapeHTML = (text) => {
    const div = document.createElement("div");
    div.textContent = text ?? "";
    return div.innerHTML.replaceAll('"', "&quot;");
  };

  const createCard = (item, index) => {
    const title = escapeHTML(item.title);
    const image = escapeHTML(item.image);
    const genres = item.genres
      .slice(0, 3)
      .map(
        (name) =>
          `<span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-0.5 text-xs text-mist/80">${escapeHTML(name)}</span>`
      )
      .join("");

    return `
      <article class="media-card group flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-plum-light/70" style="animation-delay:${Math.min(index, 12) * 40}ms">
        <div class="relative aspect-[2/3] overflow-hidden bg-plum-soft">
          <img src="${image}" alt="Poster ${title}" loading="lazy" class="h-full w-full object-cover" onerror="this.style.display='none'" />
          <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-plum-light to-transparent"></div>
          <span class="absolute left-3 top-3 rounded-full bg-plum/80 px-2.5 py-1 text-xs font-semibold text-amber backdrop-blur">&#9733; ${item.score ?? "-"}</span>
          <span class="absolute right-3 top-3 rounded-full bg-plum/80 px-2.5 py-1 text-xs backdrop-blur">${item.episodes ?? "?"} eps</span>
        </div>
        <div class="flex flex-1 flex-col gap-3 p-4">
          <h2 class="line-clamp-2 font-display text-base font-bold leading-snug">${title}</h2>
          <div class="mt-auto flex flex-wrap gap-1.5">${genres}</div>
        </div>
      </article>
    `;
  };

  // Kerangka card (skeleton) saat data sedang dimuat
  const renderSkeleton = () => {
    grid.innerHTML = Array.from(
      { length: 8 },
      () => `
      <div class="animate-shimmer overflow-hidden rounded-2xl border border-white/10 bg-plum-light/60">
        <div class="aspect-[2/3] bg-plum-soft"></div>
        <div class="space-y-3 p-4">
          <div class="h-4 w-4/5 rounded bg-plum-soft"></div>
          <div class="h-3 w-1/2 rounded bg-plum-soft"></div>
        </div>
      </div>`
    ).join("");
  };

  const renderMedia = () => {
    const filtered = mediaData
      .filter((item) => item.title.toLowerCase().includes(keyword))
      .filter(auras[activeAura].match);

    resultCount.textContent = `${filtered.length} dari ${mediaData.length} judul`;
    grid.innerHTML =
      filtered.length === 0
        ? `<div class="glass col-span-full rounded-2xl p-10 text-center">
             <p class="font-display text-xl font-bold">Belum ada yang cocok</p>
             <p class="mt-2 text-mist/70">Ubah kata kunci atau pilih aura lain.</p>
           </div>`
        : filtered.map(createCard).join("");
  };

  const renderAuraButtons = () => {
    auraContainer.innerHTML = Object.entries(auras)
      .map(([key, aura]) => {
        const isActive = key === activeAura;
        const style = isActive
          ? aura.active
          : "border-white/15 bg-white/5 text-mist/85 hover:border-white/40";
        return `<button type="button" data-aura="${key}" aria-pressed="${isActive}" class="rounded-full border px-5 py-2.5 text-sm font-medium transition ${style}"><span aria-hidden="true" class="mr-1.5">${aura.icon}</span>${aura.label}</button>`;
      })
      .join("");
    auraHint.textContent = auras[activeAura].hint;
    document.documentElement.style.setProperty("--aura", auras[activeAura].glow);
  };

  const getMedia = async () => {
    try {
      loadingMessage.textContent = "Memuat data...";
      errorBox.classList.add("hidden");
      resultCount.textContent = "";
      renderSkeleton();

      const result = await getAnime();
      mediaData = result.items;
      sourceInfo.textContent = `Sumber data: ${result.source}`;
      renderMedia();
    } catch (error) {
      grid.innerHTML = "";
      errorMessage.textContent = `Data gagal dimuat. ${error.message}. Periksa koneksi internet lalu coba lagi.`;
      errorBox.classList.remove("hidden");
    } finally {
      loadingMessage.textContent = "";
    }
  };

  // Event 1: input (search real-time)
  searchInput.addEventListener("input", () => {
    keyword = searchInput.value.trim().toLowerCase();
    renderMedia();
  });

  // Event 2: click (pilih aura, event delegation)
  auraContainer.addEventListener("click", (event) => {
    const btn = event.target.closest("button[data-aura]");
    if (!btn) return;
    activeAura = btn.dataset.aura;
    renderAuraButtons();
    renderMedia();
  });

  // Event 3: click (coba lagi)
  retryBtn.addEventListener("click", getMedia);

  renderAuraButtons();
  getMedia();
}
