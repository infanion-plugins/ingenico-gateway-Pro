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
class ConfigurationController extends Controller
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
    public function actionNew(){
        $resData = [];
        $resData['configId'] =  '';
        $resData['defaultLanguage']['options'] = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->getOptions();
        $resData['defaultLanguage']['values'] = null;
        return $this->renderTemplate(                                                                                                                 
            'ingenico-gateway-pro/_configuration/add_configuration', 
            [
                'data' => $resData
            ]                                                                                                                                                                                                                                                                                  
        );
    }

    public function actionSave(){
       
        $resData = [];
        $data = Craft::$app->getRequest()->post();
        $session = Craft::$app->getSession();
        $resData['title'] = $data['title'];
        $resData['pspid'] = $data['pspid'];
        // $resData['merchantEmail'] = $data['merchantEmail'];
        $resData['defaultCurreny'] = $data['defaultCurreny'];
        $resData['configType'] = $data['configType'];
        // $resData['defaultLanguage']['options'] = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->getOptions();
        // $resData['defaultLanguage']['values'] = $data['defaultLanguage'];
        $resData['postredirect'] = $data['postredirect'];
        $resData['sha1_bp'] = $data['sha1_bp']; 
        $resData['sha1_ap'] = $data['sha1_ap'];  
        $resData['configId'] = $data['configId'];
        try {
                $result = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->saveConfiguaration();
                if(!$data['configId']){
                    $message = 'Configuration '.$data['title'].' saved successfully';
                }else{
                    $message = 'Configuration '.$data['title'].' updated successfully';
                }
                $session->setNotice($message);
            }
        catch (Exception $e){
            $session->error = $e->getMessage();
            return $this->renderTemplate(                                                                                                                 
                'ingenico-gateway-pro/_configuration/add_configuration',                                                                                                                              
                [                                                                                                                                                     
                    'data' => $resData,                                                                                                                              
                ]                                                                                                                                                     
            );
        }
        return $this->redirect('ingenico-gateway-pro/configuration');
    }

    public function actionUpdateStatus(){
        $config_id = Craft::$app->getRequest()->get();
        IngenicoGatewayPro::$plugin->ingenicoLogService->log($config_id);
        $configuration_id = $config_id['id'];
        $session = Craft::$app->getSession();
        try{
            $result = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->changeConfigurationStatus($configuration_id);
            $results = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->fetchConfiguration();
            $config = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->ConfigurationById($configuration_id);
            $session->setNotice(Craft::t('ingenico-gateway-pro', 'Configuration '.$config['title'].' status changed successfully'));
            $count  = count($results);
            return $this->redirect('ingenico-gateway-pro/configuration'); 
        }
        catch(Exception $e) {
            // $session->error = $e->getMessage();
            IngenicoGatewayPro::$plugin->ingenicoLogService->log($config_id);
        }

    }

    public function actionUpdate(){
        $data = Craft::$app->getRequest()->get();
        $id = $data['id'];
        $session = Craft::$app->getSession();
        $resData = [];
        try{
            $result = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->ConfigurationById($id);
            // $resData['defaultLanguage']['options'] = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->getOptions();
            // $resData['defaultLanguage']['values'] = $result['defaultLanguage'];
            // print_r($result);exit;
            $resData['title'] = $result['title'];
            $resData['pspid'] = $result['pspid'];
            // $resData['merchantEmail'] = $result['merchantEmail'];
            $resData['defaultCurreny'] = $result['defaultCurreny'];
            $resData['configType'] = $result['configType'];
            $resData['postredirect'] = $result['postredirect'];
            $resData['configId'] = $result['id'];
            $resData['sha1_bp'] = $result['sha1_bp']; 
            $resData['sha1_ap'] = $result['sha1_ap'];

            return $this->renderTemplate(   
                                                                                                                            // 
                'ingenico-gateway-pro/_configuration/add_configuration', 
                [
                    'data' => $resData,
                ]                                                                                                                                                      
            );


        }
        catch(Exception $e) {
        // $session->error = $e->getMessage();
        IngenicoGatewayPro::$plugin->ingenicoLogService->log($data);
        
        }

    }
    public function actionRemove(){
        $data_id = Craft::$app->getRequest()->get();
        $data = Craft::$app->getRequest()->post();
        $session = Craft::$app->getSession();
        try{
            $config = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->ConfigurationById($data_id['id']);
            $result = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->removeConfiguration();
            // print_r($session);exit;
            $session->setNotice(Craft::t('ingenico-gateway-pro', 'Configuration '.$config['title'].' removed successfully'));

            return $this->redirect('ingenico-gateway-pro/configuration');
        }
        catch(Exception $e) {
            // $session->error = $e->getMessage();
            IngenicoGatewayPro::$plugin->ingenicoLogService->log($data);
        }

    }

}
