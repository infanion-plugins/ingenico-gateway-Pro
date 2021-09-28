<?php
/**
 * Ingenico gateway Pro plugin for Craft CMS 3.x
 *
 * Setup and use Ingenico payments in an easy way on your Craft website/Commerce for a smooth, enhanced customer experience
 *
 * @link      https://www.infanion.com/
 * @copyright Copyright (c) 2021 Infanion
 */

namespace ipcraft\ingenicogatewaypro\controllers;

use ipcraft\ingenicogatewaypro\IngenicoGatewayPro;

use Craft;
use craft\web\Controller;

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
 * @package   IngenicoGatewayPro
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/ingenico-gateway-pro/default
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $result = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->getActiveConfiguration();
        return $this->renderTemplate(                                                                                                                 
            'ingenico-gateway-pro/testing/test', 
            [
               'data' => $result,
            ]                                                                                                                                                                                                                                                                                  
        );
    }
    public function actionTest()
    {
        $result = IngenicoGatewayPro::$plugin->ingenicoGatewayProService->getActiveConfiguration();
        return $this->renderTemplate(                                                                                                                 
            'ingenico-gateway-pro/testing/test', 
            [
                'data' => $result,
            ]                                                                                                                                                                                                                                                                                  
        );

    }

    /**
     * Handle a request going to our plugin's actionDoSomething URL,
     * e.g.: actions/ingenico-gateway-pro/default/do-something
     *
     * @return mixed
     */
    public function actionDoSomething()
    {
        $result = 'Welcome to the DefaultController actionDoSomething() method';

        return $result;
    }
}
