<?php
declare(strict_types=1);

// Entry point simulasi backend FlipPlay.
// Class dimuat eksplisit agar mudah dipahami pada praktikum dasar.
require_once __DIR__ . "/src/Models/Media.php";
require_once __DIR__ . "/src/Models/Anime.php";
require_once __DIR__ . "/src/Models/User.php";
require_once __DIR__ . "/src/Services/MediaLibrary.php";

use FlipPlay\Models\Anime;
use FlipPlay\Models\User;
use FlipPlay\Services\MediaLibrary;

function printSection(string $title): void
{
    echo PHP_EOL . "=== {$title} ===" . PHP_EOL;
}

$library = new MediaLibrary();
$user = new User(1, "Farrel");

// Data contoh lokal untuk simulasi terminal.
// Pada Modul 2, katalog halaman Vibe Grid mengambil data dari Kitsu/Jikan API.
$sampleAnime = [
    new Anime(1, "Attack on Titan", 2013, ["Action", "Drama"], 25, 9.1),
    new Anime(2, "Haikyuu!!", 2014, ["Sports", "Drama"], 25, 8.7),
    new Anime(3, "Your Lie in April", 2014, ["Drama", "Romance"], 22, 8.6),
    new Anime(4, "One Piece", 1999, ["Action", "Adventure"], 1100, 8.7),
    new Anime(5, "Violet Evergarden", 2018, ["Drama", "Fantasy"], 13, 8.6),
];

echo "FLIPPLAY - BACKEND ENGINE (PHP OOP)" . PHP_EOL;
echo "Pengembangan konsep Vibe Grid dari Modul 2" . PHP_EOL;
echo str_repeat("-", 58) . PHP_EOL;

// 1. Menambahkan media ke library.
printSection("1. MENAMBAHKAN MEDIA KE LIBRARY");
foreach ($sampleAnime as $anime) {
    $added = $library->addMedia($anime);
    echo ($added ? "[BERHASIL] " : "[DUPLIKAT] ") . $anime->getTitle() . PHP_EOL;
}

// 2. Menampilkan daftar media.
printSection("2. DAFTAR MEDIA");
foreach ($library->getAllMedia() as $media) {
    echo "- {$media->getTitle()} | {$media->getMediaType()}" . PHP_EOL;
}

// 3. Mencari media berdasarkan judul.
printSection("3. PENCARIAN JUDUL");
$keyword = "Violet";
$searchResults = $library->searchByTitle($keyword);
echo "Kata kunci: {$keyword}" . PHP_EOL;
if ($searchResults === []) {
    echo "Media tidak ditemukan." . PHP_EOL;
} else {
    foreach ($searchResults as $media) {
        echo "- {$media->getTitle()}" . PHP_EOL;
    }
}

// 4. Menampilkan detail media.
printSection("4. DETAIL MEDIA");
$detail = $library->findById(1);
if ($detail !== null) {
    echo $detail->getDetails() . PHP_EOL;
}

// 5. Filter Vibe Grid sesuai aturan Modul 2.
printSection("5. FILTER VIBE GRID");
$aura = "energy";
echo "Aura aktif: High Energy" . PHP_EOL;
foreach ($library->filterAnimeByAura($aura) as $anime) {
    echo "- {$anime->getTitle()}" . PHP_EOL;
}

// 6. Koleksi media pengguna.
printSection("6. KOLEKSI PENGGUNA");
$favorite = $library->findById(1);
if ($favorite !== null) {
    $user->addToCollection($favorite);
}
$favorite = $library->findById(3);
if ($favorite !== null) {
    $user->addToCollection($favorite);
}

echo "Koleksi {$user->getName()}:" . PHP_EOL;
foreach ($user->getCollection() as $media) {
    echo "- {$media->getTitle()}" . PHP_EOL;
}

echo PHP_EOL . "Simulasi backend FlipPlay selesai." . PHP_EOL;
