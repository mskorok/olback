<?php

namespace App\Fractal;

use League\Fractal\Serializer\ArraySerializer;

class CustomSerializer extends ArraySerializer
{
    /**
     * @param string $resourceKey
     * @param array $data
     * @return array
     */
    public function collection($resourceKey, array $data): array
    {
        if ($resourceKey === null) {
            return $data;
        }

        return [$resourceKey ?: 'data' => $data];
    }

    /**
     * @param string $resourceKey
     * @param array $data
     * @return array
     */
    public function item($resourceKey, array $data): array
    {
        if ($resourceKey === null) {
            return $data;
        }

        return [$resourceKey ?: 'data' => $data];
    }
}
