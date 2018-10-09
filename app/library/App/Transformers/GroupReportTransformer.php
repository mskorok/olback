<?php

namespace App\Transformers;

use App\Model\GroupReport;
use PhalconRest\Transformers\ModelTransformer;

class GroupReportTransformer extends ModelTransformer
{
    public function __construct()
    {
        $this->modelClass = GroupReport::class;
        $this->availableIncludes = [
            'Process', 'Organization'
        ];
    }

    public function includeProcess(GroupReport $model)
    {
        return $this->item($model->getProcess(), new ProcessTransformer());
    }

    public function includeOrganization(GroupReport $model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }
}
