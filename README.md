# ION-Reseller-WHMCS-Module
WHMCS v6 Module for HugeServer Resellers!
===============================

This is a plugin for WHMCS that integrates with the HugeServer ION. It provides you access to most functions of ION on admin and client portal. 

Features
-------------------------------
* Get Server Specification
* VPN Access for Clients
* Bandwidth Graph
* Bandwidth Statics rDNS Control
* IPMI Console (requires VPN connection)
* IP Address lists
* Power Control

Installation
-------------------------------
* Download or Git clone the contents of this repository
* Make sure files are placed on /WHMCS_path/modules/servers/ion/

Configuration
-------------------------------
* Login to https://ion.hugeserver.com/ 
* Go to profile page and request new API key
* Open ./modules/servers/ion/config.php and place your user id and API key splitted 
* Change "Change me" to a encryption key on the config.php 
* You can also change default access list for each client on Array $accessList.
* Go to Setup > Products/Services > Products/Services
* Select a Product.
* On the configuration page of the Product there's a tab named "Module Setting"
* Set ION as module of the Product.

Getting Started!
-------------------------------
* Go to a Client' Products/Services page 
* Choose a Product/Server that you want to assign to a HugeServer Device.
* Now you can see all of your HugeServer devices on "Server List"
* Select the appropriate server that you want to assign
* Save Changes!
* Make sure to set "Reseller User ID" if you want the client to use VPN. That would need a Reseller User Account to be created manually on ION.
* You're done!

The module is tested with WHMCS v6, Apache 2.4, PHP 5.4 with Curl and JSON.

If you have any problem using the module please contact us at support@hugeserver.com.
