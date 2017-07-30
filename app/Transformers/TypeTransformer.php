<?php


namespace App\Transformers;


use App\Type;
use League\Fractal\TransformerAbstract;

class TypeTransformer extends TransformerAbstract
{
    /**
     * @param Type $type
     * @return array
     * @internal param Brand $brand
     */
    public function transform(Type $type)
    {
        return $type->toArray();
    }
}