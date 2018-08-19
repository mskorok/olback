<?php

namespace App\Bootstrap;

use App\BootstrapInterface;
use App\Collections\ExportCollection;
// use App\Collections\UserCollection;
use App\Resources\ProcessYearSurveyResource;
use App\Resources\UserResource;
use App\Resources\AlbumResource;
use App\Resources\PhotoResource;
use App\Resources\ProcessResource;
use App\Resources\OrganizationResource;
use App\Resources\SystemicMapResource;
use App\Resources\GroupResource;
use App\Resources\DepartmentResource;
use App\Resources\SurveyResource;
use App\Resources\StatisticsResource;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;

class CollectionBootstrap implements BootstrapInterface
{
    /**
     * @param Api $api
     * @param DiInterface $di
     * @param Config $config
     * @throws \PhalconApi\Exception
     */
    public function run(Api $api, DiInterface $di, Config $config)
    {
        $api
            ->collection(new ExportCollection('/export'))
            // ->collection(new UserCollection('/user'))
            ->resource(new UserResource('/users'))
            ->resource(new AlbumResource('/albums'))
            ->resource(new PhotoResource('/photos'))
            ->resource(new ProcessResource('/process'))
            ->resource(new ProcessYearSurveyResource('/year-survey'))
            ->resource(new OrganizationResource('/organization'))
            ->resource(new SystemicMapResource('/systemicmap'))
            ->resource(new GroupResource('/group'))
            ->resource(new DepartmentResource('/department'))
            ->resource(new SurveyResource('/survey'))
            ->resource(new StatisticsResource('/statistics'));
    }
}
