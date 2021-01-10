<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\Documents\TableGateway\BannerTableGateway;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Documents\TableGateway\DocumentsTableGateway;
use Chopin\Documents\TableGateway\BannerHasDocumentsTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;

class BannerHasDocumentsAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }
    
    private function fromBannerId(ServerRequestInterface $request, $banner_id): ResponseInterface
    {
        $tableGateway = new BannerTableGateway($this->adapter);
        $row = $tableGateway->select(['id' => $banner_id])->current();
        $documentsTableGateway = new DocumentsTableGateway($this->adapter);
        $options = $documentsTableGateway->getOptions('id', 'name', [], [
            [
                'equalTo',
                'AND',
                [
                    'type',
                    1
                ]
            ],
            [
                'equalTo',
                'AND',
                [
                    'language_id',
                    $row->language_id
                ]
            ],
            [
                'equalTo',
                'AND',
                [
                    'locale_id',
                    $row->locale_id
                ]
            ],
        ]);
        $useOptionsSelect = $documentsTableGateway->getSql()->select();
        $bannerHasDocumentsTableGateway = new BannerHasDocumentsTableGateway($this->adapter);
        $useOptionsWhere = $useOptionsSelect->where;
        $useOptionsWhere->equalTo("{$bannerHasDocumentsTableGateway->table}.banner_id", $row->id);
        $useOptionsSelect->join("{$bannerHasDocumentsTableGateway->table}", "{$documentsTableGateway->table}.id = {$bannerHasDocumentsTableGateway->table}.documents_id", [])->where($useOptionsWhere);
        $valuesResultset = $documentsTableGateway->selectWith($useOptionsSelect);
        $values = $documentsTableGateway->buildOptionsData("id", "name", $valuesResultset);
        return new ApiSuccessResponse(0, [
            'options' => [
                'documents' => $options
            ],
            'values' => [
                'documents' => $values,
            ],
            'defaultValues' => [
                'documents' => $values,
            ]
        ]);
    }
    private function fromDocumentsId(ServerRequestInterface $request, $documents_id): ResponseInterface
    {
        $tableGateway = new DocumentsTableGateway($this->adapter);
        $row = $tableGateway->select(['id' => $documents_id])->current();
        $bannerTableGateway = new BannerTableGateway($this->adapter);
        $options = $bannerTableGateway->getOptions('id', 'title', [], [
            [
                'equalTo',
                'AND',
                [
                    'type',
                    'carousel'
                ]
            ],
            [
                'equalTo',
                'AND',
                [
                    'language_id',
                    $row->language_id
                ]
            ],
            [
                'equalTo',
                'AND',
                [
                    'locale_id',
                    $row->locale_id
                ]
            ],
        ]);
        $useOptionsSelect = $bannerTableGateway->getSql()->select();
        $bannerHasDocumentsTableGateway = new BannerHasDocumentsTableGateway($this->adapter);
        $useOptionsWhere = $useOptionsSelect->where;
        $useOptionsWhere->equalTo("{$bannerHasDocumentsTableGateway->table}.documents_id", $row->id);
        $useOptionsSelect->join("{$bannerHasDocumentsTableGateway->table}", "{$bannerTableGateway->table}.id = {$bannerHasDocumentsTableGateway->table}.banner_id", [])->where($useOptionsWhere);
        $valuesResultset = $bannerTableGateway->selectWith($useOptionsSelect);
        $values = $bannerTableGateway->buildOptionsData("id", "title", $valuesResultset);
        return new ApiSuccessResponse(0, [
            'options' => [
                'banner' => $options
            ],
            'values' => [
                'banner' => $values,
            ]
        ]);
    }
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        if(isset($query['banner_id'])) {
            return $this->fromBannerId($request, $query['banner_id']);
        }
        if(isset($query['documents_id'])) {
            return $this->fromDocumentsId($request, $query['documents_id']);
        }
        
    }
    
    private function bannerPost($post)
    {
        $banner_id = intval($post['banner_id']);
        if (isset($post['documents_id'])) {
            $documents_ids = explode(',', $post['documents_id']);
        }
        $bannerHasDocumentsTableGateway = new BannerHasDocumentsTableGateway($this->adapter);
        $connection = $bannerHasDocumentsTableGateway->adapter->getDriver()->getConnection();
        $connection->beginTransaction();
        try {
            $bannerHasDocumentsTableGateway->delete([
                'banner_id' => $banner_id
            ]);
            if (isset($documents_ids)) {
                foreach ($documents_ids as $documents_id) {
                    $documents_id = intval($documents_id);
                    $set = [
                        'banner_id' => $banner_id,
                        'documents_id' => $documents_id,
                    ];
                    $bannerHasDocumentsTableGateway->insert($set);
                }
            }
            $connection->commit();
            return new ApiSuccessResponse(0, [], ['update success']);
        } catch (\Exception $e) {
            $connection->rollback();
            return new ApiErrorResponse(0, [], [$e->getMessage()]);
        }
        
    }

    private function documentsPost($post)
    {
        $documents_id = intval($post['documents_id']);
        if (isset($post['banner_id'])) {
            $banner_ids = explode(',', $post['banner_id']);
        }
        $bannerHasDocumentsTableGateway = new BannerHasDocumentsTableGateway($this->adapter);
        $connection = $bannerHasDocumentsTableGateway->adapter->getDriver()->getConnection();
        $connection->beginTransaction();
        try {
            $bannerHasDocumentsTableGateway->delete([
                'documents_id' => $documents_id
            ]);
            if (isset($banner_ids)) {
                foreach ($banner_ids as $banner_id) {
                    $banner_id = intval($banner_id);
                    $set = [
                        'banner_id' => $banner_id,
                        'documents_id' => $documents_id,
                    ];
                    $bannerHasDocumentsTableGateway->insert($set);
                }
            }
            $connection->commit();
            return new ApiSuccessResponse(0, [], ['update success']);
        } catch (\Exception $e) {
            $connection->rollback();
            return new ApiErrorResponse(0, [], [$e->getMessage()]);
        }
        
    }
    
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $post = $request->getParsedBody();
        $referer = $request->getHeader('Referer')[0];
        if(preg_match('/\/documents\/\d+/', $referer)) {
            return $this->documentsPost($post);
        }
        if(preg_match('/\/banner\/\d+/', $referer)) {
            return $this->bannerPost($post);
        }
    }
}
