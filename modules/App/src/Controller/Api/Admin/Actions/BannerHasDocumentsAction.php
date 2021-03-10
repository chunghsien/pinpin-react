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

    /**
     *
     * @var ServerRequestInterface
     */
    protected $request;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        $this->request = $request;
        return $this->{$method}($request);
    }

    private function fromBannerId(ServerRequestInterface $request, $banner_id): ResponseInterface
    {
        $tableGateway = new BannerTableGateway($this->adapter);
        $row = $tableGateway->select([
            'id' => $banner_id
        ])->current();
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
            ]
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
                'documents' => $values
            ],
        ]);
    }

    private function fromDocumentsId(ServerRequestInterface $request, $documents_id): ResponseInterface
    {
        $tableGateway = new DocumentsTableGateway($this->adapter);
        $row = $tableGateway->select([
            'id' => $documents_id
        ])->current();
        $bannerTableGateway = new BannerTableGateway($this->adapter);
        $wheres = [
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
            ]
        ];

        $queryParams = $request->getQueryParams();
        if (isset($queryParams['apiProps'])) {
            $apiProps = json_decode($queryParams['apiProps'], true);
            foreach ($apiProps as $key => $value) {
                $wheres[] = [
                    'equalTo',
                    'AND',
                    [
                        $key,
                        $value
                    ]
                ];
            }
            if ($apiProps['type'] == 's_carousel') {
                $options = $bannerTableGateway->getOptions('id', 'image', [], $wheres);
            } else {
                $options = $bannerTableGateway->getOptions('id', 'title', [], $wheres);
            }
        }else {
            $options = $bannerTableGateway->getOptions('id', 'title', [], $wheres);
        }
        $useOptionsSelect = $bannerTableGateway->getSql()->select();
        $bannerHasDocumentsTableGateway = new BannerHasDocumentsTableGateway($this->adapter);
        $useOptionsWhere = $useOptionsSelect->where;
        $useOptionsWhere->equalTo("{$bannerHasDocumentsTableGateway->table}.documents_id", $row->id);
        if (isset($queryParams['apiProps'])) {
            foreach ($apiProps as $key => $value) {
                $useOptionsWhere->equalTo($key, $value);
            }
        }
        $useOptionsSelect->join("{$bannerHasDocumentsTableGateway->table}", "{$bannerTableGateway->table}.id = {$bannerHasDocumentsTableGateway->table}.banner_id", [])->where($useOptionsWhere);
        $valuesResultset = $bannerTableGateway->selectWith($useOptionsSelect);
        // $values = $bannerTableGateway->buildOptionsData("id", "title", $valuesResultset);
        if (isset($queryParams['apiProps'])) {
            if ($apiProps['type'] == 's_carousel') {
                $values = $bannerTableGateway->buildOptionsData('id', 'image', $valuesResultset);
            } else {
                $values = $bannerTableGateway->buildOptionsData('id', 'title', $valuesResultset);
            }
        }else {
            $values = $bannerTableGateway->buildOptionsData('id', 'title', $valuesResultset);
        }

        return new ApiSuccessResponse(0, [
            'options' => [
                'banner' => $options
            ],
            'values' => [
                'banner' => $values
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
        $referer = $request->getHeader('Referer')[0];
        $referer = str_replace('s_banner', 'banner', $referer);
        if (preg_match('/\/documents\/\d+/', $referer)) {
            return $this->fromDocumentsId($request, $query['self_id']);
        }
        if (preg_match('/\/banner\/\d+/', $referer)) {
            return $this->fromBannerId($request, $query['self_id']);
        }
    }

    private function bannerPost($post)
    {
        $banner_id = intval($post['banner_id']);
        $bannerHasDocumentsTableGateway = new BannerHasDocumentsTableGateway($this->adapter);
        if (isset($post['documents_id'])) {
            $documents_ids = explode(',', $post['documents_id']);
        } /*
           * else { $bannerHasDocumentsTableGateway->delete(['banner_id' => $banner_id]); return new ApiSuccessResponse(0, [], ['update success']); }
           */
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
                        'documents_id' => $documents_id
                    ];
                    $bannerHasDocumentsTableGateway->insert($set);
                }
            }
            $connection->commit();

            $request = $this->request;
            $response = $this->fromBannerId($request, $banner_id);
            $contents = json_decode($response->getBody()->getContents(), true);
            $contents['data']['id'] = $banner_id;
            return new ApiSuccessResponse(0, $contents['data'], [
                'update success'
            ]);
        } catch (\Exception $e) {
            $connection->rollback();
            return new ApiErrorResponse(0, [], [
                $e->getMessage()
            ]);
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
                        'documents_id' => $documents_id
                    ];
                    $bannerHasDocumentsTableGateway->insert($set);
                }
            }
            $connection->commit();
            $request = $this->request;
            $response = $this->fromDocumentsId($request, $documents_id);
            $contents = json_decode($response->getBody()->getContents(), true);
            return new ApiSuccessResponse(0, $contents['data'], [
                'update success'
            ]);
        } catch (\Exception $e) {
            $connection->rollback();
            return new ApiErrorResponse(0, [], [
                $e->getMessage()
            ]);
        }
    }

    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $post = $request->getParsedBody();
        $referer = $request->getHeader('Referer')[0];
        $referer = str_replace('s_banner', 'banner', $referer);
        if(preg_match('/\/documents\/\d+/', $referer)) {
            return $this->documentsPost($post);
        }
        if(preg_match('/\/banner\/\d+/', $referer)) {
            return $this->bannerPost($post);
        }
    }
}
