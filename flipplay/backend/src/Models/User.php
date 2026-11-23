<?php
declare(strict_types=1);

namespace FlipPlay\Models;

class User
{
    private int $id;
    private string $name;

    /** @var Media[] */
    private array $collection = [];

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addToCollection(Media $media): bool
    {
        foreach ($this->collection as $item) {
            if ($item->getId() === $media->getId()) {
                return false;
            }
        }

        $this->collection[] = $media;
        return true;
    }

    /** @return Media[] */
    public function getCollection(): array
    {
        return $this->collection;
    }
}
