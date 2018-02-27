UPDATE `cms_block` SET `content`= '<div class="row nomargin row-same-height">
    <div class="image-wrapper-1 box-content-1 col-md-4 col-sm-12 col-xs-12 nopadding paddingright paddingbottom box-content valign-parent">
        <img class="bg-img" src="{{media url="wysiwyg/pearl_theme/section-1.jpg"}}" alt=" />
    </div>
    <div class="box-content-2 col-md-8 col-sm-12 col-xs-12 nopadding paddingleft paddingbottom paddingright box-content valign-parent">
        <div class="subtitle">
            <h4>Fashion for Spring</h4>
        </div>
        <div>
            {{widget type="Magento\CatalogWidget\Block\Product\ProductsList" products_count="6" template="product/widget/content/grid_v5.phtml" conditions_encoded="{{widget_condition_1}}"}}
        </div>
    </div>
</div>' WHERE `identifier` = 'section_content1_v5';

UPDATE `cms_block` SET `content`= '<div class="row nomargin row-same-height">
    <div class="box-content-2 col-md-8 col-sm-12 col-xs-12 nopadding paddingleft paddingbottom paddingright box-content valign-parent">
        <div class="subtitle">
            <h4>Fashion for Spring</h4>
        </div>
        <div>
            {{widget type="Magento\CatalogWidget\Block\Product\ProductsList" products_count="6" template="product/widget/content/grid_v5.phtml" conditions_encoded="{{widget_condition_2}}"}}
        </div>
    </div>
    <div class="image-wrapper-2 box-content-1 col-md-4 col-sm-12 col-xs-12 nopadding paddingright paddingbottom box-content valign-parent">
        <img class="bg-img" src="{{media url="wysiwyg/pearl_theme/section-2.jpg"}}" alt=" />
    </div>
</div>' WHERE `identifier` = 'section_content2_v5';

UPDATE `cms_block` SET `content`= '<div class="row nomargin row-same-height">
    <div class="image-wrapper-3 box-content-1 col-md-4 col-sm-12 col-xs-12 nopadding paddingright paddingbottom box-content valign-parent">
        <img class="bg-img" src="{{media url="wysiwyg/pearl_theme/section-3.jpg"}}" alt=" />
    </div>
    <div class="box-content-2 col-md-8 col-sm-12 col-xs-12 nopadding paddingleft paddingbottom paddingright box-content valign-parent">
        <div class="subtitle">
            <h4>Fashion for Spring</h4>
        </div>
        <div>
            {{widget type="Magento\CatalogWidget\Block\Product\ProductsList" products_count="6" template="product/widget/content/grid_v5.phtml" conditions_encoded="{{widget_condition_1}}"}}
        </div>
    </div>
</div>' WHERE `identifier` = 'section_content3_v5';

UPDATE `cms_block` SET `content`= '<h3 class="title-v8">Latest Product</h3>
<div class="catalog-product-v8">{{widget type="Magento\CatalogWidget\Block\Product\ProductsList" products_count="8" template="product/widget/content/grid_v8.phtml" conditions_encoded="{{widget_condition_1}}"}}</div>' WHERE `identifier` = 'latest_product_v8';

UPDATE `cms_block` SET `content`= '<div class="content-v10">
    <div class="row">
        <div class="section-header">
            <h2>Shop Now</h2>
        </div>
        {{widget type="Magento\CatalogWidget\Block\Product\ProductsList" products_count="4" template="product/widget/content/grid_v10.phtml" conditions_encoded="{{widget_condition_1}}"}}
    </div>
</div>' WHERE `identifier` = 'content5_shopnow_v10';