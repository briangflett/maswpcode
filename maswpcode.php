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

// Private page redirect functionality - safe version
function mas_redirect_private_pages_to_login()
{
    // Skip admin, AJAX, REST API, and cron contexts
    if (is_admin() || 
        wp_doing_ajax() || 
        (defined('REST_REQUEST') && REST_REQUEST) ||
        (defined('DOING_CRON') && DOING_CRON) ||
        is_user_logged_in()) {
        return;
    }
    
    // Skip for admin-related URLs and actions
    if (isset($_SERVER['REQUEST_URI']) && (
        strpos($_SERVER['REQUEST_URI'], '/wp-admin/') !== false ||
        strpos($_SERVER['REQUEST_URI'], '/wp-json/') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'admin-ajax.php') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'elementor') !== false ||
        strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false
    )) {
        return;
    }
    
    // Skip for any actions at all (this protects AJAX requests)
    if (isset($_REQUEST['action']) || isset($_POST['action']) || isset($_GET['action'])) {
        return;
    }

    global $post, $wpdb;

    // First check if we have a post object and it's private
    if (is_singular() && $post && $post->post_status === 'private') {
        $redirect_url = home_url($_SERVER['REQUEST_URI']);
        $login_url = wp_login_url($redirect_url);
        wp_redirect($login_url);
        exit;
    }

    // For cases where page shows 404 but exists as private, check URL path directly
    $request_uri = trim($_SERVER['REQUEST_URI'], '/');
    if (!empty($request_uri)) {
        $path_parts = explode('/', $request_uri);
        $page_slug = $path_parts[0];

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
add_action('template_redirect', 'mas_redirect_private_pages_to_login');

// Custom login screen functionality - ONLY on actual login page
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
        $microsoft_signin_url = home_url('/?action=openidredirect&redirect_to=' . urlencode($redirect_to));
        
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
