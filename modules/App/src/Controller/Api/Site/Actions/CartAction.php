<?php
declare(strict_types = 1);
namespace App\Controller\Api\Site\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use App\Service\Site\BannerService;
use Chopin\Store\TableGateway\CartTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Mezzio\Csrf\CsrfMiddleware;

class CartAction extends AbstractAction
{

    // admin/login
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        //$cartTableGateway = new CartTableGateway($this->adapter);
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
        $cartTableGateway = new CartTableGateway($this->adapter);
        $result = $cartTableGateway->addCart($request);
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
        $cartTableGateway = new CartTableGateway($this->adapter);
        $where = [];
        $guest_serial = '';
        if($request->getAttribute('method_or_id', null)) {
            $guest_serial = $request->getAttribute('method_or_id', null);
            $where = ['guest_serial' =>$guest_serial];
        }else {
            $params = json_decode($request->getQueryParams()['params'], true);
            $where = $params;
            //$guest_serial = $where['guest_serial'];
        }
        if(!$guest_serial) {
            $request = $request->withAttribute('method_or_id', $where['guest_serial']);
        }
        $cartTableGateway->delete($where);
        return $this->get($request);
    }
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $contents = json_decode($request->getBody()->getContents(), true);
        $params = $contents['params'];
        $cartTableGateway = new CartTableGateway($this->adapter);
        $set = ['quantity' => $params['quantity']];
        unset($params['quantity']);
        $where = $params;
        $cartTableGateway->update($set, $where);
        $request = $request->withAttribute('method_or_id', $where['guest_serial']);
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
            $cartTableGateway = new CartTableGateway($this->adapter);
            $vars = array_merge($vars, $cartTableGateway->getCart($request));
        }
        return $vars;
    }
}
