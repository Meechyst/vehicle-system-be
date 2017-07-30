<?php

namespace App\Http\Controllers;

use App\Transformers\TypeTransformer;
use App\Type;

class TypeController extends Controller
{

    /**
     * Get all the records of types table.
     * Route: /types - GET

     * @return \Dingo\Api\Http\Response
     */
    public function allTypes()
    {
        $brands = Type::all();
        return $this->response->item($brands, new TypeTransformer());
    }



}
