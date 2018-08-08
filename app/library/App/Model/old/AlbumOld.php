<?php

namespace App\Model;

use App\Mvc\DateTrackingModel;

class AlbumOld extends DateTrackingModel
{
    public $id;
    public $title;

    public function getSource()
    {
        return 'album';
    }

    public function columnMap()
    {
        return parent::columnMap() + [
            'id' => 'id',
            'title' => 'title'
        ];
    }

    public function initialize()
    {

        $this->hasMany('id', Photo::class, 'albumId', [
            'alias' => 'Photos',
        ]);
    }
}
