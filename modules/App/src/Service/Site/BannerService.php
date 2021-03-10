<?php

namespace App\Service\Site;

use Laminas\Db\Adapter\Adapter;
use Chopin\Documents\TableGateway\BannerTableGateway;
use Laminas\Db\Sql\Predicate\Predicate;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Documents\TableGateway\DocumentsTableGateway;
use Chopin\Documents\TableGateway\BannerHasDocumentsTableGateway;

class BannerService extends AbstractService
{

    /**
     *
     * @var BannerTableGateway
     */
    protected $bannerTableGateway;

    /**
     *
     * @var DocumentsTableGateway
     */
    protected $documentsTableGateway;

    /**
     *
     * @var BannerHasDocumentsTableGateway
     */
    protected $bannerHasDocumentsTableGateway;

    /**
     *
     * @var ServerRequestInterface
     */
    protected $request;

    public function __construct(Adapter $adapter, ServerRequestInterface $request)
    {
        $this->bannerTableGateway = new BannerTableGateway($adapter);
        $this->documentsTableGateway = new DocumentsTableGateway($adapter);
        $this->bannerHasDocumentsTableGateway = new BannerHasDocumentsTableGateway($adapter);
        parent::__construct($adapter, $request);
    }

    public function getPageCarousel($route, $type='carousel')
    {
        $select = $this->bannerTableGateway->getSql()->select();
        $select->join(
            $this->bannerHasDocumentsTableGateway->table,
            "{$this->bannerTableGateway->table}.id={$this->bannerHasDocumentsTableGateway->table}.banner_id",
            []
        );
        $select->join(
            $this->documentsTableGateway->table,
            "{$this->bannerHasDocumentsTableGateway->table}.documents_id={$this->documentsTableGateway->table}.id",
            []
        );
        $where = $select->where;
        $where->equalTo("{$this->bannerTableGateway->table}.type", $type);
        $where->equalTo("{$this->bannerTableGateway->table}.is_show", 1);
        $where->equalTo("{$this->documentsTableGateway->table}.route", $route);
        $where->equalTo("{$this->documentsTableGateway->table}.visible", 1);
        return $this->bannerTableGateway->selectWith($select);
    }

}