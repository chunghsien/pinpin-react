<?php
declare(strict_types = 1);
namespace App\Controller\Api\Site\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Store\TableGateway\WishlistTableGateway;

class WishlistAction extends AbstractAction
{

    // admin/login
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $vars = $this->getStandByVars($request);
        return new ApiSuccessResponse(0, $vars);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $wishlistTableGateway = new WishlistTableGateway($this->adapter);
        $result = $wishlistTableGateway->addWishlist($request);
        $status = $result['status'];
        $message = $result['message'];
        $vars = array_merge($this->getCommonVars($request), $result);
        if ($status == 'success') {
            return new ApiSuccessResponse(0, $vars, $message);
        } else {
            ApiErrorResponse::$status = 200;
            return new ApiErrorResponse(1, $vars, $message);
        }
    }
    
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $wishlistTableGateway = new WishlistTableGateway($this->adapter);
        $where = [];
        $guest_serial = '';
        if($request->getAttribute('method_or_id', null)) {
            $guest_serial = $request->getAttribute('method_or_id', null);
            $where = ['guest_serial' =>$guest_serial];
            $query = $request->getQueryParams();
            if(isset($query['params'])) {
                $params = json_decode($query['params'], true);
                $where['products_id'] = $params['products_id'];
            }
        }
        $wishlistTableGateway->delete($where);
        return $this->get($request);
    }
    protected function isUseCommonVars(ServerRequestInterface $request)
    {
        $query = $request->getQueryParams();
        $methodOrId = $request->getAttribute('method_or_id', null) === null;
        $queryGuestSerial = (empty($query['guest_serial']) && strtolower($request->getMethod()) == 'get');
        return $methodOrId && $queryGuestSerial;
    }
    
    public function getStandByVars(ServerRequestInterface $request)
    {
        $vars = [];
        if($this->isUseCommonVars($request)) {
            $vars = array_merge($vars, $this->getCommonVars($request));
        }else {
            $wishlistTableGateway = new WishlistTableGateway($this->adapter);
            $vars = array_merge($vars, $wishlistTableGateway->getWishlist($request));
        }
        return $vars;
    }
}
