> :warning: **BEWARE!**
> This repository has been forked from the [official repository](https://github.com/Sylius/CustomerReorderPlugin) because it has been deprecated and will not be maintained or evolved by the Sylius Team. 

<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/images/sylius_logo.svg" />
    </a>
<br>+<br>
    <a href="https://www.webgriffe.com" target="_blank">
        <img src="https://sylius.com/wp-content/uploads/2018/08/webgriffe_logo.png" height="80" />
    </a>
</p>

<h1 align="center">Customer Reorder Plugin</h1>

<p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-official-sylius-plugin.png" width="200"></a></p>

<p align="center">This plugin allows customers to reorder a previously placed order.</p>

![Screenshot showing the customer's orders page with reorder buttons](docs/screenshot.png)

## Business value

The plugin allows Customer to reorder any Order that has already been placed. Once a Reorder button is clicked, a new cart 
filled with items taken from a previously placed order is created. If for some reason Reorder can't be fulfilled completely,
the Customer is informed about every circumstance that have affected the Order (i. e. promotion being no longer available
or differences in item's prices).

Once the Reorder process is completed, the newly created Order is listed in the history just like any other Orders.

## Installation

1. Run `composer require webgriffe/sylius-customer-reorder-plugin`: it's normal that the "cache:clear" command, that is executed automatically at the end, fails because you have to do the next steps.

2. If they have not been added automatically, you have to add these bundles to `config/bundles.php` file:

    ```php
    Sylius\CustomerReorderPlugin\SyliusCustomerReorderPlugin::class => ['all' => true],
    ```

3. Add the plugin's configs by creating the file `config/packages/webriffe_sylius_customer_reorder_plugin.yaml` with the following content:

    ```yaml
    imports:
        - { resource: "@SyliusCustomerReorderPlugin/config/config.yaml" }
    ```

4. Add the plugin's routes by creating the file `config/routes/webgriffe_sylius_customer_reorder_plugin.yaml` with the following content:

    ```yaml   
    sylius_customer_reorder_plugin:
        resource: "@SyliusCustomerReorderPlugin/config/app_routing.yaml"
    ```

5. Clear cache:

    ```bash
    bin/console cache:clear
    ```

## Extension points

Customer Reorder plugin is based on two processes:

* reorder processing
* reorder eligibility checking

They are both based on Symfony's compiler passes and configured in `services.xml` file.

ReorderProcessing and EligibilityChecking are independent processes - once a Reorder
is created using Processors (services tagged as `sylius_customer_reorder_plugin.reorder_processor`), the created
entity is passed to Eligibility Checkers (services tagged as `sylius_customer_reorder_plugin.eligibility_checker`).

Hence, both processes can be extended separately by adding services that implement `ReorderEligibilityChecker`
and are tagged as `sylius_customer_reorder_plugin.eligibility_checker` or implement `ReorderProcessor` and are tagged as
`sylius_customer_reorder_plugin.reorder_processor`.

Both `Reorder` button layout and action performed on clicking it are defined in
`reorder.html.twig` template which is declared in `config.yml` file.

What's more, since Order is a Resource, major part of its configuration is placed
in `*.yml` files. Without using the plugin, Order had `Show` and `Pay` actions.
Adding `Reorder` action required extending order-related behaviours in `config.yml` file.

You can read much more about Resources here:
<http://docs.sylius.com/en/1.2/components_and_bundles/bundles/SyliusResourceBundle/index.html> 


## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for contribution guidelines.
