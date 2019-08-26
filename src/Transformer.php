<?php
/**
 * Created by PhpStorm.
 * User: zhangzhenwei
 * Date: 2019/8/26
 * Time: 20:07
 */

namespace Ritin\LaravelTransform;



use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class Transformer extends Manager
{

    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    /**
     * @var Manager;
     */
    protected $manager;

    /**
     * @var Collection
     */
    protected $resource;

    private $resourceKeyItem;

    private $resourceKeyCollection;

    /**
     * Transformer constructor.
     * @param $transform
     */
    public function __construct($transform)
    {
        $this->transformer = $transform;
    }

    /**
     * 功能:转换数据
     * transform
     * @param $data
     * @return array
     */
    public function transform($data): array
    {
        if ($data instanceof EloquentCollection) {
            $this->resource = $this->transformCollection($data);
        } elseif ($data instanceof LengthAwarePaginator) {
            $this->resource = $this->transformPaginator($data);
            $result = $this->createData($this->resource)->toArray();
            $pagination = $result['meta']['pagination'];

            return [
                'result'     => $result['data'],
                'pagination' => [
                    'pageTotal'   => $pagination['total_pages'],
                    'pageCurrent' => $pagination['current_page'],
                    'pageSize'    => $pagination['per_page'],
                ],
            ];
        } else {
            $this->resource = $this->transformItem($data);
        }

        return $this->createData($this->resource)->toArray()['data'];
    }

    /**
     * 功能:转换集合数据
     * transformCollection
     * @param $data
     * @return Collection
     */
    private function transformCollection($data)
    {
        return new Collection($data, $this->getTransformer(), $this->resourceKeyCollection);
    }

    /**
     * 功能:转换分页数据
     * transformPaginator
     * @param LengthAwarePaginator $paginator
     * @return Collection
     */
    private function transformPaginator(LengthAwarePaginator $paginator)
    {
        $collection = $paginator->getCollection();
        $resource = new Collection($collection, $this->getTransformer(), $this->resourceKeyCollection);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        return $resource;
    }

    /**
     * 功能:转换数组
     * transformItem
     * @param $data
     * @return Item
     */
    private function transformItem($data)
    {
        return new Item($data, $this->getTransformer(), $this->resourceKeyItem);
    }

    /**
     * getTransformer
     * @return mixed
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * setTransformer
     * @param TransformerAbstract $transformer
     * @return $this
     */
    public function setTransformer(TransformerAbstract $transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * getManager
     * @return Manager
     */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    /**
     * setManager
     * @return $this
     */
    public function setManager()
    {
        $this->manager = App::make('transformerManager');

        return $this;
    }

    /**
     * getResource
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }


    /**
     * setResource
     * @param $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }
}