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
use craft\helpers\ArrayHelper;
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
class IngenicoGatewayProService extends Component
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

    public function beginTransaciton($orderId, $currency, $price, $test=false)
    {
        $active_config = $this->getActiveConfiguration();
        if ($test === false){
            $currency = $active_config['defaultCurreny'];
        }
        $amount = $price * 100;
        // $email = $active_config['merchantEmail'];
        $language = 'en_US';
        $operation = 'SAL';
        $url_string = $this->getBaseUrl();
        // print_r($url_string);exit;
        $home_url = $url_string.'/admin/ogone-paymentresult';
        // print_r($home_url);exit;
        $exception_url = $decline_url = $catalog_url = $cancel_url = $accept_url = $home_url;
        $psp_id = $active_config['pspid'];
        $sha1_ap = $active_config['sha1_ap'];
        $sha1_bp = $active_config['sha1_bp'];
        // $sha1_ap = 'infanion$websites@2013';
        // $sha1_bp = 'infanion$websites@2013';
        $details['amount'] = $amount;
        $details['currency'] = $currency;
        $details['orderID'] = $orderId;
        IngenicoGatewayPro::$plugin->afterPaymentService->beforePayment($details);

        $sha_sign = sha1('ACCEPTURL='.$accept_url.$sha1_bp.'AMOUNT='.$amount.$sha1_bp.'CANCELURL='.$cancel_url.$sha1_bp.'CATALOGURL='.$catalog_url.$sha1_bp.'CURRENCY='.$currency.$sha1_bp.'DECLINEURL='.$decline_url.$sha1_bp.'EXCEPTIONURL='.$exception_url.$sha1_bp.'HOMEURL='.$home_url.$sha1_bp.'LANGUAGE='.$language.$sha1_bp.'OPERATION='.$operation.$sha1_bp.'ORDERID='.$orderId.$sha1_bp.'PSPID='.$psp_id.$sha1_bp);
        $urlParameters ='ACCEPTURL='.$accept_url.'&'.'AMOUNT='.$amount . '&' .'CURRENCY='.$currency.'&'.'CANCELURL='.$cancel_url.'&'.'CATALOGURL='.$catalog_url.'&'.'DECLINEURL='.$decline_url.'&'.'EXCEPTIONURL='.$exception_url.'&'.'HOMEURL='.$home_url.'&'.'LANGUAGE='.$language.'&'.'OPERATION='.$operation.'&'.'ORDERID='.$orderId.'&'.'PSPID='.$psp_id.'&'.'SHASIGN='.$sha_sign;
        // print_r($url);exit://secure.ogone.com/ncol/test/orderstandard.asp?$url
        $url = 'https://secure.ogone.com/ncol/test/orderstandard.asp?'.$urlParameters;

        return $url;
    }

    public function saveConfiguaration()
    {

        $now = Db::prepareDateForDb(new \DateTime());
        $data = Craft::$app->getRequest()->post();
        $title = trim($data['title']);
        $pspid = trim($data['pspid']);
        // $merchantEmail = trim($data['merchantEmail']);
        $defaultCurreny = $data['defaultCurreny'];
        // $defaultLanguage = $data['defaultLanguage'] ?? 'en_Us';
        $configType = trim($data['configType']);
        $postredirect = trim($data['postredirect']);
        $configId = $data['configId'];
        $sha1_bp = trim($data['sha1_bp']);
        $sha1_ap = trim($data['sha1_ap']);
        $now = Db::prepareDateForDb(new \DateTime());

        $active_config =$this->getActiveConfiguration();
        if($active_config === null){
            $status = 1;
        }else
        {
            $status = 0;
        }

        if( $configId){
            Craft::$app->getDb()->createCommand()
                ->update('{{%ingenicogatewaypro_configurations}}', [
                    'title' => $title, 
                    'pspid' => $pspid,
                    // 'merchantEmail' => $merchantEmail,
                    'defaultCurreny' => $defaultCurreny,
                    // 'defaultLanguage' => $defaultLanguage,
                    'configType' => $configType,
                    'postredirect' => $postredirect,
                    'sha1_bp' => $sha1_bp,
                    'sha1_ap' => $sha1_ap,
                    'dateUpdated' => $now],
                    ['in', 'id', $configId])
                ->execute();
        }else{
            Db::insert('ingenicogatewaypro_configurations', [
                'title' => $title, 
                'pspid' => $pspid,
                // 'merchantEmail' => $merchantEmail,
                'defaultCurreny' => $defaultCurreny,
                // 'defaultLanguage' => $defaultLanguage,
                'configType' => $configType,
                'postredirect' => $postredirect,
                'status' => $status,
                'sha1_bp' => $sha1_bp,
                'sha1_ap' => $sha1_ap,
                'dateCreated' => $now,
                'dateUpdated' => $now,
            ], false);
        }
        return true;



    }


    public function fetchConfiguration(){
        $results = (new \craft\db\Query())
            ->select(['id','title', 'pspid', 'configType','defaultCurreny','postredirect','status','sha1_bp','sha1_ap'])
            ->from(['{{%ingenicogatewaypro_configurations}}'])
            ->orderBy(['title' => SORT_ASC])
            ->all();
        return $results;

    }

    public function changeConfigurationStatus($id){
        $results = (new \craft\db\Query())
            ->select(['status'])
            ->from(['{{%ingenicogatewaypro_configurations}}'])
            ->where(['id' =>  $id])
            ->all();
        $present_status = $results[0]['status'];
        $set_status = '';
        if($present_status === '1'){
            $set_status = 0;
        }
        else{
            $set_status = 1;
        }
        Craft::$app->getDb()->createCommand()
        ->update('{{%ingenicogatewaypro_configurations}}', 
            [
                'status' => $set_status, 
            ],
                ['in', 'id', $id
            ])
        ->execute();
        Craft::$app->getDb()->createCommand()
        ->update('{{%ingenicogatewaypro_configurations}}', 
            [
                'status' => 0, 
            ],
                ['not in','id', $id
            ])
        ->execute();
       
    
    }

    public function ConfigurationById($id){
        $results = (new \craft\db\Query())
            ->select(['id','title', 'pspid','defaultCurreny','configType', 'postredirect','status','sha1_bp','sha1_ap'])
            ->from(['{{%ingenicogatewaypro_configurations}}'])
            ->where(['id' => $id])
            ->one();
        return $results;
        
    } 
    public function removeConfiguration(){
        $data = Craft::$app->getRequest()->get();
        if(isset($data['id'])) {
            $id = $data['id'];
            $dbobject = new Database();
            $connection = $dbobject->getDBConnection();
            $connection->open();
            $response = $connection->createCommand()->delete('ingenicogatewaypro_configurations', 'id = :id', [':id' => $id ])->execute();
            return $response;
        }

    }

    public function getOptions(){
        $sites = Craft::$app->sites->getAllSites();
        if ($sites) {
            $options = ArrayHelper::map($sites, 'id', 'name');
        } else {
            $options = [];
        }
        // array_unshift($options, "Choose a language...");
        return $options;

    }

    public function getActiveConfiguration(){
        $results = (new \craft\db\Query())
            ->select(['id','title', 'pspid','defaultCurreny', 'configType','postredirect','status','sha1_bp','sha1_ap'])
            ->from(['{{%ingenicogatewaypro_configurations}}'])
            ->where(['status' => 1])
            ->one();
        return $results;

    }


    public function getBaseUrl(){

        $protocol = 'http://';
        if (isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'],'on') === 0 || $_SERVER['HTTPS'] == 1)) {
        $protocol = 'https://';
        
        } else if ( ! empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://';
        
        } else if (isset($_SERVER['HTTP_CF_VISITOR'])) {
        $cloudFlareVisitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
        
        if ($cloudFlareVisitor->scheme == 'https') {
        $protocol = 'https://';
        }
        } else {
        $protocol = 'http://';
        }
        
        return $protocol.$_SERVER['SERVER_NAME'];
        
        }

}
?>