<?php

namespace App\Transformers;

use App\Model\ProcessOrganizations;
use PhalconRest\Transformers\ModelTransformer;
use PhalconRest\Transformers\Transformer;

class ProcessOrganizationsTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = ProcessOrganizations::class;
        $this->availableIncludes = [
            'Organization', 'Process'
        ];
    }

    public function includeOrganization(ProcessOrganizations $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer);
    }

    public function includeProcess(ProcessOrganizations $model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer);
    }
}
