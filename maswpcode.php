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

// Custom login screen functionality - COMMENTED OUT FOR TESTING
/*
function mas_custom_login_styles()
{
    // Triple-check we're only on the actual login page
    if (!isset($GLOBALS['pagenow']) || $GLOBALS['pagenow'] !== 'wp-login.php') {
        return;
    }
    
    // Additional safety checks
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    ?>
    <style type="text/css">
        body.login {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .login h1 a {
            background-image: url('<?php echo home_url(); ?>/wp-content/themes/astra-child/assets/images/logo.png');
            background-size: contain;
            background-repeat: no-repeat;
            width: 200px;
            height: 80px;
        }
        
        .login form {
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 30px;
            background: #ffffff;
        }
        
        .mas-microsoft-signin {
            background: #0078d4;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        
        .mas-microsoft-signin:hover {
            background: #106ebe;
            color: white;
            text-decoration: none;
        }
        
        .mas-microsoft-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }
        
        .mas-signin-text {
            text-align: center;
            margin: 20px 0;
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .mas-signin-text a {
            color: #0078d4;
            text-decoration: none;
        }
        
        .mas-signin-text a:hover {
            text-decoration: underline;
        }
        
        .mas-divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #999;
        }
        
        .mas-divider:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
        }
        
        .mas-divider span {
            background: white;
            padding: 0 15px;
            font-size: 14px;
        }
        
        .mas-wordpress-toggle {
            background: #f8f9fa;
            border: 1px solid #ddd;
            color: #333;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .mas-wordpress-toggle:hover {
            background: #e9ecef;
        }
        
        .mas-wordpress-form {
            display: none;
        }
        
        .mas-wordpress-form.active {
            display: block;
        }
    </style>
    <?php
}
*/

/*
function mas_custom_login_message($message)
{
    // Triple-check we're only on the actual login page
    if (!isset($GLOBALS['pagenow']) || $GLOBALS['pagenow'] !== 'wp-login.php') {
        return $message;
    }
    
    // Additional safety checks
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return $message;
    }
    
    if (empty($message)) {
        // Generate WPO365 Microsoft OAuth URL with redirect back to requested page
        $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : home_url('/vcportal/');
        
        // Use the proper WPO365 login trigger - check if WPO365 is active first
        if (class_exists('Wpo\Services\Authentication_Service')) {
            $microsoft_signin_url = home_url('/?action=openidredirect&redirect_to=' . urlencode($redirect_to));
        } else {
            // Fallback if WPO365 not available
            $microsoft_signin_url = home_url('/wp-admin/') . '?wpo365_login=1&redirect_to=' . urlencode($redirect_to);
        }
        
        $custom_message = '
        <div class="mas-custom-login">
            <a href="' . esc_url($microsoft_signin_url) . '" class="mas-microsoft-signin">
                <svg class="mas-microsoft-icon" viewBox="0 0 23 23">
                    <rect x="1" y="1" width="10" height="10" fill="#f25022"/>
                    <rect x="12" y="1" width="10" height="10" fill="#00a4ef"/>
                    <rect x="1" y="12" width="10" height="10" fill="#ffb900"/>
                    <rect x="12" y="12" width="10" height="10" fill="#7fba00"/>
                </svg>
                Sign in with Microsoft
            </a>
            
            <div class="mas-signin-text">
                Sign in with your <strong>firstname.lastname@masadvise.org</strong> account.<br>
                Contact <a href="mailto:brian.flett@masadvise.org">brian.flett@masadvise.org</a> if you do not have an account.
            </div>
            
            <div class="mas-divider">
                <span>or</span>
            </div>
            
            <button type="button" class="mas-wordpress-toggle">Sign in with WordPress Account</button>
        </div>';
        
        return $custom_message;
    }
    return $message;
}
*/

/*
function mas_wrap_wordpress_login_form()
{
    // Triple-check we're only on the actual login page
    if (!isset($GLOBALS['pagenow']) || $GLOBALS['pagenow'] !== 'wp-login.php') {
        return;
    }
    
    // Additional safety checks
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    
    ?>
    <script type="text/javascript">
        function initMasWordPressToggle() {
            const toggle = document.querySelector('.mas-wordpress-toggle');
            const form = document.querySelector('.mas-wordpress-form');
            
            if (toggle && form) {
                toggle.addEventListener('click', function() {
                    form.classList.toggle('active');
                    this.textContent = form.classList.contains('active') ? 
                        'Hide WordPress Login' : 'Sign in with WordPress Account';
                });
            } else {
                setTimeout(initMasWordPressToggle, 100);
            }
        }
        
        function wrapWordPressForm() {
            const loginForm = document.getElementById('loginform');
            if (loginForm && !loginForm.closest('.mas-wordpress-form')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'mas-wordpress-form';
                loginForm.parentNode.insertBefore(wrapper, loginForm);
                wrapper.appendChild(loginForm);
                
                const rememberMe = document.querySelector('.forgetmenot');
                const submitButton = document.querySelector('.submit');
                const nav = document.querySelector('#nav');
                const backtoblog = document.querySelector('#backtoblog');
                
                if (rememberMe) wrapper.appendChild(rememberMe);
                if (submitButton) wrapper.appendChild(submitButton);
                if (nav) wrapper.appendChild(nav);
                if (backtoblog) wrapper.appendChild(backtoblog);
                
                initMasWordPressToggle();
            } else if (!loginForm) {
                setTimeout(wrapWordPressForm, 100);
            }
        }
        
        document.addEventListener('DOMContentLoaded', wrapWordPressForm);
        setTimeout(wrapWordPressForm, 100);
    </script>
    <?php
}

// Only add login hooks when we're actually on the login page
function mas_maybe_add_login_hooks() {
    if (isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php' && 
        !is_admin() && !wp_doing_ajax() && !(defined('REST_REQUEST') && REST_REQUEST)) {
        add_action('login_head', 'mas_custom_login_styles');
        add_filter('login_message', 'mas_custom_login_message');
        add_action('login_footer', 'mas_wrap_wordpress_login_form');
    }
}
add_action('init', 'mas_maybe_add_login_hooks');
*/

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
    </style>
    <?php
}
add_action('login_head', 'mas_add_login_styles');
