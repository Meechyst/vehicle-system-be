<?php

namespace App\Http\Controllers;

use App\Transformers\BrandTransformer;
use App\Type;
use Illuminate\Http\Request;
use App\Brand;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{

    /**
     * Get all the brands of a given type.
     * Route: /brands-of-type - GET
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allBrandsOfAType(Request $request)
    {
        //User chose from the list
        if ($request->get('id') != null) {
            //get the type with given id
            $type = Type::where('id', '=', $request->get('id'))->first();
            //if there's any
            if ($type !== null) {
                //crossjoin brands table through vmodels table to fetch
                // only the names and ids that are associated with given type
                $brands = DB::table('vmodels')
                    ->crossJoin('brands')
                    ->where('type_id', '=', $type->id)->distinct()
                    ->get()->pluck('name', 'id')->groupBy('name', 'id')->toArray();
                //we only want unique data to populate our list properly
                $brands = array_unique($brands[""]);

                return response()->json($brands);
              //no record with given id
            } else {
                return response()->json(['message' => 'No brands available for this vehicle type']);
            }

        //No id = user decided to type a new brand instead of choosing from the list
        //same thing here, using name field instead of id
        } else {
            $type = Type::where('name', '=', $request->get('name'))->first();
            if ($type !== null) {
                $brands = DB::table('vmodels')
                    ->crossJoin('brands')
                    ->where('type_id', '=', $type->id)->distinct()
                    ->get()->pluck('name', 'id')->groupBy('name', 'id')->toArray();
                $brands = array_unique($brands[""]);

                return response()->json($brands);
            } else {
                return response()->json(['message' => 'No brands available for this vehicle type']);
            }
        }

    }
}
