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
 * IngenicoGatewayProModel Model
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
class TransactionModel extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some model attribute
     *
     * @var string
     */
    public $transaction_id = '';
    public $order_id = '';
    public $amount;
    public $currency = '';
    public $payment_method = '';
    public $status = '';
    public $acceptance = '';
    public $brand = '';
    public $card_number = '';
    public $card_name = '';
    public $created_time = '';
    public $expired_time = '';

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
            ['transaction_id', 'string'],
            ['order_id', 'string'],
            ['amount', 'string'],
            ['currency', 'string', 'default','value' => 'Some Default'],
            ['payment_method', 'default', 'value' => 'Some Default'],
            ['status', 'string', 'default', 'value' => 'Some Default'],
            ['acceptance', 'string', 'default', 'value' => 'Some Default'],
            ['brand', 'string', 'default', 'value' => 'Some Default'],
            ['card_number', 'string', 'default', 'value' => 'Some Default'],
            ['card_name', 'string', 'default', 'value' => 'Some Default'],
            ['created_time', 'timeStamp'],
            ['expired_time', 'timeStamp'],
        ];
    }
}
