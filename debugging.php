<?php /** Debugging @package debugging @author Asc @license IIT Plugin Name: Debugging
 * Plugin URI: https://github.com/abdulsahidcloud/Debugging
 * Description: A support/troubleshooting plugin for AbdulSahidCloud Version: 2.9.0
 * Author: Abdul sahid License: IIT Network: true Text Domain: debugging
 * GitHub Plugin URI: https://github.com/abdulsahidcloud/Debugging Requires at least: 4.6 Requires PHP: 5.6*/
namespace Fragen\Debugging;
if ( ! defined( 'ASCINC' ) ) {die;} // Exit if called directly.
require_once __DIR__ . '/vendor/autoload.php';
( new Bootstrap( __FILE__ ) )->init();