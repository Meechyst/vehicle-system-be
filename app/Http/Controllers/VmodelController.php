<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Transformers\VmodelTransformer;
use Illuminate\Http\Request;

class VmodelController extends Controller
{
    /**
     * Get all the models of a given brand
     * Route: /models-of-brand - GET
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allModelsOfABrand(Request $request){
        //proceed if user picked from list
        //(front-end sends id if data is coming from select-box, name if it's text-box)
        if ($request->get('id') != null) {
            //check if there's any brand with given id
            $brand = Brand::where('id', '=', $request->get('id'))->first();
            if ($brand !== null) {
                //if so get all the model ids and names that are associated with this brand
                $models = $brand->vmodels()->get()->pluck('name', 'id')->toArray();
                //we only want unique data
                return response()->json(array_unique($models));
            } else {
                return response()->json(['message' => 'No models available for this brand']);
            }
          //same thing with name this time
        } else {
            $brand = Brand::where('name', '=', $request->get('name'))->first();
            if ($brand !== null) {
                $models = $brand->vmodels()->get()->pluck('name', 'id')->toArray();

                return response()->json(array_unique($models));
            } else {
                return response()->json(['message' => 'No models available for this brand ']);
            }
        }


    }

}
