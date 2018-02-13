<?php

namespace App\Resources;

use PhalconRest\Api\ApiEndpoint;
use PhalconRest\Api\ApiResource;
use App\Model\Process;
use App\Transformers\ProcessTransformer;
use App\Constants\AclRoles;
use PhalconRest\Mvc\Controllers\CrudResourceController;

class ProcessResource extends ApiResource {

    public function initialize()
    {
        $this
            ->name('Process')
            ->model(Process::class)
            ->expectsJsonData()
            ->transformer(ProcessTransformer::class)
            ->itemKey('process')
            ->collectionKey('process')
//            ->deny(AclRoles::UNAUTHORIZED)
            ->handler(CrudResourceController::class)

            ->endpoint(ApiEndpoint::all())
            ->endpoint(ApiEndpoint::create())
            ->endpoint(ApiEndpoint::find())
            ->endpoint(ApiEndpoint::update())
            ->endpoint(ApiEndpoint::remove());
    }
}
