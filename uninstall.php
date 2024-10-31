<?php
/*
* If uninstall/delete not called from WordPress then exit
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

// Delete option from options table
delete_option('neexa_ai_agents_configs');
// Delete any other options, custom tables/data, files
