# Ingenico gateway Pro plugin for Craft CMS 3.x

This plugin comes with an interface in which you can create and manage your Ingenico configurations.


<img width="500" src="resources/img/Screenshot 1 - Manage configuration.png">
<img width="500" src="resources/img/Screenshot 2 - Add new configuration.png">
<img width="500" src="resources/img/Screenshot 3 - Make test payment.png">

## Features

This module comes with the following features:

1. Simple and secure way to configure and process your payments
2. Automatically fetch the configured payment providers from your Ingenico account
3. You can set default currency to be displayed on the payment page
4. You can configure and manage multiple Ingenico accounts for your site
5. Redirect visitors to a target landing page after a successful payment
6. Define admin and user permissions



## Requirements

This plugin requires Craft CMS 3.5.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Composer to load the plugin:

        composer require ip-craft/ingenico-gateway-pro

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Ingenico gateway Pro.

## Configuring Ingenico gateway Pro

   After installation, you can add your Ingenico (Ogone) configurations in the control panel located at:
   /admin/ingenico-gateway-pro/configurations

<img width="500" src="resources/img/Screenshot 1 - Manage configuration.png">

## Steps to use the plugin after installing:

1. Manage configurations

   Once you install the plugin you will receive the ‘Manage Ingenico configuration’ screen in which you can manage all your Ingenico configurations. To create new configurations, simply click on the ‘+ New configuration’ button. 

<img width="500" src="resources/img/Screenshot 1 - Manage configuration.png">

2. Create configurations

   This is the create configuration screen, here you can add the required Ingenico account details like PSPID, Default currency, Post payment redirection, sha_ap and sha_bp. 

<img width="500" src="resources/img/Screenshot 2 - Add new configuration.png">

3. Activate/Deactivate Configurations

   From the ‘Manage Ingenico configuration’ page you can activate and deactivate the required configuration by clicking the ‘Activate’ or ‘Deactivate’ in the Activate/Deactivate column. Only one configuration can be active at a time.  

<img width="500" src="resources/img/Screenshot 1 - Manage configuration.png">

4. In order to test if your configuration is working properly, you can use the test payment functionality. Follow the next steps to achieve this goal:
- Click on the ‘Make test payment’ sub menu from the left navigation bar and fill in the dummy Order Id and Price fields
- Click on ‘Make payment’, this will redirect you to the Ingenico payment screen 
- Select the required payment provider, add the payment details and confirm the payment
- After confirmation, you will be redirected to the defined post payment redirection page 
 

<img width="500" src="resources/img/Screenshot 3 - Make test payment.png">


5. User permissions

   In this section you can define the required permissions necessary for a user to have access.

<img width="500" src="resources/img/Screenshot 4 - Permissions.png">



## How to use (Programatically):

1. Payment request
Add the use statement to your class - use ipcraft\ingenicogatewaypro\IngenicoGatewayPro;

Access beginTransaction function and pass values Template order id, currency, price. As shown below -

$ingenicoService = IngenicoGatewayPro::$plugin->ingenicoGatewayProService;<br>
$ingenicoService->beginTransaciton($orderId, $currency, $price);

<img width="500" src="resources/img/Screenshot 5 - how to use.png">


2. Post payment handle transaction

- Once payment is done will send all the payment details trough the post payment redirection url // post-payment-redirection-url?orderID=XXXXXXXXXXX&currency=EUR&amount=XXXX&PM=XXXXXXXX&ACCEPTANCE=XXXXX&STATUS=X&CARDNO=XXXXXXXXXXXXXXXX&ED=XXXX&CN=XXXX&PAYID=XXXXXXXXXXX&NCERROR=X&BRAND=XXXXX&ECI=XX&SHASIGN=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

- You can get all the details as shown bellow

$data = $_REQUEST;

$orderId = $data['orderID']; <br>
$currency = $data['currency']; <br>
$status = $data['STATUS']; <br>
$cardNumber = $data['CARDNO']; <br>
$paymentId = $data['PAYID']; <br>
$brand = $data['BRAND']; <br>
$error = $data['NCERROR']; <br>



## Support

Get in touch with us via the mail [Ingenico gateway Pro Support mail](mailto:support-craftplugins@infanion.com) or by [creating a Github issue](https://github.com/infanion-plugins/ingenico-gateway-Pro/issues)


## Roadmap

* Recurring payments
* Complete and partial refunds
* Webhooks

Brought to you by [Infanion](https://www.infanion.com/)  
