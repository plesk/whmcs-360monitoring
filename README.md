# 360 Monitoring WHMCS provisioning module

## Description

360 Monitoring WHMCS provisioning module gives the ability to sell the site and server monitoring solution.

## Requirements

The minimum required PHP version is 7.4.

For the latest WHMCS minimum system requirements, please refer to <https://docs.whmcs.com/System_Requirements>.

## Installation

* Download the latest zip archive from the [releases page](https://github.com/plesk/whmcs-360monitoring/releases)
* Extract the contents of the zip file in the WHMCS root directory; the module will be extracted to `/modules/servers/p360monitoring`
* Remove the zip file afterward

## Server setup

The module uses the Plesk Key Administrator Partner API 3.0. To configure the module, go to Products/Services -> Servers and add a new server with the credentials:

![Add Server](./docs/server.png)

## Product setup

After server setup is done, go to Products/Services and add a new product group e.g. `Monitoring`. Then create a new product:

![Add Product](./docs/product.png)

and configure it further in the Module Settings:

![Module Settings](./docs/module-settings.png)

The `Additional servers` and `Additional websites` fields are optional and can be left empty.

To allow the customer to choose additional resources (pay-as-you-grow model), go to the Configurable Options, and add a new group with two options inside, named `Servers` and `Websites` respectively and assign them to the relevant products:

![Configurable Options](./docs/configurable-options.png)

## Email template customization

The 360 Monitoring license can be activated through the client area or by sending the activation link in the "New Product Information", which by default is the "Other Product/Service Welcome Email". To do so:

* Go to Configuration -> Email Templates
* Edit the "Other Product/Service Welcome Email" in the "Product/Service Messages" group
* Add the placeholder `{$service_custom_field_activationlink}` to the template, e.g. `If not already done please activate the product here:{$service_custom_field_activationlink}`

## Troubleshooting

In case of problems look at the System Logs -> Module Log.

## Copyright

Copyright 2022. [Plesk International GmbH](https://www.plesk.com). All rights reserved.
