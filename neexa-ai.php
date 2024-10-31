<?php
/*
Plugin Name: Neexa AI
Plugin URI:  https://github.com/Campaignity/neexa-ai-wordpress-plugin
Description: This plugin seamlessly integrates Neexa AI's 24/7 AI Powered Sales Assistants onto any WordPress site
Version: 1.0
Author: Campaignity's Neexa.AI
Author URI: https://neexa.ai
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly 

define('CAM_NEEXAAI_VERSION', '1.0');
define('CAM_NEEXAAI_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Register activation hook
register_activation_hook(__FILE__, 'cam_neexai_activate');
add_action('admin_init', 'cam_neexai_activation_redirect');
function cam_neexai_activate()
{
    add_option('cam_neexai_do_activation_redirect', true);
}
function cam_neexai_activation_redirect()
{
    // Check if the user has the capability to activate plugins
    if (current_user_can('activate_plugins')) {

        if (get_option('cam_neexai_do_activation_redirect', false)) {
            delete_option('cam_neexai_do_activation_redirect');

            // Redirect to the plugin's home page after activation
            wp_redirect(admin_url('admin.php?page=neexa-ai-settings'));
            exit;
        }
    }
}

add_action('admin_enqueue_scripts', 'cam_neexai_enqueue_admin_plugin_styles');
function cam_neexai_enqueue_admin_plugin_styles()
{
    wp_enqueue_style('cam_neexai_admin_style', plugins_url('assets/admin-style.css', __FILE__, [], CAM_NEEXAAI_VERSION));
}

add_action('wp_enqueue_scripts', 'cam_neexai_add_header_script');
function cam_neexai_add_header_script()
{
    $neexa_ai_agents_configs = get_option('neexa_ai_agents_configs');
    if (!empty($neexa_ai_agents_configs["config_agent_id"])) {

        wp_enqueue_script(
            "cam_neexai_agent_id",
            "https://chat-widget.neexa.ai/main.js",
            [],
            time(),
            [
                "in_footer" => false
            ]
        );

        wp_add_inline_script(
            "cam_neexai_agent_id",
            'var neexa_xgmx_cc_wpq_ms = "' . esc_html($neexa_ai_agents_configs["config_agent_id"]) . '";',
            "before"
        );
    }
}



// create custom plugin settings menu
add_action('admin_menu', 'cam_neexai_create_menu');
function cam_neexai_create_menu()
{
    //create new top-level menu
    add_menu_page(
        'Neexa AI Assistants Configuration',
        'Neexa AI',
        'manage_options',
        'neexa-ai-settings',
        'cam_neexai_settings_page',
        plugins_url("assets/neexa-logo.svg", __FILE__),
        2
    );

    add_submenu_page(
        'neexa-ai-settings',
        'About Neexa AI',
        'How it Works',
        'manage_options',
        'neexa-ai-agents-sub-how-it-works',
        'cam_neexai_how_it_works_page'
    );

    //call register settings callback
    add_action('admin_init', 'cam_neexai_register_settings');
}

function cam_neexai_register_settings()
{
    //register our settings
    register_setting(
        'neexa-ai-agents-config-group',
        'neexa_ai_agents_configs',
        'neexa_ai_agents_sanitize_configs'
    );
}

function cam_neexai_sanitize_configs($input)
{
    $input['config_agent_id'] = sanitize_text_field($input['config_agent_id']);
    return $input;
}

function cam_neexai_settings_page()
{
?>
    <div class="wrap neexa-settings">
        <h2>Neexa AI Assistant Configuration</h2>
        <form method="post" action="options.php">
            <?php settings_fields('neexa-ai-agents-config-group'); ?>
            <?php $neexa_ai_agents_configs = get_option('neexa_ai_agents_configs'); ?>


            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Assistant ID</th>
                    <td><input class="assistant-id" type="text" name="neexa_ai_agents_configs[config_agent_id]" value="<?php echo esc_html(
                                                                                                        !empty($neexa_ai_agents_configs['config_agent_id'])
                                                                                                            ? $neexa_ai_agents_configs['config_agent_id']
                                                                                                            : ""
                                                                                                    ); ?>" /></td>
                </tr>
            </table>
            <i>The Assistant ID can be got from the <a href="http://app.neexa.ai" target="_blank" rel="noopener noreferrer">neexa.ai dashboard</a></i>

            <p class="submit">
                <input type="submit" class="button-primary" value="Save Changes" />
            </p>
        </form>

        <div>
            Need Help Setting Up an AI Assistant? <a href="https://wa.me/256743665790" target="_blank">
                CONTACT US NOW
            </a> to support you.
        </div>
    </div>
<?php
}


function cam_neexai_how_it_works_page()
{
?>
    <div class="wrap neexa-how-it-works">
        <h2>How It Works</h2>

        <h3> Step 1. You will need a Neexa AI account to use this plugin. </h3>
        <p>
            If you already have one, go to <a href="https://app.neexa.ai/#login" target="_blank">https://app.neexa.ai/#login</a> to log in,
            or use <a href="https://app.neexa.ai/#signup" target="_blank">https://app.neexa.ai/#signup</a> to create your account.
        </p>

        <h3>Step 2. Add business Information to teach the AI about your business. </h3>
        <p>
            This is information about your business, products and services, as well as any other interesting information people would want to know about your business.

            You Add business Information in 2 ways;
        <ol>
            <li> Add plain text, where you can type manually or copy and paste from a document. </li>
            <li> Use the website scraping feature where you simply give Neexa your website URL so it can scrape and find all the information available on your website. </li>
        </ol>
        </p>

        <h3>Step 3: Create a New AI Assistant, this will be the one chatting with people. </h3>
        <p>
            Once you've added your business information, now you can create your AI Assistant by clicking on the plus button in the 'Widgets' page(<a href="https://app.neexa.ai/#widget-chats" target="_blank">https://app.neexa.ai/#widget-chats</a>).
            Here you will be able to give the AI its name, change its Avatar, choose the Role, assign it to the business you just created, etc.
        </p>

        <h3>Step 4: Install the AI on your website or Integrate with WhatsApp business. </h3>
        <p>
            Inside the same page after creating your AI Assistant, Click the pen icon(edit) on the right side of the Assistant you just created.
            This will open a modal to edit anything of the AI Assistant you just created. In this modal, you will also see the 'Installation' section. It has an Assistant ID which you will copy and past into this Wordpress Plugin.

            After pasting this code in this plugin, you will be able to see the Neexa AI agent on your website where you and your website visitors can stat to engage/chat with it.
        </p>

        <p>
            Learn more about Neexa AI at <a href="https://www.neexa.ai" target="_blank">www.neexa.ai</a>
        </p>
    </div>
<?php
}
