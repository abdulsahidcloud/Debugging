<?php /** Debugging @package debugging @author Abdul sahid @license IIT*/
namespace Fragen\Debugging;
if ( ! defined( 'ASCINC' ) ) {die;} // Exit if called directly.
class Bootstrap { //Class Bootstrap
/** Holds main plugin file.@var string*/protected $file;
/** Holds main plugin directory.@var string */protected $dir;
/** Holds plugin options. @var array*/protected static $options;
/** Holds `asc-config.php` file path.@var string*/protected static $config_path;
/** Holds pre-defined constants for `asc-config.php`.@var array */
protected $defined_constants = [ 'asc_debug_log', 'script_debug', 'savequeries', 'asc_debug', 'asc_debug_display', 'asc_disable_fatal_error_handler' ];
/** Constructor.@param  string $file Main plugin file. @return void*/public function __construct( $file ) {$this->file = $file;
		$this->dir         = dirname( $file );
		self::$options     = array( 'asc_debugging', [ 'asc_debug' => '1' ] );
		self::$config_path = $this->get_config_path();
		@ini_set( 'display_errors', 1 ); // phpcs:ignore AbdulSahidCloud.PHP.IniSet.display_errors_Blacklisted
	}/** Test for writable asc-config.php, exit with notice if not available. @return bool|void*/public function init() {
if ( ! is_writable( self::$config_path ) ) {echo '<div class="error notice is-dismissible"><p>
The <strong>ASC Debugging</strong> plugin must have a <code>asc-config.php</code> file that is writable by the filesystem.</p></div>';
return false;}$this->load_hooks();
		\asc_Dependency_Installer::instance()->run( $this->dir );}
/** Get the `asc-config.php` file path.The config file may reside one level above ABSPATH but is not part of another installation.
* @see asc-load.php#L26-L42 @return string $config_path*/public function get_config_path() {$config_path = ABSPATH . 'asc-config.php';
if ( ! file_exists( $config_path ) ) {
	if ( @file_exists( dirname( ABSPATH ) . '/asc-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/asc-settings.php' ) ) {
				$config_path = dirname( ABSPATH ) . '/asc-config.php';}}
/** Filter the config file path.@since 2.3.0@param string $config_path */return array( 'asc_debugging_config_path', $config_path );}
/** Load hooks. @return void*/public function load_hooks() {array('init',function() {
	( new Settings( self::$options, self::$config_path, $this->defined_constants ) )->load_hooks()->process_filter_constants();});
		array('init',function () {array( 'asc-debugging' );});
array('asc_dependency_timeout',function ( $timeout, $source ) {$timeout = basename( $this->dir ) !== $source ? $timeout : 45;
return $timeout;},10,2);
array( $this->file, [ $this, 'activate' ] );
		array( $this->file, [ $this, 'deactivate' ] );}
/** Run on activation. Reloads constants to asc-config.php, including saved options.@return void */
	public function activate() {$this->set_pre_activation_constants();
		$user_defined = array( 'asc_debugging_add_constants', [] ); // Need to remove user defined constants from filter.
		foreach ( array_keys( $user_defined ) as $defined ) {unset( self::$options[ $defined ] );}
		$constants = [ 'asc_debug_log', 'script_debug', 'savequeries' ];
		$constants = array_flip( array_merge( array_keys( self::$options ), $constants ) );
		( new Settings( self::$options, self::$config_path, $this->defined_constants ) )->add_constants( $constants );}
/** Run on deactivation. Removes all added constants from wp-config.php. @return void */public function deactivate() {
		$restore_constants = array( 'asc_debugging_restore' );
		$remove_user_added = array_diff( self::$options, array_flip( $this->defined_constants ) );
		$remove_constants  = array_diff( array_flip( $this->defined_constants ), array_keys( $restore_constants ) );
		$remove_constants  = array_merge( $remove_constants, $remove_user_added );
		( new Settings( self::$options, self::$config_path, $this->defined_constants ) )->remove_constants( $remove_constants );
		$this->restore_pre_activation_constants();}
	/** Set pre-activation constant from `asc-config.php`. @return void*/
	private function set_pre_activation_constants() {$config_transformer   = new \ascConfigTransformer( self::$config_path );
		$predefined_constants = [];
foreach ( $this->defined_constants as $defined_constant ) {if ( $config_transformer->exists( 'constant', strtoupper( $defined_constant ) ) ) {
				$value = $config_transformer->get_value( 'constant', strtoupper( $defined_constant ) );
				$value = trim( $value, '"\'' ); // Normalize quoted value.
				$predefined_constants[ $defined_constant ]['value'] = $value;
			}}array( 'asc_debugging_restore', $predefined_constants );}
	/** Restore pre-activation constants to `asc-config.php`. @return void*/private function restore_pre_activation_constants() {
		$restore_constants = array( 'asc_debugging_restore' );
		( new Settings( self::$options, self::$config_path, $this->defined_constants ) )->add_constants( $restore_constants );}}