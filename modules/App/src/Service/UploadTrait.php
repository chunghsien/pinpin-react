<?php

namespace App\Service;

use Psr\Http\Message\ServerRequestInterface;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Validator\File\IsImage;
use Laminas\Db\RowGateway\RowGateway;
use Laminas\Validator\AbstractValidator;
use Laminas\Diactoros\UploadedFile;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\ValidatorInterface;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Svgo;
use Spatie\ImageOptimizer\Optimizers\Cwebp;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Intervention\Image\ImageManager;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;

trait UploadTrait
{

    /**
     *
     * @var JsonResponse
     */
    protected $uploadResponse;

    /**
     *
     * @var []
     */
    protected $uploaded = [];

    /**
     *
     * @return \Laminas\Diactoros\Response\JsonResponse
     */
    public function getUploadResponse()
    {
        return $this->uploadResponse;
    }

    public function getUploaded()
    {
        return $this->uploaded;
    }

    public function validator(UploadedFile $uploadFile, AbstractValidator $validator)
    {
        $validator->isValid($uploadFile);
        return $validator->getMessages();
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param AbstractTableGateway $tablegateway
     * @param ValidatorInterface[] $otherValidators
     */
    public function processUpload(ServerRequestInterface $request, AbstractTableGateway $tablegateway = null, $otherValidators = [])
    {
        if ($files = $request->getUploadedFiles()) {
            $storefolder = './public/storage/uploads/' . date("Ymd");
            if (! is_dir($storefolder)) {
                mkdir($storefolder, 0644, true);
            }
            $isImage = new IsImage();
            $post = $request->getParsedBody();

            $row = null;
            if ($tablegateway instanceof AbstractTableGateway) {
                /**
                 *
                 * @var RowGateway $row
                 */
                $row = $tablegateway->select([
                    'id' => isset($post['id']) ? $post['id'] : 0
                ])->current();
            }
            $imagepattern = '/photo|image|path|banner|avater$/';
            $mediaPattern = '/video|audio$/';
            foreach ($files as $name => $uploadFile) {

                /**
                 *
                 * @var \Laminas\Diactoros\UploadedFile $uploadFile
                 */
                if (preg_match($imagepattern, $name)) {
                    $errorMessage = $this->validator($uploadFile, $isImage);

                    if ($errorMessage) {
                        $this->uploadResponse = new ApiErrorResponse(1, [
                            'File is no image'
                        ], [
                            'File is no image'
                        ]);
                        return;
                    }
                } elseif (preg_match($mediaPattern, $name)) {
                    $errorMessage = $this->validator($uploadFile, new MimeType([
                        'video',
                        'audio',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/pdf',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/csv',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ]));
                    if ($errorMessage) {
                        $errorMessage = array_values($errorMessage);
                        $this->uploadResponse = new ApiErrorResponse(1, $errorMessage, $errorMessage);
                        return;
                    }
                }

                if (count($otherValidators)) {
                    foreach ($otherValidators as $validator) {
                        if ($validator instanceof ValidatorInterface) {
                            $errorMessage = $this->validator($uploadFile, $validator);
                            if ($errorMessage) {
                                $errorMessage = array_values($errorMessage);
                                $this->uploadResponse = new ApiErrorResponse(1, $errorMessage, $errorMessage);
                                return;
                            }
                        }
                    }
                }
                $extMatcher = [];
                preg_match('/\.(?P<ext>\w+)$/', $uploadFile->getClientFilename(), $extMatcher);
                
                $watermarkPrefix = '';
                if ($row && $row->{$name}) {
                    $filename = $row->{$name};
                    if (is_file('./public/' . $filename)) {
                        unlink('./public/' . $filename);
                    }
                    $filename = preg_replace('/\.\w+$/', '.' . $extMatcher['ext'], $filename);
                } else {
                    $filename = crc32(serialize($uploadFile) . uniqid() . time() . microtime());
                    $filename .= '.' . $extMatcher['ext'];
                }
                if ($row) {
                    $pos = 'storage';
                    if (false === strpos($filename, $pos)) {
                        $targetPath = $storefolder . '/' . $filename;
                        $watermarkPrefix = $storefolder . '/';
                    } else {
                        $targetPath = './public' . $filename;
                        $watermarkPrefix = './public';
                    }
                } else {
                    $targetPath = $storefolder . '/' . $filename;
                    if(preg_match('/^\.\/public/', $storefolder)) {
                        $watermarkPrefix = './public';
                    }
                }
                $targetPath = preg_replace('/\/+/', '/', $targetPath);
                //$imgOptimizer = config('system_settings.imageOptimizer');
                $system_settings = config('system_settings');
                $imgOptimizer = $system_settings['imageOptimizer'];
                
                //debug($targetPath);
                $dirVerify = dirname($targetPath);
                if(!is_dir($dirVerify)) {
                    mkdir($dirVerify, 0644, true);
                }
                $uploadFile->moveTo($targetPath);
                if ($isImage->isValid($targetPath)) {
                    $ext = strtolower($extMatcher['ext']);
                    if($ext == 'jpg' || $ext == 'jpeg') {
                        $manager = new ImageManager(array('driver' => 'gd'));
                        $image = $manager->make($targetPath);
                        $watermarkWidth = intval($image->width()/3);
                        $adapter = GlobalAdapterFeature::getStaticAdapter();
                        $systemSettingsTableGateway = new SystemSettingsTableGateway($adapter);
                        $watermarkRow = $systemSettingsTableGateway->select(['key' => 'watermark'])->current();
                        $watermakePath = $watermarkPrefix.$watermarkRow->value;
                        if(is_file($watermakePath)) {
                            $watermakeImage = $manager->make($watermakePath);
                            $rate = $watermakeImage->getWidth() / $watermarkWidth;
                            $watermarkHeight = $watermakeImage->getHeight() / $rate;
                            $watermakeImage->resize($watermarkWidth, $watermarkHeight);
                            $x = ($image->width() - $watermarkWidth) / 2;
                            $y = ($image->getHeight() - $watermakeImage->getHeight()) / 2;
                            $image->insert($watermakeImage, 'top-left', intval($x), intval($y));
                        }
                        $image->save();
                    }
                    if($imgOptimizer) {
                        OptimizerChainFactory::create();
                        $imageOptimizer = new OptimizerChain();
                        switch ($ext) {
                            case 'jpg':
                            case 'jpeg':
                                $imageOptimizer->addOptimizer(new Jpegoptim([
                                '-m85',
                                '--strip-all',
                                '--all-progressive'
                                    ]));
                                break;
                            case 'png':
                                $imageOptimizer->addOptimizer(new Pngquant([
                                '--force'
                                    ]))->addOptimizer(new Optipng([
                                    '-i0',
                                    '-o2',
                                    '-quiet'
                                        ]));
                                    break;
                            case 'gif':
                                $imageOptimizer->addOptimizer(new Gifsicle([
                                '-b',
                                '-O3'
                                    ]));
                                break;
                            case 'svg':
                                $imageOptimizer->addOptimizer(new Svgo([
                                '--disable={cleanupIDs,removeViewBox}'
                                    ]));
                                break;
                            case 'webp':
                                $imageOptimizer->addOptimizer(new Cwebp([
                                '-m 6',
                                '-pass 10',
                                '-mt',
                                'q 80'
                                    ]));
                                break;
                        }
                        $imageOptimizer->optimize($targetPath);
                    }
                    
                } 
                $this->uploaded[$name] = preg_replace('/^\.\/public/', '', $targetPath);
            }
        }
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return boolean
     */
    protected function verifyUpload(ServerRequestInterface $request)
    {
        return count($request->getUploadedFiles()) > 0;
    }
}