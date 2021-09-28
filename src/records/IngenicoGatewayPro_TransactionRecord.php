<?php
/**
 * Ingenico gateway Pro plugin for Craft CMS 3.x
 *
 * Setup and use Ingenico payments in an easy way on your Craft website/Commerce for a smooth, enhanced customer experience
 *
 * @link      https://www.infanion.com/
 * @copyright Copyright (c) 2021 Infanion
 */

namespace ipcraft\ingenicogatewaypro\records;

use ipcraft\ingenicogatewaypro\IngenicoGatewayPro;

use Craft;
use craft\db\ActiveRecord;

/**
 * IngenicoGatewayProRecord Record
 *
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 *
 * Active Record implements the [Active Record design pattern](http://en.wikipedia.org/wiki/Active_record).
 * The premise behind Active Record is that an individual [[ActiveRecord]] object is associated with a specific
 * row in a database table. The object's attributes are mapped to the columns of the corresponding table.
 * Referencing an Active Record attribute is equivalent to accessing the corresponding table column for that record.
 *
 * http://www.yiiframework.com/doc-2.0/guide-db-active-record.html
 *
 * @author    Infanion
 * @package   IngenicoGatewayPro
 * @since     1.0.0
 */
class IngenicoGatewayPro_TransactionRecord extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

     /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
     * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
     * if the table is not named after this convention.
     *
     * By convention, tables created by plugins should be prefixed with the plugin
     * name and an underscore.
     *
     * @return string the table name
     */
    public static function tableName()
    {
        return '{{%ingenicogatewaypro_ingenicogatewayprorecord}}';
    }
    public function getTableName()
    {
        return 'ingenicogatewaypro_transaction';
    }

    protected function defineAttributes()
    {
        return array(
            'transaction_id' => AttributeType::String,
            'reference' => AttributeType::String,
            'created_time' => AttributeType::String,
            'expired_time' => AttributeType::String,
            'status' => AttributeType::String,
            'currency' => AttributeType::String,
            'amount' => AttributeType::String,
            'payment_method' => AttributeType::String,
            'acceptance' => AttributeType::String,
            'brand' => AttributeType::String,
            'card_number' => AttributeType::String,
            'card_name' => AttributeType::String,
        );
    }

    // public function defineRelations()
    // {
    //     return array(
    //         'configuration' => array(static::HAS_MANY, 'IngenicoGatewayPro_ConfigurationRecord', 'transaction_id'),
    //     );
    // }
    public function defineIndexes()
    {
        return array(
            array('columns' => array('transaction_id'), 'unique' => true),
        );
    }
}
