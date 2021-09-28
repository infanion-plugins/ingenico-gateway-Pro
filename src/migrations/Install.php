<?php
/**
 * Ingenico gateway Pro plugin for Craft CMS 3.x
 *
 * Setup and use Ingenico payments in an easy way on your Craft website/Commerce for a smooth, enhanced customer experience
 *
 * @link      https://www.infanion.com/
 * @copyright Copyright (c) 2021 Infanion
 */

namespace ipcraft\ingenicogatewaypro\migrations;

use ipcraft\ingenicogatewaypro\IngenicoGatewayPro;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * Ingenico gateway Pro Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Infanion
 * @package   IngenicoGatewayPro
 * @since     1.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            $this->insertDefaultData();
        }

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables()
    {
        $tablesCreated = false;

    // ingenicogatewaypro_ingenicogatewayprorecord table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%ingenicogatewaypro_ingenicogatewayprorecord}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%ingenicogatewaypro_ingenicogatewayprorecord}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),
                    'some_field' => $this->string(255)->notNull()->defaultValue(''),
                ]
            );
        }
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%ingenicogatewaypro_transactioninformation}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%ingenicogatewaypro_transactioninformation}}',
                [
                    'id' => $this->primaryKey(),
                    'amount' => $this->string()->notNull(),
                    'currency' => $this->string(),
                    'orderId' => $this->string()->notNull(),
                    'transactionStatus' => $this->string()->notNull(),
                    'payment_method' => $this->string(),
                    'acceptance' => $this->string(),
                    'brand' => $this->string(),
                    'card_name' => $this->string(),
                    'card_number' => $this->string(),
                    'error_code' => $this->string(),
                    'TransactionDate' => $this->dateTime()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%ingenicogatewaypro_configurations}}',
                [
                    'id' => $this->primaryKey(),
                    'title' => $this->string()->notNull(),
                    'pspid' => $this->string()->notNull(),
                    // 'merchantEmail' => $this->string()->notNull(),
                    'defaultCurreny' => $this->string()->notNull(),
                    // 'defaultLanguage' => $this->string()->notNull(),
                    'postredirect' => $this->string()->notNull(),
                    'configType' => $this->string()->notNull(),
                    'status' => $this->string()->notNull(),
                    'sha1_bp' => $this->string()->notNull(),
                    'sha1_ap' => $this->string()->notNull(),                    
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }
        

        return $tablesCreated;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
    // ingenicogatewaypro_ingenicogatewayprorecord table
        $this->createIndex(
            $this->db->getIndexName(
                '{{%ingenicogatewaypro_ingenicogatewayprorecord}}',
                'some_field',
                true
            ),
            '{{%ingenicogatewaypro_ingenicogatewayprorecord}}',
            'some_field',
            true
        );
        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
    // ingenicogatewaypro_ingenicogatewayprorecord table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%ingenicogatewaypro_ingenicogatewayprorecord}}', 'siteId'),
            '{{%ingenicogatewaypro_ingenicogatewayprorecord}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
    // ingenicogatewaypro_ingenicogatewayprorecord table
        $this->dropTableIfExists('{{%ingenicogatewaypro_ingenicogatewayprorecord}}');
        $this->dropTableIfExists('{{%ingenicogatewaypro_transactioninformation}}');
        $this->dropTableIfExists('{{%ingenicogatewaypro_configurations}}');
    }
}
