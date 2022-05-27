# 360 Monitoring Provisioning Module for WHMCS Version 1.4 #

## Installation ##

- Extract the contents of the release zip file recursively in your WHMCS root folder. The module will be extracted to /modules/servers/p360monitoring and other folders won't be touched
- The module is now installed and can be used to configure new products
- Don't forget to remove the zip file afterwards

## Example Configuration as Product ##

- Login to your WHMCS instance as an admin user
- Go to Configuration -> Sytem-Settings -> Products/Services
  - Create a new product group e.g. "Monitoring" with a nice headline like "Everything you need to keep your servers and websites up and running!" and "Standard Cart" template
  - Go back to Sytem-Settings -> Products/Services and create a new product called "360 Monitoring Pro" of type "Other" and select the just created product group there. Choose "360 Monitoring Provisioning Module" as the module for automation. Keep the "Create as Hidden" switch set to "On" and click "Continue"
  - Switch to the tab "Module Settings" and fill the fields "Plesk KA Username" and "Plesk KA Password" with the credentials for the Plesk Partner API 3.0
  - Leave the fields "Servers" and "Websites" empty (don't use "0") for a configurable product or fill both of them with fixed values. Either both or none have to be filled out
  - Check "Automatically setup the product as soon as an order is placed" if you want a good user experience or any other option that matches your existing process 
  - Switch to the tab "Pricing" and select the payment type, e.g. "Recurring" and enable the "One Time/Monthly" option for selected currencies with the price you like to have as a "base fee". For a pay-as-you-grow product leave this at 0.00 and otherwise set this as the monthly fee 
  - Click on "Save Changes"
- Go to Configuration -> Sytem-Settings -> Configurable Options (only if you want to have a pay-as-you-grow product)
  - Create a new group e.g. "Monitoring Options" and select the just created product in the "Assigned Products" list
  - Click on "Save Changes"
  - Add a new configurable option named "Servers". For that enter "Servers" to the field "Option Name" AND "Add Option" (both fields) choose option type "Quantity" and click on "Save Changes"
  - After saving the options for "Minimum Quantity Required" and "Maximum Allowed" appear and can be set accordingly. 
  - Set the price that will be charged for every option value on top of the base price defined in the product.
  - Don't forget to "Save Changes" and close the window
  - Do the same for another configurable option named "Websites"
  - IMPORTANT: There must only be two options, first "Servers" and second "Websites" in this order
  - Click on "Save Changes"
- Go back to Configuration -> Sytem-Settings -> Products/Services
  - Edit the newly created product by clicking on the edit icon on the right
  - If you want a nice description with a product image for the product you can edit the "Product Description" and refer to the logo that has been delivered within the module's zip archive e.g.  
    `Keep track of the performance and availability with our professional`
    `server and website monitoring service <br/>`
    `<img height="50" src="./modules/servers/p360monitoring/360monitoring.png">`
  - Remove the check from "Hidden" to enable customers to buy the product
  - Click on "Save Changes"

## Example Configuration as Addon ##

- Login to your WHMCS instance as an admin user
- Go to Configuration -> Sytem-Settings -> Products Addons
  - Create a new product addon e.g. "360 Monitoring Addon" with a nice description like "Keep track of your servers performance and website uptime with our easy to use monitoring platform"
  - Optionally you can add a nice product image that has been delivered within the module's zip archive e.g.  
    `Keep track of your servers performance and website uptime with our easy`
    `to use monitoring platform <br/>`
    `<img height="50" src="./modules/servers/p360monitoring/360monitoring.png">`
  - Check "Show addon during initial product order process" if you want the addon to be ordered e.g. with a virtual server in the initial order process
  - Select the appropriate "Welcome email" if you want your customers to receive a separate email for the addon after ordering. This is normally the "Other Product/Service Welcome Email"
  - Switch to the tab "Pricing" and select the payment type, e.g. "Recurring" and enable the "One Time/Monthly" option for selected currencies with the price you like to have as the monthly fee. The fixed amount of servers and websites will be set in the next step
  - Switch to the tab "Module Settings" and choose "Other" for the product type together with "360 Monitoring Provisioning Module" as the module name
  - Fill the fields "Plesk KA Username" and "Plesk KA Password" with the credentials for the Plesk Partner API 3.0
  - Set the fields "Servers" and "Websites" to the fixed values that apply to this addon. You have to fill both fields
  - Check "Automatically setup the product as soon as an order is placed" if you want a good user experience or any other option that matches your existing process 
  - Switch to the tab "Applicable Products" and select all the products where this addon will be available for 
  - Click on "Save Changes"

## Example E-Mail Templates ##

- The 360 Monitoring license can be activated through the client area or alternatively by sending the activation link in the "New Product Information", which by default is the "Other Product/Service Welcome Email". To do so:
  - Go to Configuration -> Sytem-Settings - Email Templates
  - Edit the "Other Product/Service Welcome Email" in the "Product/Service Messags" group
  - Add the placeholder `{$service_custom_field_activationlink}` to the template, e.g.  
    `If not already done please activate the product here:`
    `{$service_custom_field_activationlink}`
  - Click on "Save Changes"

## Troubleshooting ##

In case of problems pleae have a look at the "Module Log" by visiting Configuration -> System Logs and selecting "Module Log" on the left sidebar.

## Minimum Requirements ##

The 360 Monitoring Provisioning Module has been tested with WHMCS versions 7.8 and higher.

For the latest WHMCS minimum system requirements, please refer to
https://docs.whmcs.com/System_Requirements

## Copyright ##

Copyright 2022 [Plesk International GmbH](https://www.plesk.com)
