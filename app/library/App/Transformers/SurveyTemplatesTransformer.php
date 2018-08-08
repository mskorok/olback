<?php

namespace App\Transformers;

use App\Model\SurveyTemplate;
use PhalconRest\Transformers\Transformer;

class SurveyTemplatesTransformer extends Transformer
{
    protected $modelClass = SurveyTemplate::class;

    protected $availableIncludes = [
        'Organization', 'User'
    ];

    public function includeOrganization($model)
    {
        return $this->item($model->getOrganization(), new OrganizationTransformer());
    }

    public function includeUser($model)
    {
        return $this->item($model->getUser(), new UserTransformer());
    }
}
