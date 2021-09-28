<?php
/**
 * Email Templates plugin for Craft CMS 3.x
 *
 * You can build and manage your email templates used in your Craft website or Craft Commerce. Emails can be sent dynamically from your application, by using tokens 
 *
 * @link      https://www.infanion.com/
 * @copyright Copyright (c) 2021 Infanion
 */

namespace ipcraft\ingenicogatewaypro\controllers;

use ipcraft\ingenicogatewaypro\IngenicoGatewayPro;
use Craft;
use craft\web\Controller;
use Exception;
use craft\web\Request;
use craft\helpers\App;
use craft\helpers\Template;
use craft\web\View;
use craft\elements\User;
use yii\base\InvalidConfigException;
use yii\helpers\Markdown;
use yii\mail\MessageInterface;
use craft\services\Users;
use craft\elements\Entry;


/**
 * Default Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Infanion
 * @package   EmailTemplates
 * @since     1.0.0
 */
class TransactionController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    // protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================
    public function actionGetConfiguration(){
        $config_data = Craft::$app->plugins->getPlugin('ingenico-gateway-pro')->getSettings();
        return $this->redirect('ingenico-gateway-pro/configuration');

    }

    public function actionFetchConfiguration(){
        $results = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->fetchConfiguration();
        $count  = count($results);
        return $this->renderTemplate(                                                                                                                 
            'ingenico-gateway-pro/_configuration/manage_configuration', 
            [
               'results' => $results,
               'count' => $count,
            ]                                                                                                                                                                                                                                                                                  
        );

    }

    public function actionTest()
    {
        $results['currency'] = 'INR';
        return $this->renderTemplate(                                                                                                                 
            'ingenico-gateway-pro/testing/test', 
            [
                'data' => $results,
               
            ]                                                                                                                                                                                                                                                                                  
        );

    }

    public function actionTestPayment()
    {
        $data = Craft::$app->getRequest()->post();
        if( isset($data['orderid'])){
            $order_id = $data['orderid'];
        }
        if(isset($data['currency'])){
            $currency = $data['currency'];
        }
        if(isset($data['amount'])){
            $price_data = $data['amount'];
        }
        
        $url = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->beginTransaciton($order_id,$currency,$price_data,$test=true);
        return $this->redirect($url);

    }

}
