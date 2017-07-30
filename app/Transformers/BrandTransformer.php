<?php


namespace App\Transformers;


use App\Brand;
use League\Fractal\TransformerAbstract;

class BrandTransformer extends TransformerAbstract
{
    /**
     * @param Brand $brand
     * @return array
     */
    public function transform(Brand $brand)
    {
        return $brand->toArray();
    }
}