<?php
namespace WeltPixel\Command\Model;

use Magento\Framework\View\Asset\PreProcessor\AlternativeSource\AssetBuilder;
use Magento\Deploy\Model\Filesystem as Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class GenerateCss
{
    /**
     * @var AssetBuilder
     */
    protected $assetBuilder;

    /** @var \Magento\Framework\View\Asset\Repository */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\App\View\Asset\Publisher
     */
    protected $assetPublisher;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param AssetBuilder $assetBuilder
     * @param \Magento\Framework\App\View\Asset\Publisher $assetPublisher
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param Filesystem $filesystem
     */
    public function __construct(
        AssetBuilder $assetBuilder,
        \Magento\Framework\App\View\Asset\Publisher $assetPublisher,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $file,
        Filesystem $filesystem
    ) {
        $this->assetBuilder = $assetBuilder;
        $this->assetRepo = $assetRepo;
        $this->assetPublisher = $assetPublisher;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $theme Pearl/weltpixel_custom
     * @param string $locale en_US
     * @param string $storeCode
     * @return bool
     */
    public function processContent($theme, $locale, $storeCode) {

        $filesToGenerate = [
            "css/styles-l-temp.css",
            "css/styles-m-temp.css",
            "WeltPixel_CategoryPage::css/weltpixel_category_store_" . $storeCode .".css",
            "WeltPixel_CustomHeader::css/weltpixel_custom_header_" . $storeCode .".css",
            "WeltPixel_ProductPage::css/weltpixel_product_store_" . $storeCode .".css"
        ];

        $this->filesystem->cleanupFilesystem(
            [
                DirectoryList::TMP_MATERIALIZATION_DIR
            ]
        );


        foreach ($filesToGenerate as $file) {
            $path = $file;
            $area = 'frontend';
            $theme = $theme;
            $locale = $locale;

            $asset = $this->assetRepo->createAsset($path, [
                'area' => $area,
                'theme' => $theme,
                'locale' => $locale,
                'module' => null
            ]);

            $filePath = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::STATIC_VIEW)
                . DIRECTORY_SEPARATOR .$asset->getRelativeSourceFilePath() ;


            if ($this->file->isExists($filePath)) {
                $this->file->deleteFile($filePath);
            }

            /** For production mode */
            $lessFile = rtrim($filePath, 'css');
            $lessFile .= 'less';
            if ($this->file->isExists($lessFile)) {
                $this->file->deleteFile($lessFile);
            }

            $this->assetPublisher->publish($asset);

            /** only for styles-l and styles-m, as they have temporary less files, their regeneration takes longer */
            if (strpos($filePath, '-temp.css') !== false ) {
                $newPath = str_replace('-temp.css', '', $filePath);
                $newPath .= '.css';
                copy($filePath, $newPath);
            }
        }

        return true;
    }



}