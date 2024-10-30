<?php
/**
 * Plugin Name: bidorbuy Store Integrator
 * Plugin URI: www.bidorbuy.co.za
 * phpcs:enable
 * Description: The bidorbuy store integrator allows you to get products from your online store listed on bidorbuy
 * quickly and easily.
 * phpcs:disable
 * Author: bidorbuy
 * Author URI: www.bidorbuy.co.za
 * Version: 2.12.0
 */

/**
 * Copyright (c) 2014, 2015, 2016 Bidorbuy http://www.bidorbuy.co.za
 * This software is the proprietary information of Bidorbuy.
 *
 * All Rights Reserved.
 * Modification, redistribution and use in source and binary forms, with or without
 * modification are not permitted without prior written approval by the copyright
 * holder.
 *
 * Vendor: EXTREME IDEA LLC http://www.extreme-idea.com
 */

namespace Com\ExtremeIdea\Bidorbuy\StoreIntegrator\WooCommerce;

use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core as bobsi;
use Symfony\Component\HttpFoundation\Request;

// phpcs:disable PSR1.Files.SideEffects
if (!defined('ABSPATH')) {
    die();
}

require_once(dirname(__FILE__) . '/vendor/autoload.php');
require_once(ABSPATH . '/wp-admin/includes/plugin.php');

register_activation_hook(
    __FILE__,
    ['Com\ExtremeIdea\Bidorbuy\StoreIntegrator\WooCommerce\BidorbuyStoreIntegrator', 'activate']
);
add_action(
    'plugins_loaded',
    ['Com\ExtremeIdea\Bidorbuy\StoreIntegrator\WooCommerce\BidorbuyStoreIntegrator', 'init']
);
// phpcs:enable PSR1.Files.SideEffects

/**
 * Class BidorbuyStoreIntegrator.
 *
 * @package com\extremeidea\bidorbuy\storeintegrator\woocommerce
 *
 * @SuppresWarnings(PHPMD.CouplingBetweenObjects)
 */
class BidorbuyStoreIntegrator
{
    const ENDPOINT_NAMESPACE = 'bidorbuystoreintegrator';
    const WOOCOMMERCE_PLUGIN_PHP_FILE = 'woocommerce/woocommerce.php';
    const WOOCOMMERCE_ATTRIBUTE_COLUMN = 'bobsi_attribute_flag';
    const WOOCOMMERCE_ATTRIBUTE_FIELD = 'bobsi_attribute_field';
    const ALLOW_OFFERS_SET_DEFAULTS = 'bobsi_allow_offers_set_defaults';
    const SETTING_CLEAN_OUTPUT_CACHE_BEFORE_ACTION = 'bobsi_setting_clean_output_cache_before_action';

    const VERSION = '2.12.0';

    protected static $instance;

    public $wpdb;
    public $core;
    public $currencyConverter;
    public $request;

    protected function __construct()
    {
        global $wpdb;
        $request = new Request();
        $this->wpdb = $wpdb;
        $this->request = $request->createFromGlobals();
        $this->currencyConverter = new WoocommerceCurrencyConverter($this);
        $this->core = $this->initializeCore();

        bobsi\Version::$version = static::VERSION;

        $this->registerActions();
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function registerActions()
    {
        /* View */
        add_action(
            'woocommerce_after_add_attribute_fields',
            [$this, 'woocommerceAfterAddAttributeFields']
        );
        add_action(
            'woocommerce_after_edit_attribute_fields',
            [$this, 'woocommerceAfterEditAttributeFields']
        );
        add_action(
            'woocommerce_after_product_attribute_settings',
            [$this, 'woocommerceAfterProductAttributeSettings'],
            10,
            2
        );
        /* Actions */
        add_action(
            'woocommerce_attribute_added',
            [$this, 'woocommerceAttributeAdded']
        );
        add_action(
            'woocommerce_attribute_updated',
            [$this, 'woocommerceAttributeUpdated']
        );
        add_action(
            'admin_init',
            [$this, 'wpAjaxWoocommerceSaveAttributes']
        );
        add_action(
            'admin_init',
            [$this, 'registerSetting']
        );
        add_action(
            'init',
            [$this, 'pluginCheckUpdate']
        );
        add_action(
            'init',
            [$this, 'registerEndpoint']
        );
        add_action(
            'pre_get_posts',
            [$this, 'initEndpoint']
        );
        if (get_option('show_admin_notices')) {
            add_action(
                'admin_notices',
                [$this, 'newUrlsWarning']
            );
        }
        if (is_plugin_active('comet-cache/comet-cache.php')) {
            add_action(
                'admin_notices',
                [$this, 'cometCacheWarning']
            );
        }
        $page_hook_suffix = 'settings_page_bidorbuystoreintegrator'; // Settings Menu Slug bobsi\Version::$id
        add_action(
            'admin_menu',
            function () {
                add_options_page(
                    bobsi\Version::$name,
                    bobsi\Version::$name,
                    'manage_options',
                    bobsi\Version::$id,
                    [$this, 'settingsPage']
                );
            }
        );
        add_action(
            'admin_notices',
            [$this, 'showBetaTesterBanner']
        );
        add_action(
            'admin_print_scripts-' . $page_hook_suffix,
            [$this, 'requiredScripts']
        );

        $page = isset($_GET['page']) ? $_GET['page'] : false;
        // Determinate Plugin Page
        // Example request wp-admin/admin.php?page=bidorbuystoreintegrator
        if ($page && $page == 'bidorbuystoreintegrator') {
            // Fix for YITH plugins
            add_filter(
                'admin_body_class',
                function ($adminBodyClasses) {
                    //Delete class woocommerce from <body> tag
                    return str_replace(' woocommerce ', '', $adminBodyClasses);
                },
                100
            );

            /* Defect #4031 */
            add_filter('style_loader_src', [$this, 'deleteCss']);
        }

        $triggers = new BidorbuyStoreIntegratorTriggers($this);
        $triggers->registerTriggersActions();
    }

    protected function initializeCore()
    {
        $platform = 'WordPress ' . strval(get_bloginfo('version'));
        $woocommercePluginFile = WP_PLUGIN_DIR . '/' . static::WOOCOMMERCE_PLUGIN_PHP_FILE;

        if (file_exists($woocommercePluginFile)) {
            $data = get_plugin_data($woocommercePluginFile);
            $platform .= ', ' . $data['Name'] . ' ' . $data['Version'];
        }

        $currencyConverterPluginFile = $this->currencyConverter->getPluginFile();
        if (file_exists($currencyConverterPluginFile)) {
            $data = get_plugin_data($currencyConverterPluginFile);
            $platform .= ', ' . $data['Name'] . ' ' . $data['Version'];
        }

        $dbSettings = [bobsi\Db::SETTING_PREFIX => $this->wpdb->prefix, bobsi\Db::SETTING_SERVER => DB_HOST,
            bobsi\Db::SETTING_USER => DB_USER, bobsi\Db::SETTING_PASS => DB_PASSWORD,
            bobsi\Db::SETTING_DBNAME => DB_NAME];
        $core = new bobsi\Core();
        $core->init(
            get_bloginfo('name'),
            get_bloginfo('admin_email'),
            $platform,
            get_option(bobsi\Settings::NAME),
            $dbSettings
        );

        return $core;
    }

    /**
     * Initialize plugin function
     *
     * @return self
     */
    public static function init()
    {
        if (!self::$instance) {
            self::$instance = new self;

            return self::$instance;
        }

        return self::$instance;
    }

    /**
     * Check Woocommerce status plugin
     *
     * @return void  or exit if plugin doesn't install or disabled
     */
    public function checkWoocommersPlugin()
    {
        if (is_plugin_active(static::WOOCOMMERCE_PLUGIN_PHP_FILE)) {
            return;
        }

        $this->exitWithError(
            bobsi\Version::$name . ' requires <a href="http://www.woothemes.com/woocommerce/"
        target="_blank">WooCommerce</a> to be activated. Please install and activate <a href="' . admin_url(
                'plugin-install.php?tab=search&type=term&s=WooCommerce'
            ) . '" target="_blank">WooCommerce</a> first.'
        );
    }

    /**
     * Plugin activate hook
     *
     * @return void
     */
    public static function activate()
    {
        $storeIntegrator = new self();
        $warnings = $storeIntegrator->core->getWarnings();
        if (!empty($warnings)) {
            $storeIntegrator->exitWithError(implode('. ', $warnings));
        }

        $storeIntegrator->checkWoocommersPlugin();

        if (!($storeIntegrator->wpdb->query($storeIntegrator->core->getQueries()->getInstallAuditTableQuery())
            && $storeIntegrator->wpdb->query($storeIntegrator->core->getQueries()->getInstallTradefeedTableQuery())
            && $storeIntegrator->wpdb->query(
                $storeIntegrator->core->getQueries()->getInstallTradefeedDataTableQuery()
            ))) {
            $storeIntegrator->exitWithError($storeIntegrator->wpdb->last_error);
        }

        $storeIntegrator->core->getSettings()->setExportStatuses(['publish']);
        $storeIntegrator->core->getSettings()->setExportVisibilities(['visible']);

        //add all products to the queue in case of first activation
        if (!get_option('bobsi_first_activate', false)) {
            $storeIntegrator->addAllProductsInTradefeedQueue();
            update_option('bobsi_first_activate', true);
        }

        update_option(bobsi\Settings::NAME, $storeIntegrator->core->getSettings()->serialize(true));

        $storeIntegrator->registerEndpoint();
        flush_rewrite_rules();
    }

    /**
     * Refresh all products
     *
     * @return void
     */
    public function refreshAllProducts()
    {
        $this->wpdb->query($this->core->getQueries()->getTruncateJobsQuery());
        $this->wpdb->query($this->core->getQueries()->getTruncateProductQuery());
        $this->addAllProductsInTradefeedQueue(true);
    }

    /**
     * Add all products in tradefeed queue.
     *
     * @param bool $update flag to update all products
     *
     * @return bool
     */
    public function addAllProductsInTradefeedQueue($update = null)
    {
        $productsIds = array_chunk($this->getAllProducts(), 500);
        $productStatus = ($update) ? bobsi\Queries::STATUS_UPDATE : bobsi\Queries::STATUS_NEW;

        foreach ($productsIds as $page) {
            if (!$this->wpdb->query($this->core->getQueries()->getAddJobQueries($page, $productStatus))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all products.
     *
     * @return mixed
     */
    public function getAllProducts()
    {
        //    TODO: tax_query starts from 3.1, and As of 3.5, a bug was fixed where tax_query would
        // inadvertently return all posts when a result was empty.
        $statuses = $this->core->getSettings()->getExportStatuses();
        $wpq = ['post_type' => 'product', 'fields' => 'ids', 'post_status' => $statuses];
        $wpq['posts_per_page'] = PHP_INT_MAX;
        $wpq['offset'] = 0;

        $query = new \WP_Query();
        $posts = $query->query($wpq);

        $query = null;

        return $posts;
    }

    /**
     * Register setting
     *
     * @return void
     */
    public function registerSetting()
    {
        register_setting('bobsi-settings', bobsi\Settings::NAME);
    }

    /**
     * Admin Settings Page
     *
     * @return void
     */
    public function settingsPage()
    {

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        foreach ($this->core->getWarnings() as $warn) {
            $this->exitWithError('<strong>' . bobsi\Version::$name . ':</strong> ' . $warn, 'error', false);
        }

        $this->settingsSubmitAction();

        include(dirname(__FILE__) . '/templates/options.tpl.php');
    }

    /**
     * Settings submit action
     *
     * @return void
     */
    protected function settingsSubmitAction()
    {
        $actionResetTokens = $this->request->request->get(bobsi\Settings::NAME_ACTION_RESET);
        $actionSaveSettings = $this->request->request->get('submit_options');
        $actionLoggingForm = $this->request->request->get(bobsi\Settings::NAME_LOGGING_FORM_ACTION);

        if ($actionResetTokens) {
            $this->core->processAction(bobsi\Settings::NAME_ACTION_RESET);
            update_option(bobsi\Settings::NAME, $this->core->getSettings()->serialize(true));
        }

        if ($actionSaveSettings == 1) {
            //            unset($_POST['submit_options']);
            //            unset($_POST['submit']);

            //**************
            $wordings = $this->core->getSettings()->getDefaultWordings();

            $presaved_settings = [];
            $prevent_saving = false;

            $settings_checklist = [bobsi\Settings::NAME_USERNAME => 'strval', bobsi\Settings::NAME_PASSWORD => 'strval',
                bobsi\Settings::NAME_FILENAME => 'strval', bobsi\Settings::NAME_COMPRESS_LIBRARY => 'strval',
                bobsi\Settings::NAME_DEFAULT_STOCK_QUANTITY => 'intval',
                bobsi\Settings::NAME_LOGGING_APPLICATION => 'strval', bobsi\Settings::NAME_LOGGING_LEVEL => 'strval',
                bobsi\Settings::NAME_EXPORT_QUANTITY_MORE_THAN => 'intval',
                bobsi\Settings::NAME_EXCLUDE_CATEGORIES => 'categories',
                bobsi\Settings::NAME_INCLUDE_ALLOW_OFFERS_CATEGORIES => 'categories',
                bobsi\Settings::NAME_EXPORT_STATUSES => 'categories',
                bobsi\Settings::NAME_PRODUCT_CONDITION_SECONDHAND_CATEGORIES => 'categories',
                bobsi\Settings::NAME_PRODUCT_CONDITION_REFURBISHED_CATEGORIES => 'categories',];

            foreach ($settings_checklist as $setting => $prevalidation) {
                $presaved_settings[$setting] = $this->validateSettings($setting, $prevalidation);

                if (!call_user_func(
                    $wordings[$setting][bobsi\Settings::NAME_WORDINGS_VALIDATOR],
                    $presaved_settings[$setting]
                )) {
                    $field = $wordings[$setting][bobsi\Settings::NAME_WORDINGS_TITLE];
                    _e(
                        "<div class='error notice'>
                        <p>
                        <strong>
                            invalid value: ' $presaved_settings[$setting]' 
                            in the field: $field
                        </strong>
                        </p>
                    </div>"
                    );
                    $prevent_saving = true;
                }
            }

            if (!$prevent_saving) {
                //Saving tokens
                $presaved_settings[bobsi\Settings::NAME_TOKEN_EXPORT] = $this->core->getSettings()->getTokenExport();

                $presaved_settings[bobsi\Settings::NAME_TOKEN_DOWNLOAD] = $this->core->getSettings()->getTokenExport();

                $previousSettings = $this->core->getSettings()->serialize(true);

                $this->core->getSettings()->unserialize(serialize($presaved_settings));

                $newSettings = $this->core->getSettings()->serialize(true);

                update_option(bobsi\Settings::NAME, $newSettings);

                if ($this->core->checkIfExportCriteriaSettingsChanged($previousSettings, $newSettings, true)) {
                    $this->refreshAllProducts();
                }

                // Custom Plugin Settings
                $cleanOutputBuffer = (bool)$this->request->request->get(
                    static::SETTING_CLEAN_OUTPUT_CACHE_BEFORE_ACTION,
                    false
                );
                update_option(static::SETTING_CLEAN_OUTPUT_CACHE_BEFORE_ACTION, $cleanOutputBuffer);
            }
        }

        if ($actionLoggingForm) {
            $file = $this->request->request->get(bobsi\Settings::NAME_LOGGING_FORM_FILENAME) ?: '';
            $file = sanitize_text_field($file);
            $data = [bobsi\Settings::NAME_LOGGING_FORM_FILENAME => $file];

            $result = $this->core->processAction($actionLoggingForm, $data);

            add_action(
                'admin_notices',
                function () use ($result) {
                    foreach ($result as $warn) {
                        echo '<div class="updated"><p><strong>' . bobsi\Version::$name . ':</strong> ' . $warn
                            . '.</p></div>';
                    }
                }
            );
        }
    }

    /**
     * Prevalidation settings
     *
     * @param array  $data          data from $_POST
     * @param string $setting       setting
     * @param string $prevalidation rule
     *
     * @return mixed
     */
    protected function validateSettings($setting, $prevalidation)
    {
        $value = $this->request->request->get($setting);
        $presavedSetting = null;

        switch ($prevalidation) {
            case ('strval'):
                $presavedSetting = $value ? (string)$value : '';
                break;
            case ('intval'):
                $presavedSetting = $value ? (int)$value : 0;
                break;
            case ('bool'):
                $presavedSetting = $value ? (bool)$value : false;
                break;
            case ('categories'):
                $presavedSetting = $value ? (array)$value : [];
        }

        return $presavedSetting;
    }


    /**
     * Add JS Scripts To Settings page.
     *
     * @return void
     */
    public function requiredScripts()
    {
        wp_enqueue_script(
            'bobsi_admin',
            plugin_dir_url(bobsi\Settings::$coreAssetsPath) . '/assets/js/admin.js',
            ['jquery', 'jquery-tiptip', 'woocommerce_admin']
        );
        wp_enqueue_script('bobsi_copy_button', plugins_url('/assets/js/copy-button.js', __FILE__), ['jquery']);
        wp_enqueue_style('woocommerce_admin_styles', WP_PLUGIN_URL . '/woocommerce/assets/css/admin.css');
        wp_enqueue_style('bobsi_admin_styles', plugins_url('assets/css/styles.css', __FILE__));
    }


    /**
     * Check update for plugin
     *
     * @return void
     */
    public function pluginCheckUpdate()
    {
        $databaseVersion = get_option('bobsi_db_version');
        $version = $this->core->getVersionInstance()->getVersionFromString(bobsi\Version::$version);

        if ($databaseVersion) {
            if (version_compare($databaseVersion, '2.0.7', '<')) {
                $this->pluginUpdate();
            }
            if (version_compare($databaseVersion, '2.0.12', '<')) {
                $this->feature4451Update();
            }
            if (version_compare($databaseVersion, '2.0.15', '<')) {
                update_option('bobsi_show_admin_notices', 1);
            }
            if (version_compare($databaseVersion, '2.1.1', '<')) {
                $this->updateTablesCollation();
            }
            if (version_compare($databaseVersion, '2.2.3', '<')) {
                $this->migrationForFeature5080AddNewColumnGTIN();
            }
            if (version_compare($databaseVersion, '2.4.0', '<')) {
                $this->migrationForFeature5083AddNewColumnAllowOffers();
            }
        } elseif (!$databaseVersion && $version) {
            // if $version == null, DO NOT UPDATE, see
            // Defect #7031: [Bidorbuy - WooCommerce] Fix plugin version issue

            /* First install or old plugin version < 2.0.7 */
            $this->pluginUpdate();
            $this->feature4451Update();
            $this->updateTablesCollation();
            $this->migrationForFeature5080AddNewColumnGTIN();
            $this->migrationForFeature5083AddNewColumnAllowOffers();
        }

        if ($databaseVersion !== $version) {
            update_option('bobsi_db_version', $version);
        }
    }

    /**
     * Plugin update
     *
     * @return void
     */
    protected function pluginUpdate()
    {
        $this->addAllProductsInTradefeedQueue(true);
        $query = "ALTER TABLE " . $this->wpdb->prefix . bobsi\Queries::TABLE_BOBSI_TRADEFEED
            . " ADD `images` text AFTER `image_url`";
        $this->wpdb->query($query);
    }

    protected function migrationForFeature5080AddNewColumnGTIN()
    {
        $this->addAllProductsInTradefeedQueue(true);
        $query = "ALTER TABLE " . $this->wpdb->prefix . bobsi\Queries::TABLE_BOBSI_TRADEFEED
            . " ADD `gtin` varchar(65) AFTER `code`";
        $this->wpdb->query($query);
    }

    protected function migrationForFeature5083AddNewColumnAllowOffers()
    {
        $this->addAllProductsInTradefeedQueue(true);
        $query = "ALTER TABLE " . $this->wpdb->prefix . bobsi\Queries::TABLE_BOBSI_TRADEFEED
            . " ADD `allow_offers` tinyint(1) NOT NULL DEFAULT '0' AFTER `market_price`";
        $this->wpdb->query($query);
    }


    /**
     * Delete css from header.
     *
     * @param string $href url to css
     *
     * @return mixed
     */
    public function deleteCss($href)
    {
        if (strpos($href, "menu.css") !== false) {
            return false;
        }

        return $href;
    }


    /**
     * Add checkbox on WooCommerce: Product->Attributes for new attributes
     *
     * @return void
     */
    public function woocommerceAfterAddAttributeFields()
    {
        echo "<div class='form-field'>
          <label for='bobsi_attribute_field'>
          <input name='" . static::WOOCOMMERCE_ATTRIBUTE_COLUMN . "' id='bobsi_attribute_field' type='checkbox' 
                value='1' checked> Add this attribute to product name in bidorbuy tradefeed?</label>
          <p class='description'>Enable if you want add this attribute to bidorbuy tradefeed product name.</p>
          </div>
         ";
    }

    /**
     * Add checkbox on WooCommerce: Product->Attributes on edit page(update attribute)
     *
     * @return void
     */
    public function woocommerceAfterEditAttributeFields()
    {
        $edit = absint($this->request->query->get('edit'));
        $attribute = $this->wpdb->get_row(
            "SELECT " . static::WOOCOMMERCE_ATTRIBUTE_COLUMN . " FROM " . $this->wpdb->prefix
            . "woocommerce_attribute_taxonomies WHERE attribute_id = '$edit'"
        );
        $param = static::WOOCOMMERCE_ATTRIBUTE_COLUMN;
        $flag = $attribute->$param;
        echo "
        <tr class='form-field form-required'>
            <th scope='row' valign='top'>
                <label for='bobsi_attribute_field'>Add this attribute to product name in bidorbuy tradefeed?</label>
            </th>
            <td>
                 <input name='" . static::WOOCOMMERCE_ATTRIBUTE_COLUMN
            . "' id='bobsi_attribute_field' type='checkbox' value='1' " . checked($flag, 1, 0) . ">
                 <p class='description'>Enable if you want add this attribute to bidorbuy tradefeed product name.
</p>
            </td>
        </tr>
         ";
    }

    /**
     * Add value for bobsi in woocommerce table after attribute added
     *
     * @param int $attribute_id attribute id
     *
     * @return void
     */
    public function woocommerceAttributeAdded($attribute_id)
    {
        $attributeFlag = $this->request->request->get(static::WOOCOMMERCE_ATTRIBUTE_COLUMN) ?: 0;
        $this->wpdb->update(
            $this->wpdb->prefix . 'woocommerce_attribute_taxonomies',
            [static::WOOCOMMERCE_ATTRIBUTE_COLUMN => $attributeFlag],
            ['attribute_id' => $attribute_id]
        );
    }

    /**
     * Change value for bobsi in woocommerce table after attribute updated
     *
     * @param int $attribute_id attribute id
     *
     * @return void
     */
    public function woocommerceAttributeUpdated($attribute_id)
    {
        $attributeFlag = $this->request->request->get(static::WOOCOMMERCE_ATTRIBUTE_COLUMN) ?: 0;

        $this->wpdb->update(
            $this->wpdb->prefix . 'woocommerce_attribute_taxonomies',
            [static::WOOCOMMERCE_ATTRIBUTE_COLUMN => $attributeFlag],
            ['attribute_id' => $attribute_id]
        );
    }

    /* Custom Attributes */

    /**
     * Add checkbox on WooCommerce: Product->Edit_Product->Attributes
     *
     * @param object $attribute Woocommerce attribute
     * @param  int   $attrIndex attribute array index
     *
     * @return void
     */
    public function woocommerceAfterProductAttributeSettings($attribute, $attrIndex)
    {
        // POST wp_ajax - post_id
        // GET  open edit page - post
        $postId = $this->request->request->get('post_id') ?: $this->request->query->get('post');
        $postMeta = get_post_meta($postId, '_' . static::WOOCOMMERCE_ATTRIBUTE_FIELD);
        $excludedAttributes = array_shift($postMeta) ?: [];

        $checked = !in_array($attribute->get_name(), $excludedAttributes);
        echo "
        <tr>
            <td>
                <label>
                    <input type='checkbox' class='checkbox' name='"
                    . static::WOOCOMMERCE_ATTRIBUTE_FIELD . "[$attrIndex]' 
                    value='1' " . checked($checked, 1, 0) . "> Add this attribute to product name in bidorbuy tradefeed
                </label>
            </td>
        </tr>
    ";
    }

    /**
     * Update bobsi attribute field for product
     *
     * @return void
     */
    public function wpAjaxWoocommerceSaveAttributes()
    {
        $isSaveAttributesAction = $this->request->isMethod('POST')
            && $this->request->request->get('action')
            && $this->request->request->get('action') === 'woocommerce_save_attributes'
            && is_ajax();

        if (!$isSaveAttributesAction) {
            return;
        }

        $postData = $this->request->request->get('data');
        $postId = $this->request->request->get('post_id');

        parse_str($postData, $data);
        $product_id = absint($postId);
        $exclutedAttributes = [];
        $attributes = $data['attribute_names'];
        foreach ($attributes as $key => $attribute) {
            if (!isset($data[static::WOOCOMMERCE_ATTRIBUTE_FIELD][$key])) {
                $exclutedAttributes[] = $attribute;
            }
        }
        update_post_meta($product_id, '_' . static::WOOCOMMERCE_ATTRIBUTE_FIELD, $exclutedAttributes);
    }

    /**
     * Update function for plugin.
     * Add new bobsi column for woocommerce table
     *
     * @return mixed
     */
    protected function feature4451Update()
    {
        $result = $this->wpdb->query(
            "ALTER TABLE `" . $this->wpdb->prefix . "woocommerce_attribute_taxonomies` ADD `"
            . static::WOOCOMMERCE_ATTRIBUTE_COLUMN . "` TINYINT(1) NULL DEFAULT '1' AFTER `attribute_public`"
        );

        return $result;
    }

    /**
     * Delete bobsi column in woocommerce table
     *
     * @return mixed
     */
    protected function feature4451Uninstall()
    {
        $result = $this->wpdb->query(
            "ALTER TABLE `" . $this->wpdb->prefix . "woocommerce_attribute_taxonomies` DROP `"
            . static::WOOCOMMERCE_ATTRIBUTE_COLUMN . "`"
        );

        return $result;
    }

    /**
     * Register endpoint
     *
     * @return void
     */
    public function registerEndpoint()
    {
        add_rewrite_endpoint(static::ENDPOINT_NAMESPACE, EP_ROOT);
    }


    /**
     * Endpoint controller
     *
     * @param object $query request
     *
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function initEndpoint($query)
    {
        $request = $query->get(static::ENDPOINT_NAMESPACE);
        if ($query->is_main_query() && $request) {
            $this->cancelBatcacheIfEnabled();

            $params = explode('/', $request);
            $action = sanitize_text_field($params[0]);
            $token = isset($params[1]) ? substr($params[1], 0, 32) : '';
            $token = sanitize_text_field($token);
            switch ($action) {
                case 'export':
                    $this->checkWoocommersPlugin();
                    $this->export($token);
                    break;
                case 'download':
                    if (get_option(static::SETTING_CLEAN_OUTPUT_CACHE_BEFORE_ACTION, false)) {
                        $this->cleanOutputBuffers();
                    }

                    $this->downloadTradefeed($token);
                    break;
                case 'resetaudit':
                    $this->resetAudit($token);
                    break;
                case 'version':
                    $phpInfoParam = isset($params[1]) ? sanitize_text_field($params[1]) : '';
                    $phpInfo = strpos($phpInfoParam, 'phpinfo=y') !== false;
                    $this->showVersion($token, $phpInfo);
                    break;
                case 'downloadl':
                    $this->downloadLogs($token);
                    break;
            }
        }
    }

    protected function export($token)
    {
        $exportAction = new BidorbuyStoreIntegratorExport($this);
        $ids = $this->request->request->get(bobsi\Settings::PARAM_IDS) ?: false;
        $productStatus = $this->request->request->get(bobsi\Settings::PARAM_PRODUCT_STATUS) ?: false;
        $excludededAttributes = ['Width', 'Height', 'Length'];

        delete_transient('wc_attribute_taxonomies');

        foreach (wc_get_attribute_taxonomies() as $attribute) {
            if (!$attribute->bobsi_attribute_flag) {
                $excludededAttributes[] = $attribute->attribute_name;
            }
        }

        $exportConfiguration =
            [bobsi\Settings::PARAM_IDS => $ids, bobsi\Settings::PARAM_PRODUCT_STATUS => $productStatus,
                bobsi\Tradefeed::NAME_EXCLUDED_ATTRIBUTES => $excludededAttributes,
                bobsi\Settings::PARAM_CALLBACK_GET_PRODUCTS => [$exportAction, '@Deprecated for normalized platforms'],
                bobsi\Settings::PARAM_CALLBACK_GET_BREADCRUMB => [$exportAction, 'getBreadcrumb'],
                bobsi\Settings::PARAM_CALLBACK_EXPORT_PRODUCTS => [$exportAction, 'exportProducts'],
                bobsi\Settings::PARAM_EXTENSIONS => [],
                bobsi\Settings::PARAM_CATEGORIES => $this->getExportCategoriesIds(
                    $this->core->getSettings()->getExcludeCategories()
                ),

            ];

        $plugins = get_option('active_plugins');
        foreach ($plugins as $plugin) {
            $pluginData = get_plugin_data(dirname(__FILE__) . '/../' . $plugin);
            $exportConfiguration[bobsi\Settings::PARAM_EXTENSIONS][$pluginData['Name']] =
                $pluginData['Name'] . 'Version: ' . $pluginData['Version'];
        }

        $currency = $this->core->getSettings()->getCurrency();
        $this->currencyConverter->setCurrencyIntoCookie($currency);

        $this->core->export($token, $exportConfiguration);
    }

    protected function resetAudit($token)
    {
        if (!$this->core->canTokenDownload($token)) {
            $this->core->show403Token($token);
        }
        $this->refreshAllProducts();
        $this->core->resetaudit();
    }

    protected function downloadTradefeed($token)
    {
        $exportConfiguration = [bobsi\Settings::PARAM_CATEGORIES => $this->getExportCategoriesIds(
            $this->core->getSettings()->getExcludeCategories()
        ),];
        $this->core->download($token, $exportConfiguration);
    }

    protected function showVersion($token, $phpinfo)
    {
        $this->core->showVersion($token, $phpinfo);
    }

    protected function downloadLogs($token)
    {
        $this->core->downloadl($token);
    }

    /**
     * Generate action url
     *
     * @param string $action action
     * @param string $token  token
     *
     * @return string
     */
    public function generateActionUrl($action, $token)
    {
        $siteUrl = home_url();
        $permalinkStructure = get_option('permalink_structure');
        $indexPrefix = strpos($permalinkStructure, '/index.php/') === 0;

        if ($permalinkStructure && !$indexPrefix) {
            // pretty links
            return "{$siteUrl}/" . static::ENDPOINT_NAMESPACE . "/{$action}/{$token}";
        }

        if ($permalinkStructure && $indexPrefix) {
            return "{$siteUrl}/index.php/" . static::ENDPOINT_NAMESPACE . "/{$action}/{$token}";
        }

        // Plain
        return "$siteUrl?" . static::ENDPOINT_NAMESPACE . "={$action}/{$token}";
    }

    /**
     * Show Dashboard Warning.
     *
     * @return void
     */
    public function newUrlsWarning()
    {
        $closeAdminNotice = $this->request->request->get('bobsi_close_admin_notice');
        if ($closeAdminNotice) {
            delete_option('bobsi_show_admin_notices');
            delete_transient('bobsi_show_admin_notices');

            return;
        }
        $message = '<h3><b style="color: red">bidorbuy Store Integrator warning:</b>
               to improve plugin security the export/download link structure will be changed from 
               Store Integrator 2.0.15 version and higher. 
               <b>Please ensure you have provided updated links to bidorbuy.</b></h3>';
        echo "
        <div id='bobsi_admin_warning' class='error notice'>
            $message
            <p>
            <div align='right'>
            <form method='post'>
                <input type='submit' name='bobsi_close_admin_notice' value='Close'>            
            </form> 
            </div>
            </p>
        </div>
        <script>
        jQuery(document).ready(function() {
          (function blink() { 
                jQuery('#bobsi_admin_warning').fadeOut(500).fadeIn(5000, blink); 
            })();  
        })
            
        </script>        
    ";
    }

    public function cometCacheWarning()
    {
        $cometCacheOptions = get_option('comet_cache_options');
        if (!preg_match('/\/bidorbuystoreintegrator\/\*\*/', $cometCacheOptions['exclude_uris'])) {
            $message = '<h4 style="color: red">bidorbuy Store Integrator warning:</h4>
                <p>You are using Comet Cache plugin, to get bidorbuy 
                Store Integrator working properly please add exclusion rule: 
                <a href="' . admin_url('admin.php?page=comet_cache')
                . '">Comet Cache</a> > URI exclusion > /bidorbuystoreintegrator/** > Save changes.</p>';

            echo "
                <div class='error notice'>
                    $message
                </div>
            ";
        }
    }

    /**
     * Update table collation if collation isn't utf8_unicode_ci
     *
     * @return void
     */
    protected function updateTablesCollation()
    {

        $showTableInfoSql = "SHOW TABLE STATUS WHERE name='{$this->wpdb->prefix}%s'";
        $alterTableSql = "ALTER TABLE {$this->wpdb->prefix}%s CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
        $tableNames = [bobsi\Queries::TABLE_BOBSI_TRADEFEED_AUDIT, bobsi\Queries::TABLE_BOBSI_TRADEFEED,
            bobsi\Queries::TABLE_BOBSI_TRADEFEED_TEXT];
        foreach ($tableNames as $tableName) {
            $showTableInfoQuery = sprintf($showTableInfoSql, $tableName);
            $result = $this->wpdb->get_results($showTableInfoQuery, ARRAY_A);
            $result = array_shift($result);

            if ($result['Collation'] !== 'utf8_unicode_ci') {
                $alterTableQuery = sprintf($alterTableSql, $tableName);
                $this->wpdb->query($alterTableQuery);
            }
        }
    }

    /**
     * Exit with error.
     *
     * @param string $message error message
     * @param string $type    error type
     * @param bool   $exit    flag to exit
     *
     * @return void
     */
    public function exitWithError($message, $type = 'error', $exit = 1)
    {
        $message = '<div class="' . $type . '"><p>' . $message . '</p></div>';
        if ($exit) {
            wp_die($message);
        }
        echo $message;
    }

    /**
     * Get Categories.
     *
     * @param array $args arguments
     *
     * @return array|WP_Error
     */
    public function getCategories($args = [])
    {
        $taxonomies = ['product_cat',];

        $terms = empty($args) ? get_terms($taxonomies) : get_terms($taxonomies, $args);

        if (is_object($terms) && ($terms instanceof \WP_Error)) {
            $this->core->logError('Unable to get category terms: ' . implode('. ', array_keys($terms->errors)));
            $terms = [];
        }

        return $terms;
    }

    /**
     * Get export categories ids
     *
     * @param array $ids categories ids
     *
     * @return array
     */
    public function getExportCategoriesIds($ids = [])
    {
        $uncategorized = in_array(0, $ids);

        $args = ['hide_empty' => 0];

        if (!empty($ids)) {
            $args['exclude'] = $ids;
        }

        $terms = $this->getCategories($args);

        $ids = [];
        foreach ($terms as &$term) {
            $ids[] = $term->term_id;
        }

        $terms = null;

        if (!$uncategorized) {
            $ids[] = 0;
        }

        return $ids;
    }

    /**
     * Disable BatCache for plugin's endpoints.
     * See Support #6451: WooCommerce bidorbuy Store Integrator | Error: IOException: Premature EOF
     *
     * @return void
     */
    protected function cancelBatcacheIfEnabled()
    {
        if (defined('WP_CACHE') && WP_CACHE && function_exists('batcache_cancel')) {
            batcache_cancel();
        }
    }

    /**
     * Clean all output buffers
     * Error: IOException: Premature EOF
     *
     * @return void
     */
    protected function cleanOutputBuffers()
    {
        $level = ob_get_level();
        for ($index = 0; $index < $level; $index++) {
            ob_end_clean();
        }
    }

    public function showBetaTesterBanner()
    {
        include(dirname(__FILE__) . '/templates/banner.tpl.php');
    }
}
