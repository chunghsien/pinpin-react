<?php
declare(strict_types = 1);

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Db\Adapter\Adapter;
use Chopin\Documents\TableGateway\LayoutZonesTableGateway;
use Chopin\LaminasDb\RowGateway\RowGateway;

class SiteNavigatotMiddleware implements MiddlewareInterface
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    private function getNavigator($request, $type)
    {
        $tableGateway = new LayoutZonesTableGateway($this->adapter);
        $language_id = $request->getAttribute('language_id');
        $locale_id = $request->getAttribute('locale_id');
        $parentRow = $tableGateway->select([
            "type" => $type,
            "language_id" => $language_id,
            "locale_id" => $locale_id,
        ])->current();
        $navigator = [];
        $select = $tableGateway->getSql()->select();
        $select->order([
            'sort ASC',
            'id ASC'
        ]);
        $where = $select->where;
        $where->isNull('deleted_at');
        $where->equalTo('visible', 1);
        $where->equalTo('parent_id', $parentRow->id);
        $select->where($where);
        $select->columns(['id', 'type', 'name', 'uri', 'image']);
        $headerResultSet = $tableGateway->selectWith($select);
        foreach ($headerResultSet as $row) {
            /**
             *
             * @var RowGateway $row
             */
            if ($row->type == 'not_use') {
                $ChildSelect = $tableGateway->getSql()->select();
                $ChildSelect->order([
                    'sort ASC',
                    'id ASC'
                ]);
                $ChildWhere = $ChildSelect->where;
                $ChildWhere->isNull('deleted_at');
                $ChildWhere->equalTo('visible', 1);
                $ChildWhere->equalTo('parent_id', $row->id);
                $ChildSelect->where($ChildWhere);
                $ChildSelect->columns(['id', 'type', 'name', 'uri', 'image']);
                $childResultSet = $tableGateway->selectWith($ChildSelect);
                $childs = [];
                foreach ($childResultSet as $childRow)
                {
                    if($childRow->type == 'not_use')
                    {
                        $grandSelect = $tableGateway->getSql()->select();
                        $grandSelect->order([
                            'sort ASC',
                            'id ASC'
                        ]);
                        $grandWhere = $grandSelect->where;
                        $grandWhere->isNull('deleted_at');
                        $grandWhere->equalTo('visible', 1);
                        $grandWhere->equalTo('parent_id', $childRow->id);
                        $grandSelect->where($grandWhere);
                        $grandSelect->columns(['id', 'type', 'name', 'uri', 'image']);
                        $grandResultSet = $tableGateway->selectWith($grandSelect);
                        $childRow->with('children', $grandResultSet);
                    }
                    $childs[] = $childRow;
                }
                $row->with('children', $childs);
            }
            $navigator[] = $row;
        }

        return $navigator;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $request->withAttribute('site_header', $this->getNavigator($request, "header_nav"));
        $request = $request->withAttribute('site_footer', $this->getNavigator($request, "footer_nav"));
        return $handler->handle($request);
    }
}