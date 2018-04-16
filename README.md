# OroInfinitePayBundle

OroInfinitePayBundle adds [Infinite Pay](https://www.infinitepay.de/) [integration](https://github.com/oroinc/platform/tree/master/src/Oro/Bundle/IntegrationBundle) into Oro applications.
 
The bundle helps admin users to enable and configure Infinite Pay [payment methods](https://github.com/oroinc/orocommerce/tree/master/src/Oro/Bundle/PaymentBundle) for customer orders, and therefore allows customers to pay for orders with Invoices attested by Infinite Pay service.

Table of Contents
-----------------
- [Overview](#overview)
- [General information](./Resources/doc/general-information.md)


## Overview

OroInfinitePayBundle implements the most relevant payment method on the German market: The invoice. The infinite Pay integration takes care of invoices being paid, by covering the risk of accounting losses. This means, once an invoice is set for payment, Infinite Pay reviews the buying institution and decides if the client is eligible for invoice payment. If not, the client needs to select a different payment method, if eligible Infinite Pay takes care of pursuing the payment default of accounting loss. In short: They secure the invoice payment and ensure the merchant receives his money (payment default security).
