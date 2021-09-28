<?php
/**
 * Ingenico gateway Pro plugin for Craft CMS 3.x
 *
 * Setup and use Ingenico payments in an easy way on your Craft website/Commerce for a smooth, enhanced customer experience
 *
 * @link      https://www.infanion.com/
 * @copyright Copyright (c) 2021 Infanion
 */

namespace ipcraft\ingenicogatewaypro\models;

use ipcraft\ingenicogatewaypro\IngenicoGatewayPro;

use Craft;
use craft\base\Model;

/**
 * IngenicoGatewayPro Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Infanion
 * @package   IngenicoGatewayPro
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $someAttribute = 'Some Default';
    public $title = '';
    public $pspid = '';
    public $merchantemail = '';
    public $defaultcurreny = '';
    public $defaultlang = '';
    public $url = '';
    public $postredirect = '';
    public $sha1 = '';


    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['someAttribute', 'string'],
            ['someAttribute', 'default', 'value' => 'Some Default'],
            ['title', 'string'],
            ['title', 'default', 'value' => ''],
            ['pspid', 'string'],
            ['pspid', 'default', 'value' => ''],
            ['merchantemail', 'string'],
            ['merchantemail', 'default', 'value' => ''],
            ['defaultcurreny', 'string'],
            ['defaultcurreny', 'default', 'value' => ''],
            ['defaultlang', 'string'],
            ['defaultlang', 'default', 'value' => ''],
            ['url', 'string'],
            ['url', 'default', 'value' => ''],
            ['postredirect', 'string'],
            ['postredirect', 'default', 'value' => ''],
            ['sha1', 'string'],
            ['sha1', 'default', 'value' => ''],
        ];
    }
}
