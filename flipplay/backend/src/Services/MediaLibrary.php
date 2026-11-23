<?php
declare(strict_types=1);

namespace FlipPlay\Services;

use FlipPlay\Models\Anime;
use FlipPlay\Models\Media;

class MediaLibrary
{
    /** @var Media[] */
    private array $items = [];

    public function addMedia(Media $media): bool
    {
        if ($this->findById($media->getId()) !== null) {
            return false;
        }

        $this->items[] = $media;
        return true;
    }

    /** @return Media[] */
    public function getAllMedia(): array
    {
        return $this->items;
    }

    public function findById(int $id): ?Media
    {
        foreach ($this->items as $item) {
            if ($item->getId() === $id) {
                return $item;
            }
        }

        return null;
    }

    public function searchByTitle(string $keyword): array
    {
        $results = [];

        foreach ($this->items as $item) {
            if (stripos($item->getTitle(), $keyword) !== false) {
                $results[] = $item;
            }
        }

        return $results;
    }

    /** @return Anime[] */
    public function filterAnimeByAura(string $aura): array
    {
        $results = [];

        foreach ($this->items as $item) {
            if ($item instanceof Anime && $item->matchesAura($aura)) {
                $results[] = $item;
            }
        }

        return $results;
    }
}
