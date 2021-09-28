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
use Exception;
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
class AfterPaymentService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     IngenicoGatewayPro::$plugin->ingenicoGatewayProService->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (IngenicoGatewayPro::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }

    public function afterPayment($data){
        $after_url_details = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->getActiveConfiguration();
            if(isset($after_url_details['postredirect'])){
                $after_url = $after_url_details['postredirect'];
            }
        try{
            $order_id = $data['orderID'];
            $status = $data['STATUS'];
            $details['transaction_id'] = $data['PAYID'];
            $details['currency'] = $data['currency'];
            $details['amount'] = $data['amount'];
            $details['payment_method'] = $data['PM'];
            $details['acceptance'] = $data['ACCEPTANCE'];
            $details['brand'] = $data['BRAND'];
            $details['card_name'] = $data['CN'];
            $details['card_number'] = $data['CARDNO'];
            $details['error_code'] = $data['NCERROR'];


            switch ($status) {
                case 0: // Invalid or Incomplete or failed
                case 1: // Cancelled
                    $status = 2;
                    // no break
                case 2: // Authorization failed
                case 52:
                case 93: // Authorization failed
                // Redirect to failure page
                break;
                case 4: // Order stored
                case 41:
                case 5: // Authorized (payment status pending)
                case 51:
                case 9: // Requested (success)
                case 91:
                    $status = 3;
                    // Redirect to success page
                    break;
                default:

            }
            $details['status'] = $status;
            // call update transaction function here
            $update = $this->updateOrderDetails($details, $order_id);
            return  $after_url;
        }
        
        catch(\Exception $e ) {
            return  $after_url;
        } 
    
    }

    public function updateOrderDetails($details, $order_id){
        $now = Db::prepareDateForDb(new \DateTime());
        Craft::$app->getDb()->createCommand()
            ->update('{{%ingenicogatewaypro_transactioninformation}}', [
                        // 'amount' => $details['amount'],
                        // 'currency' => $details['currency'],
                        // 'orderId' => $order_id,
                        'payment_method' => $details['payment_method'],
                        'acceptance' => $details['acceptance'],
                        'brand' => $details['brand'],
                        'card_name' => $details['card_name'],
                        'card_number' => $details['card_number'],
                        'error_code' => $details['error_code'],
                        'transactionStatus' => $details['status'], 
                        'TransactionDate' => $now,
                        'dateUpdated' => $now,
                    ],
                        ['in','orderId', $order_id])
            ->execute();
    }

    public function beforePayment($data){
        $order_id = $data['orderID'];
        $status = 0;
        $details['currency'] = $data['currency'];
        $details['amount'] = $data['amount'];

        $details['status'] = $status;
        // call update transaction function here
        $create = $this->createOrderDetails($details, $order_id);
        if($create) {
          return 'sucessss';
        }
    }
    public function createOrderDetails($details, $order_id){
        $now = Db::prepareDateForDb(new \DateTime());
        Db::insert('ingenicogatewaypro_transactioninformation', [
            'amount' => $details['amount'],
            'currency' => $details['currency'],
            'orderId' => $order_id,
            'transactionStatus' => $details['status'],
            // 'payment_method' => $details['payment_method'],
            // 'acceptance' => $details['acceptance'],
            // 'brand' => $details['brand'],
            // 'card_name' => $details['card_name'],
            // 'card_number' => $details['card_number'],
            // 'error_code' => $details['error_code'],
            'dateCreated' => $now,
            'dateUpdated' => $now, 
            'TransactionDate' => $now
        ], false);
    }

}
