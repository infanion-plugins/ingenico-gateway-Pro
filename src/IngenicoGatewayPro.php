<?php
/**
 * Ingenico gateway Pro plugin for Craft CMS 3.x
 *
 * Setup and use Ingenico payments in an easy way on your Craft website/Commerce for a smooth, enhanced customer experience
 *
 * @link      https://www.infanion.com/
 * @copyright Copyright (c) 2021 Infanion
 */

namespace ipcraft\ingenicogatewaypro;

use ipcraft\ingenicogatewaypro\services\IngenicoGatewayProService as IngenicoGatewayProServiceService;
use ipcraft\ingenicogatewaypro\services\AfterPaymentService as AfterPaymentServiceService;
use ipcraft\ingenicogatewaypro\services\IngenicoLogService as IngenicoLogServiceService;
use ipcraft\ingenicogatewaypro\variables\IngenicoGatewayProVariable;
use ipcraft\ingenicogatewaypro\models\Settings;
use ipcraft\ingenicogatewaypro\fields\IngenicoGatewayProField as IngenicoGatewayProFieldField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\services\Elements;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Infanion
 * @package   IngenicoGatewayPro
 * @since     1.0.0
 *
 * @property  IngenicoGatewayProServiceService $ingenicoGatewayProService
 * @property  IngenicoLogServiceService $ingenicoLogService
 * @property  AfterPaymentServiceService $afterPaymentService
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class IngenicoGatewayPro extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * IngenicoGatewayPro::$plugin
     *
     * @var IngenicoGatewayPro
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * IngenicoGatewayPro::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'ingenico-gateway-pro/default';
            }
        );

        // Register our CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                // after payment webhook url
                $event->rules['ogone-paymentresult'] = 'ingenico-gateway-pro/home/after-payment';
                // $event->rules['cpActionTrigger1'] = 'ingenico-gateway-pro/default/do-something';
                // $event->rules['ingenico-gateway-pro'] = 'ingenico-gateway-pro/transaction/get-configuration';
                $event->rules['ingenico-gateway-pro'] = 'ingenico-gateway-pro/transaction/get-configuration';
                $event->rules['ingenico-gateway-pro/configuration'] = 'ingenico-gateway-pro/transaction/fetch-configuration';
                $event->rules['ingenico-gateway-pro/test'] = 'ingenico-gateway-pro/default/test';
                $event->rules['ingenico-gateway-pro/new-configuration'] = 'ingenico-gateway-pro/configuration/new';
                $event->rules['ingenico-gateway-pro/save-configuration'] = 'ingenico-gateway-pro/configuration/save';
                $event->rules['ingenico-gateway-pro/testpayment'] = 'ingenico-gateway-pro/transaction/test-payment';
                $event->rules['ingenico-gateway-pro/update-status'] = 'ingenico-gateway-pro/configuration/update-status';
                $event->rules['ingenico-gateway-pro/update'] = 'ingenico-gateway-pro/configuration/update';
                $event->rules['ingenico-gateway-pro/remove'] = 'ingenico-gateway-pro/configuration/remove';
                $event->rules['ingenico-gateway-pro/test'] = 'ingenico-gateway-pro/default/test';
                // $event->rules['ingenico-gateway-pro/testpayment'] = 'ingenico-gateway-pro/transaction/test-payment';
            }
        );

        // Register our elements
        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            function (RegisterComponentTypesEvent $event) {
            }
        );

        // Register our fields
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = IngenicoGatewayProFieldField::class;
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('ingenicoGatewayPro', IngenicoGatewayProVariable::class);
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );
        $this->_registerPermissions();

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'ingenico-gateway-pro',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }
    // only for testing purpose
    public function getCpNavItem(): array
    {
        $ret = parent::getCpNavItem();

        $ret['label'] = Craft::t('ingenico-gateway-pro', 'Ingenico gateway pro');

            $ret['subnav']['configuration'] = [
                'label' => Craft::t('ingenico-gateway-pro', 'Manage configurations'),
                'url' => 'ingenico-gateway-pro/configuration'
            ];
            $ret['subnav']['test'] = [
                'label' => Craft::t('ingenico-gateway-pro', 'Make test payment'),
                'url' => 'ingenico-gateway-pro/test'
            ];
            
        return $ret;

        
        // $item = parent::getCpNavItem();
        // $item['badgeCount'] = 5;
        // $item['subnav'] = [
        //     'configuration' => ['label' => Craft::t('ingenico-gateway-pro', 'Manage configuration'), 'url' => 'ingenico-gateway-pro/configuration'],
        //     'test' => ['label' => Craft::t('ingenico-gateway-pro', 'Make test payment'), 'url' => 'ingenico-gateway-pro/test'],
        // ];
        // return $item;


    }
    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return \craft\base\Model|null
     */

    private function _registerPermissions()
    {
        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions[Craft::t('ingenico-gateway-pro', 'Ingenico Gateway Pro')] = [
                'manageConfiguration' => ['label' => Craft::t('ingenico-gateway-pro', 'Create, Update and Remove configuration')],
            ];
        });
    }

    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * Returns the rendered settings HTML, which will be inserted into the content
     * block on the settings page.
     *
     * @return string The rendered settings HTML
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'ingenico-gateway-pro/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
