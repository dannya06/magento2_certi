<?php 

namespace Icube\CustomCatalogImageResize\Helper;

use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Encryption\EncryptorInterface;
use Icube\CustomCatalogImageResize\Service\ImageResize;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $imageResize;
    protected $encryptor;

	public function __construct(
        ImageResize $imageResize,
        EncryptorInterface $encryptor
    ) {
        $this->imageResize = $imageResize;
        $this->encryptor = $encryptor;
    }

    public function resizeImage($product, $params)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $fileDriver = $objectManager->create('Magento\Framework\Filesystem\Driver\File');
        $mediaDirectory = $directory->getPath('media').'/catalog/product/cache/';

        foreach ($params as $miscParams) {
            if (isset($miscParams['image_type'])) {
                unset($miscParams['image_type']);
            }
            if (isset($miscParams['id'])) {
                unset($miscParams['id']);
            }
            $path = $this->hashDir($miscParams);
            foreach ($product as $image) {
                if (!$fileDriver->isExists($mediaDirectory.$path.$image)) {
                    $this->imageResize->resizeFromImageName($image, $params);
                } 
            }
        }
    }

    private function hashDir(array $miscParams)
    {
        return $this->encryptor->hash(
            implode('_', $this->convertToReadableFormat($miscParams)),
            Encryptor::HASH_VERSION_MD5
        );
    }

    private function convertToReadableFormat(array $miscParams)
    {
        $miscParams['image_height'] = 'h:' . ($miscParams['image_height'] ?? 'empty');
        $miscParams['image_width'] = 'w:' . ($miscParams['image_width'] ?? 'empty');
        $miscParams['quality'] = 'q:' . ($miscParams['quality'] ?? 'empty');
        $miscParams['angle'] = 'r:' . ($miscParams['angle'] ?? 'empty');
        $miscParams['keep_aspect_ratio'] = (!empty($miscParams['keep_aspect_ratio']) ? '' : 'non') . 'proportional';
        $miscParams['keep_frame'] = (!empty($miscParams['keep_frame']) ? '' : 'no') . 'frame';
        $miscParams['keep_transparency'] = (!empty($miscParams['keep_transparency']) ? '' : 'no') . 'transparency';
        $miscParams['constrain_only'] = (!empty($miscParams['constrain_only']) ? 'do' : 'not') . 'constrainonly';
        $miscParams['background'] = !empty($miscParams['background'])
            ? 'rgb' . implode(',', $miscParams['background'])
            : 'nobackground';
        return $miscParams;
    }
}