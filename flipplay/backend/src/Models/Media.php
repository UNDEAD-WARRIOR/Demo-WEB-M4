<?php
declare(strict_types=1);

namespace FlipPlay\Models;

/**
 * Class induk untuk item yang ditampilkan di katalog FlipPlay.
 */
abstract class Media
{
    private int $id;
    protected string $title;
    protected int $releaseYear;
    /** @var string[] */
    protected array $genres;

    /** @param string[] $genres */
    public function __construct(int $id, string $title, int $releaseYear, array $genres)
    {
        $this->id = $id;
        $this->title = $title;
        $this->releaseYear = $releaseYear;
        $this->genres = $genres;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /** @return string[] */
    public function getGenres(): array
    {
        return $this->genres;
    }

    abstract public function getMediaType(): string;

    public function getDetails(): string
    {
        return "ID: {$this->id}\n"
            . "Judul: {$this->title}\n"
            . "Jenis: " . $this->getMediaType() . "\n"
            . "Tahun rilis: {$this->releaseYear}\n"
            . "Genre: " . implode(", ", $this->genres);
    }
}
