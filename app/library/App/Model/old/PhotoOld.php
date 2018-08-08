<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class PhotoOld extends DateTrackingModel
{
    public $id;
    public $title;
    public $albumId;

    public function getSource()
    {
        return 'photo';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'title' => 'title',
            'album_id' => 'albumId'
        ];
    }

    public function initialize()
    {

        $this->belongsTo('albumId', Album::class, 'id', [
            'alias' => 'Album',
        ]);
    }
}
