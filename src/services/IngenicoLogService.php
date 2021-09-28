<?php
/**
 * Ingenico gateway Pro plugin for Craft CMS 3.x
 *
 * Setup and use Ingenico payments in an easy way on your Craft website/Commerce for a smooth, enhanced customer experience
 *
 * @link      https://www.infanion.com/
 * @copyright Copyright (c) 2021 Infanion
 */

namespace ipcraft\ingenicogatewaypro\services;

use ipcraft\ingenicogatewaypro\IngenicoGatewayPro;

use Craft;
use craft\base\Component;
use craft\helpers\Db;
use ipcraft\ingenicogatewaypro\utilities\Database;
/**
 * IngenicoGatewayProService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Infanion
 * @package   IngenicoGatewayPro
 * @since     1.0.0
 */
class IngenicoLogService extends Component
{
    // Public Methods
    // =========================================================================
    public $logFileName = 'Ingenicogatewayprolog';
    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     IngenicoGatewayPro::$plugin->logService->logService()
     *
     * @return mixed
     */
    public function log($log)
    {
        $file = Craft::getAlias('@storage/logs/'.$this->logFileName.'.log');
        $log = json_encode([ 'date'=> date('Y-m-d H:i:s'), 'log'=> $log], true)."\n";
        \craft\helpers\FileHelper::writeToFile($file, $log, ['append' => true]);
    }

}
?>