<?php


namespace App\Http\Api;


use Closure;
use Illuminate\Support\Collection;

trait Helpers
{
    use \Dingo\Api\Routing\Helpers;

    /**
     * Bind an item to a transformer and start building a response.
     *
     * @param object $item
     * @param object $transformer
     * @param array $parameters
     * @param \Closure $after
     *
     * @return \Dingo\Api\Http\Response
     */
    public function item($item, $transformer, $parameters = [], Closure $after = null)
    {
        $response = $this->response->item($item, $transformer, $parameters, $after);
        if (\Session::has('sql')) {
            $response->addMeta('sql', \Session::get('sql'));
        }
        return $response;
    }

    /**
     * Bind a collection to a transformer and start building a response.
     *
     * @param \Illuminate\Support\Collection $collection
     * @param object $transformer
     * @param array|\Closure $parameters
     * @param \Closure|null $after
     *
     * @return \Dingo\Api\Http\Response
     */
    public function collection(Collection $collection, $transformer, $parameters = [], Closure $after = null)
    {
        $response = $this->response->collection($collection, $transformer, $parameters, $after);

        if (\Session::has('sql')) {
            $response->addMeta('sql', \Session::get('sql'));
        }
        return $response;
    }

}