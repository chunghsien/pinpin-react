<?php 

namespace Chopin\Support;

use Laminas\Mail\Message as MailMessage;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part as MimePart;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Laminas\Mail\Transport\Sendmail;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;

abstract class TwigMail {
    static public function mail(array $options, array $headLines = []) {
        /*$options = [
            'to' => 'xxx@gmai.com',
            'subject' => 'Subject',
            'template' => [
                'path' => '',
                'name' => '',
                'vars' => [],
            ],
            'cc' => [],
            'bcc' => [],
            'reply_to' => [],
            'transport' => [
                'method' => sendmail|smtp,
                'options' => []
            ],
        ];*/

        $path = $options['template']['path'];
        $name = $options['template']['name'];
        $loader = new FilesystemLoader($path);
        $twig = new Environment($loader);
        $vars = $options['template']['vars'];
        $htmlMarkup = $twig->render($name, $vars);
        
        $html = new MimePart($htmlMarkup);
        $html->type = Mime::TYPE_HTML;
        $html->charset = 'utf-8';
        $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        
        $body = new MimeMessage();
        $body->addPart($html);
        
        $mail = new MailMessage();
        $mail->setBody($body);
        if($headLines) {
            $headers = $mail->getHeaders();
            foreach ($headLines as $headLine) {
                $headers->addHeaderLine($headLine[0], $headLine[1]);
            }
        }
        $allowParams = ['from', 'to', 'subject', 'cc', 'bcc', 'reply_to'];
        $underscoreToCamelCase = new UnderscoreToCamelCase();
        foreach ($options as $key => $param) {
            if(false !== array_search($key, $allowParams)) {
                $func = 'set'.ucfirst($underscoreToCamelCase->filter($key));
                $mail->{$func}($param);
            }
        }
        $trsnsport = null;
        if($options['transport']['method'] == 'sendmail') {
            $trsnsport = new Sendmail();
        }
        
        if($options['transport']['method'] == 'smtp') {
            $trsnsport = new SmtpTransport();
            $smtpOptions = new SmtpOptions($options['transport']['options']);
            $trsnsport->setOptions($smtpOptions);
        }
        $trsnsport->send($mail);
    }
}