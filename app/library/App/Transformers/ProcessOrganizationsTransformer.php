<?php

namespace App\Transformers;

use App\Model\ProcessOrganizations;
use PhalconRest\Transformers\Transformer;

class ProcessOrganizationsTransformer extends Transformer
{
    protected $modelClass = ProcessOrganizations::class;

    protected $availableIncludes = [
        'Organization', 'Process'
    ];

    public function includeOrganization($model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer);
    }

    public function includeProcess($model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer);
    }
}
