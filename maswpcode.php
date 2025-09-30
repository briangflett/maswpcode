<?php

/**
 * Plugin Name: Maswpcode
 * Description: form processing + private page redirect
 * Version:     1.0.1
 * Author:      Brian Flett
 */

// Form processor functionality
function mas_form_processor($form_actions_registrar)
{
    include_once(__DIR__ .  '/form-actions/Mas_Form_Processor.php');
    $form_actions_registrar->register(new Maswpcode\Mas_Form_Processor());
}
add_action('elementor_pro/forms/actions/register', 'mas_form_processor');

// Private page redirect functionality - WordPress native approach
function mas_redirect_private_pages_to_login()
{
    // Only proceed if user is not logged in
    if (is_user_logged_in()) {
        return;
    }

    // Skip admin, AJAX, REST API, and cron contexts
    if (is_admin() ||
        wp_doing_ajax() ||
        (defined('REST_REQUEST') && REST_REQUEST) ||
        (defined('DOING_CRON') && DOING_CRON)) {
        return;
    }

    // Skip WPO365 authentication flows
    if (isset($_GET['action']) && $_GET['action'] === 'openidredirect') {
        return;
    }

    // Skip Microsoft login callback parameters
    if (isset($_GET['code']) || isset($_GET['state']) || isset($_GET['session_state'])) {
        return;
    }

    // Skip login page itself
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        return;
    }

    global $post, $wpdb;

    // Check if current page/post is private (when WordPress has loaded it)
    if (is_singular() && $post && $post->post_status === 'private') {
        $redirect_url = home_url($_SERVER['REQUEST_URI']);
        $login_url = wp_login_url($redirect_url);
        wp_redirect($login_url);
        exit;
    }

    // For 404 cases, check if the URL path corresponds to a private page
    if (is_404()) {
        $request_uri = trim($_SERVER['REQUEST_URI'], '/');
        if (!empty($request_uri)) {
            // Get the first part of the URL path (the page slug)
            $path_parts = explode('/', $request_uri);
            $page_slug = $path_parts[0];

            // Check if a private page exists with this slug
            $private_page = $wpdb->get_row($wpdb->prepare(
                "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_status = 'private' AND post_type = 'page'",
                $page_slug
            ));

            if ($private_page) {
                $redirect_url = home_url($_SERVER['REQUEST_URI']);
                $login_url = wp_login_url($redirect_url);
                wp_redirect($login_url);
                exit;
            }
        }
    }
}
add_action('template_redirect', 'mas_redirect_private_pages_to_login');

// Login page customization using WordPress native hooks
function mas_add_login_message($message)
{
    // Only add message on the login page (not registration, lost password, etc.)
    if (isset($_GET['action'])) {
        return $message;
    }

    $custom_message = '<div class="mas-signin-text" style="background: #f0f6fc; border: 1px solid #c3d4e8; border-radius: 6px; padding: 15px; margin: 20px 0; text-align: center; color: #0f4c75; font-size: 14px; line-height: 1.5;">
        Click the <strong>Sign in with Microsoft</strong> button below.<br>
        On the following screen sign in with your <strong>firstname.lastname@masadvise.org</strong> account.<br>
        Contact <a href="mailto:brian.flett@masadvise.org" style="color: #0078d4; text-decoration: none;">brian.flett@masadvise.org</a> if you do not have an account.
    </div>';

    return $custom_message . $message;
}
add_filter('login_message', 'mas_add_login_message');

// Add minimal styling to login page
function mas_add_login_styles()
{
    ?>
    <style type="text/css">
        /* Improve overall login page appearance */
        body.login {
            background: #f1f1f1;
        }

        /* Style the login form container */
        .login form {
            margin-top: 20px;
            margin-bottom: 20px;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 1px 3px rgba(0,0,0,.1);
        }

        /* Ensure message styling looks good */
        .mas-signin-text a:hover {
            text-decoration: underline !important;
        }

        /* Style WPO365 login button if present */
        .wpo365-button {
            margin-top: 15px;
        }

        /* Hide username and password fields */
        .login form p.login-username,
        .login form p.login-password,
        .login form p.login-remember,
        .login form p.submit,
        .login form .user-pass-wrap,
        .login form label[for="user_login"],
        .login form label[for="user_pass"],
        .login form #user_login,
        .login form #user_pass,
        .login form .forgetmenot,
        .login form #rememberme {
            display: none !important;
        }

        /* Hide the entire form except WPO365 button */
        #loginform > p,
        #loginform > label {
            display: none !important;
        }

        /* Hide "Lost your password" link */
        .login #nav,
        .login #backtoblog {
            display: none !important;
        }
    </style>
    <?php
}
add_action('login_head', 'mas_add_login_styles');

function modify_category_search_query($query)
{
    if (!is_admin() && $query->is_main_query()) {
        if (isset($_GET['category_search']) && isset($_GET['include_children'])) {
            $category_slug = sanitize_text_field($_GET['category_search']);
            $category = get_category_by_slug($category_slug);

            if ($category) {
                $child_categories = get_term_children($category->term_id, 'category');
                $all_categories = array_merge(array($category->term_id), $child_categories);

                // Debug logging
                // error_log("Category Search Debug:");
                // error_log("Category: " . $category->name . " (ID: " . $category->term_id . ")");
                // error_log("Child categories: " . print_r($child_categories, true));
                // error_log("All categories: " . print_r($all_categories, true));

                // Modify the existing query instead of completely replacing it
                $query->set('tax_query', array(
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'term_id',
                        'terms'    => $all_categories,
                        'operator' => 'IN'
                    )
                ));
                $query->set('post_type', 'post');
                $query->set('post_status', 'publish');
                $query->set('posts_per_page', 10);

                // Clear conflicting query vars that might interfere
                $query->set('p', 0);
                $query->set('page_id', 0);
                $query->set('name', '');
                $query->set('pagename', '');
                $query->set('s', '');

                // Set query flags properly
                $query->is_home = false;
                $query->is_front_page = false;
                $query->is_page = false;
                $query->is_single = false;
                $query->is_archive = true;
                $query->is_category = true;
                $query->is_404 = false;

                // Set the queried object
                $query->queried_object = $category;
                $query->queried_object_id = $category->term_id;
                
                // Ensure query_vars is properly initialized
                if (!is_array($query->query_vars)) {
                    $query->query_vars = array();
                }
            }
        }
    }
}
add_action('pre_get_posts', 'modify_category_search_query');

// Add rewrite rule for category search
function add_category_search_rewrite_rule()
{
    add_rewrite_rule('^category-search/?$', 'index.php?category_search_page=1', 'top');
    add_rewrite_tag('%category_search_page%', '([^&]+)');
}
add_action('init', 'add_category_search_rewrite_rule');

// Handle category search page template
function handle_category_search_template($template)
{
    if (get_query_var('category_search_page')) {
        // Use the index template for now
        return get_home_template();
    }
    return $template;
}
add_filter('template_include', 'handle_category_search_template');

// Prevent 404 status for category search with no results
function prevent_category_search_404($preempt, $wp_query)
{
    if (isset($_GET['category_search']) && isset($_GET['include_children'])) {
        status_header(200);
        return true;
    }
    return $preempt;
}
add_filter('pre_handle_404', 'prevent_category_search_404', 10, 2);
