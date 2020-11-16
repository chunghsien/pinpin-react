<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use App\Service\ApiQueryService;
use App\Service\AjaxFormService;
use Chopin\Newsletter\TableGateway\ContactTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\Support\TwigMail;

class ContactAction extends AbstractAction
{

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        ContactTableGateway::$isRemoveRowGatewayFeature = true;
        $tablegateway = new ContactTableGateway($this->adapter);
        $response = $ajaxFormService->getProcess($request, $tablegateway);
        if ($response->getStatusCode() == 200) {
            return $response;
        } else {
            $apiQueryService = new ApiQueryService($tablegateway);
            // $apiQueryService->setTableGateway($tablegateway);
            return $apiQueryService->processPaginator($request, 'src/App/scripts/db/admin/contact.php', [
                'display_name' => 'language_has_locale',
                'full_name' => 'contact_decrypt',
                'email' => 'contact_decrypt',
                'subject' => 'contact_decrypt',
                'is_reply' => 'contact_decrypt',
                'created_at' => 'contact_decrypt',
            ]);
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::delete()
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        return $ajaxFormService->deleteProcess($request, new ContactTableGateway($this->adapter));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $post = $request->getParsedBody();
        //debug($post);
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new ContactTableGateway($this->adapter);
        $sendCheck = false;
        $row = $tablegateway->select([
            'id' => $post['id']
        ])->current();
        if ($row->is_reply == 0 && isset($post['reply']) && trim($post['reply'])) {
            $sendCheck = true;
        }

        $response = $ajaxFormService->putProcess($request, $tablegateway);

        if ($response instanceof ApiSuccessResponse && $sendCheck) {
            // 寄信
            $lang = str_replace('-', '_', $request->getAttribute('html_lang'));
            $system_settings = $request->getAttribute('system_settings');
            $site_info = $system_settings['site_info'][$lang];
            $mailSettings = $system_settings['mail-service']['children'];
            //$systemSettingsTableGateway = new SystemSettingsTableGateway($this->adapter);
            // $mailServerOptions = ["connection_class"=> 'login'];
            $mailServerOptions = [];
            if ($mailSettings['mail_method']['value'] == 'smtp') {

                foreach ($mailSettings as $item) {
                    // $item = $systemSettingsTableGateway->deCryptData($item);
                    $key = $item['key'];
                    if ($key == 'username' || $key == 'password' || $key == 'ssl') {

                        $mailServerOptions['connection_config'][$key] = $item['value'];
                    } else if ($key != 'from' && $key != 'mail_method') {
                        $mailServerOptions[$key] = $item['value'];
                    }
                }
                $mailServerOptions["connection_class"] = 'login';
            }
            $site_info = $system_settings['site_info'][$lang];
            $system = $system_settings['system'];
            $row = $row->toArray();
            $row['reply'] = $post['reply'];
            $row = $tablegateway->deCryptData($row);
            
            TwigMail::mail([
                'from' => $mailSettings['from']['value'],
                'to' => $row['email'],
                'subject' => 'Re: ' . $row['subject'],
                'template' => [
                    'path' => './modules/App/resources/templates/' . $lang . '/',
                    'name' => 'contact_reply.html.twig',
                    'vars' => [
                        'site_info' => $site_info,
                        'comp_logo' => $system['children']['comp_logo'],
                        'item' => $row,
                    ],
                ],
                'transport' => [
                    'method' => $mailSettings['mail_method']['value'],
                    'options' => $mailServerOptions
                ]
            ]);
            
        }
        if (isset($post['reply']) && trim($post['reply'])) {
            $row = $tablegateway->select([
                'id' => $post['id']
            ])->current();
            if ($row->is_reply == 0) {
                $row->is_reply = 1;
                $row->save();
            }
        }

        return $response;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::post()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        if (isset($queryParams['put'])) {
            return $this->put($request);
        }
        return new ApiErrorResponse(1, [], []);
    }
}
