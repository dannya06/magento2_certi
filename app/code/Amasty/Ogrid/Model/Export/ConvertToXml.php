<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Ogrid
 */


namespace Amasty\Ogrid\Model\Export;


class ConvertToXml extends \Magento\Ui\Model\Export\ConvertToXml
{
    use ExportTrait;

    public function getXmlFile()
    {
        $component = $this->filter->getComponent();

        $name = md5(microtime());
        $file = 'export/'. $component->getName() . $name . '.xml';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();

        $component->getContext()->getDataProvider()->setLimit(0, 0);

        /** @var SearchResultInterface $searchResult */
        $searchResult = $component->getContext()->getDataProvider()->getSearchResult();

        /** @var DocumentInterface[] $searchResultItems */
        $searchResultItems = $this->getDataProviderItems($component->getContext()->getDataProvider());//$searchResult->getItems();

        $this->prepareItems($component->getName(), $searchResultItems);

        /** @var SearchResultIterator $searchResultIterator */
        $searchResultIterator = $this->iteratorFactory->create(['items' => $searchResultItems]);

        /** @var Excel $excel */
        $excel = $this->excelFactory->create([
            'iterator' => $searchResultIterator,
            'rowCallback'=> [$this, 'getRowData'],
        ]);

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $excel->setDataHeader($this->metadataProvider->getHeaders($component));
        $excel->write($stream, $component->getName() . '.xml');

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

}