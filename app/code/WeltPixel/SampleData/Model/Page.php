<?php

namespace WeltPixel\SampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Framework\App\ProductMetadataInterface;

class Page
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    private $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Cms\Model\PageFactory $pageFactory,
        ProductMetadataInterface $productMetadata
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->pageFactory = $pageFactory;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param array $fixtures
     * @param mixed $sliderId
     * @throws \Exception
     */
    public function install(array $fixtures, $sliderId)
    {
        $magentoVersion = $this->productMetadata->getVersion();

        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;

                switch ($row['identifier']) {
                    case 'home-page-v1':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" slider_id="'. $sliderId .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);
                        break;
                    case 'home-page-v5':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" slider_id="'. $sliderId[0] .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);

                        /** Listing widgets placeholders for conditions */
                        $conditionWidgetsPlaceHolder = ['{{widget_condition_1}}', '{{widget_condition_2}}'];
                        if (version_compare($magentoVersion, '2.2', '<')) {
                            //initial version
                            $conditionWidgetsContent = [
                                'a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:2:`24`;]]',
                                'a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:2:`15`;]]'
                            ];
                        } else  {
                            // new 2.2 version
                            $conditionWidgetsContent = [
                                '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`24`^]^]',
                                '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`15`^]^]'
                            ];
                        }
                        $row['content'] = str_replace($conditionWidgetsPlaceHolder,$conditionWidgetsContent, $row['content']);

                        break;
                    case 'home-page-v7':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" slider_id="'. $sliderId[0] .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);
                        break;
                    case 'home-page-v8':
                        $widgetPlaceholders = ['{{widget_nr_1}}', '{{widget_nr_2}}'];
                        $widgetContents = [
                            '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" slider_id="'. $sliderId[0] .'"}}',
                            '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" slider_id="'. $sliderId[1] .'"}}'
                        ];
                        $row['content'] = str_replace($widgetPlaceholders,$widgetContents, $row['content']);

                        /** Listing widgets placeholders for conditions */
                        $conditionWidgetsPlaceHolder = ['{{widget_condition_1}}'];
                        if (version_compare($magentoVersion, '2.2', '<')) {
                            //initial version
                            $conditionWidgetsContent = [
                                'a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:1:`2`;]]'
                            ];
                        } else  {
                            // new 2.2 version
                            $conditionWidgetsContent = [
                                '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`2`^]^]'
                            ];
                        }
                        $row['content'] = str_replace($conditionWidgetsPlaceHolder,$conditionWidgetsContent, $row['content']);

                        break;
                    case 'home-page-v9':
                        $widgetPlaceholders = ['{{widget_nr_1}}', '{{widget_nr_2}}'];
                        $widgetContents = [
                            '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" slider_id="'. $sliderId[0] .'"}}',
                            '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" slider_id="'. $sliderId[1] .'"}}'
                        ];
                        $row['content'] = str_replace($widgetPlaceholders,$widgetContents, $row['content']);
                        break;
                    case 'home-page-v10':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" slider_id="'. $sliderId[0] .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);

                        /** Listing widgets placeholders for conditions */
                        $conditionWidgetsPlaceHolder = ['{{widget_condition_1}}'];
                        if (version_compare($magentoVersion, '2.2', '<')) {
                            //initial version
                            $conditionWidgetsContent = [
                                'a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:2:`24`;]]'
                            ];
                        } else  {
                            // new 2.2 version
                            $conditionWidgetsContent = [
                                '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`24`^]^]'
                            ];
                        }
                        $row['content'] = str_replace($conditionWidgetsPlaceHolder,$conditionWidgetsContent, $row['content']);
                        break;
                    case 'home-page-v12':
                        $widgetPlaceholder = '{{widget_nr_1}}';
                        $widgetContent = '{{widget type="WeltPixel\OwlCarouselSlider\Block\Slider\Custom" slider_id="'. $sliderId[0] .'"}}';
                        $row['content'] = str_replace($widgetPlaceholder,$widgetContent, $row['content']);
                        break;
                    case 'home-page-v15':
                        /** Listing widgets placeholders for conditions */
                        $conditionWidgetsPlaceHolder = ['{{widget_condition_1}}', '{{widget_condition_2}}', '{{widget_condition_3}}'];
                        if (version_compare($magentoVersion, '2.2', '<')) {
                            //initial version
                            $conditionWidgetsContent = [
                                'a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:2:`24`;]]',
                                'a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:2:`25`;]]',
                                'a:2:[i:1;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Combine`;s:10:`aggregator`;s:3:`all`;s:5:`value`;s:1:`1`;s:9:`new_child`;s:0:``;]s:4:`1--1`;a:4:[s:4:`type`;s:50:`Magento|CatalogWidget|Model|Rule|Condition|Product`;s:9:`attribute`;s:12:`category_ids`;s:8:`operator`;s:2:`==`;s:5:`value`;s:2:`15`;]]'
                            ];
                        } else  {
                            // new 2.2 version
                            $conditionWidgetsContent = [
                                '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`24`^]^]',
                                '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`25`^]^]',
                                '^[`1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Combine`,`aggregator`:`all`,`value`:`1`,`new_child`:``^],`1--1`:^[`type`:`Magento||CatalogWidget||Model||Rule||Condition||Product`,`attribute`:`category_ids`,`operator`:`==`,`value`:`15`^]^]'
                            ];
                        }
                        $row['content'] = str_replace($conditionWidgetsPlaceHolder,$conditionWidgetsContent, $row['content']);
                        break;
                }

                $this->pageFactory->create()
                    ->load($row['identifier'], 'identifier')
                    ->addData($row)
                    ->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->save();
            }
        }
    }
}
