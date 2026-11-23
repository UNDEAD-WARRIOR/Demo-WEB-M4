<?php
declare(strict_types=1);

namespace FlipPlay\Models;

class Anime extends Media
{
    private ?int $episodes;
    private ?float $score;

    /** @param string[] $genres */
    public function __construct(
        int $id,
        string $title,
        int $releaseYear,
        array $genres,
        ?int $episodes,
        ?float $score
    ) {
        parent::__construct($id, $title, $releaseYear, $genres);
        $this->episodes = $episodes;
        $this->score = $score;
    }

    public function getMediaType(): string
    {
        return "Anime";
    }

    public function getEpisodes(): ?int
    {
        return $this->episodes;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * Logika aura mengikuti aturan Vibe Grid pada Modul 2.
     */
    public function matchesAura(string $aura): bool
    {
        $aura = strtolower($aura);

        if ($aura === "all") {
            return true;
        }

        if ($aura === "energy") {
            return count(array_intersect(
                $this->genres,
                ["Action", "Adventure", "Sports", "Sci-Fi"]
            )) > 0;
        }

        if ($aura === "melancholy") {
            return count(array_intersect(
                $this->genres,
                ["Drama", "Romance", "Tragedy", "Psychological", "Mystery", "Suspense"]
            )) > 0;
        }

        if ($aura === "binge") {
            return ($this->episodes ?? 0) >= 24;
        }

        return false;
    }

    public function getDetails(): string
    {
        return parent::getDetails() . "\n"
            . "Episode: " . ($this->episodes ?? "Tidak diketahui") . "\n"
            . "Skor: " . ($this->score !== null ? number_format($this->score, 1) : "-");
    }
}
