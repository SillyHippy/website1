<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'TWBB_VERSION', '1.27.37' );
define( 'TWBB_PREFIX', 'twbb' );
define( 'TWBB_DIR', dirname( __FILE__ ) );
define( 'TWBB_URL', plugins_url( plugin_basename( dirname( __FILE__ ) ) ) );
define( 'TWBB_DEV', FALSE );
define( 'TWBB_DEBUG', FALSE );
define( 'TWBB_ELEMENTOR_MIN_VERSION', '3.5.0' );
if(!defined('TENWEB_PREFIX')){
    define('TENWEB_PREFIX', 'tenweb');
}

if(!defined('TENWEB_VERSION')){
    define('TENWEB_VERSION', 'two-123.131.32');
}

// in seconds
if (!defined('TW_IN_PROGRESS_LOCK')) {
    define('TW_IN_PROGRESS_LOCK', 300);
}

if (!defined('TENWEB_MANAGER_ID')) {
    define('TENWEB_MANAGER_ID', 51);
}

//TODO check or change White label
if ( class_exists( '\Tenweb_Manager\Helper' ) && method_exists( '\Tenweb_Manager\Helper', 'get_company_name' ) && strtolower( \Tenweb_Manager\Helper::get_company_name() ) !== '10web' ) {
	define( 'TENWEB_WHITE_LABEL', TRUE );
} else {
	define( 'TENWEB_WHITE_LABEL', FALSE );
}
if (!function_exists('get_plugins')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
if (!defined('TW_HOSTED_ON_10WEB')) {
    $mu_plugins = get_mu_plugins();
    define('TW_HOSTED_ON_10WEB', isset($mu_plugins['tenweb-init.php']));
}
require_once TWBB_DIR . '/env.php';
if (!defined('TW_AI_PLAN_IDS')) {
    // very bad solution
    if (strpos(TENWEB_DASHBOARD, 'test') !== false) {
        define('TW_AI_PLAN_IDS', [374,375,376,377,378,379,380,381,382,383,407,408,409,410,411,412,415,416]);
        define('TW_PROFFESIONAL_IDS', [425,426,427,428,429,430,431,432,433,434,435,436]);
    } else {
        define('TW_AI_PLAN_IDS', [368,369,370,371,372,373,374,375,376,377,390,391,393,394,395,396,397,398,400,401,163,165]);
        define('TW_PROFFESIONAL_IDS', [408,409,410,411,412,413,414,415,416,417,419]);
    }
}
