<?php

namespace App\Transformers;

use App\Model\Album;
use PhalconRest\Transformers\ModelTransformer;

class AlbumTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = Album::class;
        $this->availableIncludes = [
            'photos'
        ];
    }

    public function includePhotos(Album $album)
    {
        return $this->collection($album->getPhotos(), new PhotoTransformer);
    }

    public function transform($album)
    {
        /** @var Album $album */
        return [
            'id' => $this->int($album->id),
            'title' => $album->title,
            'updated_at' => $album->updatedAt,
            'created_at' => $album->createdAt
        ];
    }
}
