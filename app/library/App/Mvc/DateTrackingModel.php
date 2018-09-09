<?php

namespace App\Mvc;

use Phalcon\Mvc\Model;

class DateTrackingModel extends Model
{
    public $createdAt;
    public $updatedAt;

    public function columnMap(): array
    {
        return [
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
        ];
    }

    public function beforeCreate(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = date('Y-m-d H:i:s');
        }

        $this->updatedAt = $this->createdAt;
    }

    public function beforeUpdate(): void
    {
        $this->updatedAt = date('Y-m-d H:i:s');
    }
}
