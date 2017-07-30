<?php


namespace App\Transformers;


use App\Vmodel;
use League\Fractal\TransformerAbstract;

class VmodelTransformer extends TransformerAbstract
{
    /**
     * @param Vmodel $vmodel
     * @return array
     * @internal param Type $type
     * @internal param Brand $brand
     */
    public function transform(Vmodel $vmodel)
    {
        return $vmodel->toArray();
    }
}