<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\LaminasDb\DB;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Newsletter\TableGateway\NnClassHasNewsTableGateway;
use Chopin\Newsletter\TableGateway\NewsTableGateway;

class NnClassHasNewsAction extends AbstractAction
{

    private function getOptions(ServerRequestInterface $request)
    {
        $params = array_merge($request->getQueryParams(), $request->getParsedBody());
        $news_id = $params['news_id'];
        $newsTableGateway = new NewsTableGateway($this->adapter);
        $newsRow = $newsTableGateway->select([
            'id' => $news_id
        ])->current();
        $nnClassHasNewsScripts = require 'modules/App/scripts/db/admin/nnClassHasNews.php';
        $options = DB::selectFactory($nnClassHasNewsScripts['options'], [
            'language_id' => $newsRow->language_id,
            'locale_id' => $newsRow->locale_id
        ])->toArray();

        $values = DB::selectFactory($nnClassHasNewsScripts['defaultValue'], [
            'news_id' => $news_id,
        ])->toArray();
        
        return [
            'values' => [
                'nn_class' => $values,
            ],
            'options' => [
                'nn_class' => $options,
            ]
        ];
    }
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->getOptions($request);
        return new ApiSuccessResponse(0, $data);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->adapter->getDriver()->getConnection()->beginTransaction();
            $post = $request->getParsedBody();
            $news_id = $post['news_id'];
            $nnClassHasNewsTableGateway = new NnClassHasNewsTableGateway($this->adapter);
            if($nnClassHasNewsTableGateway->select(['news_id' => $news_id])->count()) {
                $nnClassHasNewsTableGateway->delete(['news_id' => $news_id]);
            }
            $nn_class_ids = explode(',', $post['nn_class_id']);
            foreach ($nn_class_ids as $nn_class_id) {
                $set = [
                    'nn_class_id' => $nn_class_id,
                    'news_id' => $news_id
                ];
                $nnClassHasNewsTableGateway->insert($set);
            }
            $this->adapter->getDriver()->getConnection()->commit();
            $data = $this->getOptions($request);
            return new ApiSuccessResponse(0, $data, [
                'update success'
            ]);
        } catch (\Exception $e) {
            $this->adapter->getDriver()->getConnection()->rollback();
            loggerException($e);
            return new ApiErrorResponse(417, [], [
                'tract' => $e->getTrace(),
                'message' => $e->getMessage()
            ], [
                'update fail'
            ]);
            
        }
    }
}
