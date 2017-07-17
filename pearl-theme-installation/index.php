<?php
try {
    require __DIR__ . '/../app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    exit(1);
}

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$storeRepository = $objectManager->get('\Magento\Store\Model\StoreRepository');
$helperBackend = $objectManager->get('\Magento\Backend\Helper\Data');
$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

$homeUrl = $storeManager->getStore()->getBaseUrl();
$backendUrl = $helperBackend->getHomePageUrl();

$stores = $storeRepository->getList();
$storeList = array(
        "GLOBAL" => "GLOBAL"
);
foreach ($stores as $store) {
    $storeId = $store["code"];
    $storeName = $store["name"];
    $storeList[$storeId] = $storeName;
}
unset($storeList['admin']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Pearl Theme Installation">
    <meta name="author" content="WeltPixel">

    <title>Pearl Theme Installation</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
<div class="jumbotron">
    <div class="container">
        <h1>Pearl Theme Instalation</h1>
        <p>This is a simple GUI wizard for the Pearl Theme installation and configuration.</p>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <p class="notification-msg">Please make sure that you have copied the source files and the sample data to your magento installation.</p>
        </div>
    </div>
    <!-- Example row of columns -->
    <div class="row">
        <div class="col-md-4">
            <h2>Step 1</h2>
            <p>In this step we run the upgrade scripts and clear the cache</p>
        </div>
        <div class="col-md-4">
            <h2>Step 2</h2>
            <p>In this step we clear the cache and regenerate the theme specific less files. </p>
        </div>
        <div class="col-md-4">
            <h2>Step 3</h2>
            <p>In this step the Pearl theme can be activated for a desired store view, or globally, using the GLOBAL input. </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <h2>Step 4</h2>
            <p>In this step you can configure your store, import predefined demo versions.</p>
        </div>
        <div class="col-md-4">
            <h2>Step 5</h2>
            <p>In this step you can configure your store, using different configuration options.</p>
        </div>
        <div class="col-md-4">
            <h2>Step 6</h2>
            <p>In this step we cleanup the cache and generate the theme specific files after the customizations.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <h2>Step 7</h2>
            <p>Success page.</p>
        </div>
    </div>

    <hr>

    <div class="container">

        <div class="stepwizard col-xs-12">
            <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step">
                    <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                    <p>Step 1</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                    <p>Step 2</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
                    <p>Step 3</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
                    <p>Step 4</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-5" type="button" class="btn btn-default btn-circle" disabled="disabled">5</a>
                    <p>Step 5</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-6" type="button" class="btn btn-default btn-circle" disabled="disabled">6</a>
                    <p>Step 6</p>
                </div>
                <div class="stepwizard-step">
                    <a href="#step-7" type="button" class="btn btn-default btn-circle" disabled="disabled">7</a>
                    <p>Step 7</p>
                </div>
            </div>
        </div>

        <form role="form" action="" method="post">
            <div class="row setup-content" id="step-1">
                <div class="col-xs-12 col-md-6 col-md-offset-3">
                    <div class="col-md-12">
                        <h3> Step 1 - Install Pearl Theme and included extensions</h3>
                        <div class="content">
                            This step will clean cache and run the upgrades for the modules.
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="skip" id="skip-step-1">Skip this step
                            </label>
                        </div>

                        <button class="btn btn-primary nextBtn btn-lg pull-right" data-loading-text="<i class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></i> Processing" type="button" >Proceed</button>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-2">
                <div class="col-xs-12 col-md-6 col-md-offset-3">
                    <div class="col-md-12">
                        <h3> Step 2 - Theme specific less generation</h3>
                        <div class="content">
                            This step will clean cache and generate the theme specific less files.
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="skip" id="skip-step-2">Skip this step
                            </label>
                        </div>

                        <button class="btn btn-primary nextBtn btn-lg pull-right" data-loading-text="<i class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></i> Processing" type="button" >Proceed</button>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-3">
                <div class="col-xs-12 col-md-6 col-md-offset-3">
                    <div class="col-md-12">
                        <h3> Step 3 - Theme Activation</h3>
                        <div class="content">
                            Here you can activate the Pearl Theme for your store. If you don't want to activate it, you can skip this step. <br/><br/>
                            If you want to set the theme for all markets use <b>GLOBAL</b> otherwise specify the store code or store id.
                        </div>
                        <div class="form-group">
                            <label class="control-label">Store Code</label>
                            <select  name="theme-activation-store-code" id="theme-activation-store-code" class="form-control" required="required">
                                <?php foreach ($storeList as $code => $storeName) : ?>
                                    <option value="<?php echo $code ?>"><?php echo $storeName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="skip" id="skip-step-3">Skip this step
                            </label>
                        </div>

                        <button class="btn btn-primary nextBtn btn-lg pull-right" data-loading-text="<i class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></i> Processing" type="button" >Proceed</button>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-4">
                <div class="col-xs-12 col-md-6 col-md-offset-3">
                    <div class="col-md-12">
                        <h3> Step 4 - Import Demo Configuration</h3>
                        <div class="form-group">
                            <label class="control-label">Store Code</label>
                            <select  name="demo-configuration-store-code" id="demo-configuration-store-code" class="form-control" required="required">
                                <?php foreach ($storeList as $code => $storeName) : ?>
                                    <option value="<?php echo $code ?>"><?php echo $storeName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Demo version</label>
                            <select class="form-control" id="demo-configuration-version" name="demo-configuration-version" required="required">
                                <option value="v1">v1</option>
                                <option value="v2">v2</option>
                                <option value="v3">v3</option>
                                <option value="v4">v4</option>
                                <option value="v5">v5</option>
                                <option value="v6">v6</option>
                                <option value="v7">v7</option>
                                <option value="v8">v8</option>
                                <option value="v9">v9</option>
                            </select>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="skip" id="skip-step-4">Skip this step
                            </label>
                        </div>
                        <button class="btn btn-primary nextBtn btn-lg pull-right" data-loading-text="<i class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></i> Processing" type="button" >Proceed</button>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-5">
                <div class="col-xs-12 col-md-6 col-md-offset-3">
                    <div class="col-md-12">
                        <h3> Step 5 - Theme Options Configuration</h3>
                        <div class="form-group">
                            <label class="control-label">Store Code</label>
                            <select  name="theme-configuration-store-code" id="theme-configuration-store-code" class="form-control" required="required">
                                <?php foreach ($storeList as $code => $storeName) : ?>
                                    <option value="<?php echo $code ?>"><?php echo $storeName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Home Page Version</label>
                            <select class="form-control" id="theme-configuration-home-page" name="theme-configuration-home-page" required="required">
                                <option value="v1">v1</option>
                                <option value="v2">v2</option>
                                <option value="v3">v3</option>
                                <option value="v4">v4</option>
                                <option value="v5">v5</option>
                                <option value="v6">v6</option>
                                <option value="v7">v7</option>
                                <option value="v8">v8</option>
                                <option value="v9">v9</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Header Version</label>
                            <select class="form-control" id="theme-configuration-header" name="theme-configuration-header" required="required">
                                <option value="v1">v1</option>
                                <option value="v2">v2</option>
                                <option value="v3">v3</option>
                                <option value="v4">v4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Category Page Columns</label>
                            <select class="form-control" id="theme-configuration-category-columns" name="theme-configuration-category-columns" required="required">
                                <option value="2columns">2columns</option>
                                <option value="3columns">3columns</option>
                                <option value="4columns">4columns</option>
                                <option value="5columns">5columns</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Product Page Version</label>
                            <select class="form-control" id="theme-configuration-product-page" name="theme-configuration-product-page" required="required">
                                <option value="v1">v1</option>
                                <option value="v2">v2</option>
                                <option value="v3">v3</option>
                                <option value="v4">v4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Footer Version</label>
                            <select class="form-control" id="theme-configuration-footer" name="theme-configuration-footer" required="required">
                                <option value="v1">v1</option>
                                <option value="v2">v2</option>
                                <option value="v3">v3</option>
                                <option value="v4">v4</option>
                            </select>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="skip" id="skip-step-5">Skip this step
                            </label>
                        </div>
                        <button class="btn btn-primary nextBtn btn-lg pull-right" data-loading-text="<i class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></i> Processing" type="button" >Proceed</button>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-6">
                <div class="col-xs-12 col-md-6 col-md-offset-3">
                    <div class="col-md-12">
                        <h3> Step 6 - Cache cleanup</h3>
                        <div class="content">
                            <p>In this step we cleanup the cache and generate the theme specific files after the customizations.</p>
                            <p><b>Don't skip this test if you made configuration changes.</b></p>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="skip" id="skip-step-6">Skip this step
                            </label>
                        </div>

                        <button class="btn btn-primary nextBtn btn-lg pull-right" data-loading-text="<i class='glyphicon glyphicon-refresh glyphicon-refresh-animate'></i> Processing" type="button" >Proceed</button>
                    </div>
                </div>
            </div>
            <div class="row setup-content" id="step-7">
                <div class="col-xs-6 col-md-offset-3">
                    <div class="col-md-12">
                        <h3> Step 7 - Success Page</h3>
                        <div class="success-container">
                            <p>Congratulations!</p>
                            <p>Theme was installed and configured successfully.</p>
                            <a class="btn btn-success" target="_blank" href="<?php echo $homeUrl ?>">Go to Homepage</a>
                            <a class="btn btn-success" target="_blank" href="<?php echo $backendUrl ?>">Go to Admin</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" >
                <h3>Installation Log</h3>
                <div class="row result-container" >
                </div>
            </div>
        </form>

    </div>

    <footer>
        <p class="copyright">&copy; <?php echo date("Y"); ?> WeltPixel</p>
    </footer>
</div> <!-- /container -->


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/custom.js"></script>
</body>
</html>
