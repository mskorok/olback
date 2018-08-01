<?php
/**
 * Created by PhpStorm.
 * User: thomaschatzidimitris
 * Date: 13/02/2018
 * Time: 23:59
 */

namespace App\Transformers;

use App\Model\Process;
use PhalconRest\Transformers\ModelTransformer;

class ProcessTransformer extends ModelTransformer
{
    protected $modelClass = Process::class;
}