<?php

namespace Cleantalk\Common\Firewall;

/**
 * CleanTalk FireWall core class.
 * Compatible with any CMS.
 *
 * @version       3.4
 * @author        Cleantalk team (welcome@cleantalk.org)
 * @copyright (C) 2014 CleanTalk team (http://cleantalk.org)
 * @license       GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 * @see           https://github.com/CleanTalk/php-antispam
 */

use Cleantalk\Common\Helper;
use Cleantalk\Common\Variables\Get;

class Firewall
{
	
	private $ip_array = array();
	
	// Database
	private $db;
	
	//Debug
	private $debug;
	
	private $statuses_priority = array(
		// Lowest
		'PASS_SFW',
		'PASS_SFW__BY_COOKIE',
		'PASS_ANTIFLOOD',
        'PASS_ANTICRAWLER_UA',
		'PASS_ANTICRAWLER',
		'DENY_ANTIFLOOD',
        'DENY_ANTICRAWLER_UA',
		'DENY_ANTICRAWLER',
		'DENY_SFW',
		'PASS_SFW__BY_WHITELIST',
		// Highest
	);
	
	private $fw_modules = array();

	private $module_names = array();
	
	/**
	 * Creates Database driver instance.
	 *
	 * @param $db
	 */
	public function __construct( $db )
	{
		$this->db       = $db;
		$this->debug    = !! Get::get( 'debug' );
		$this->ip_array = $this->ip__get( array('real'), true );
	}
	
	/**
	 * Getting arrays of IP (REMOTE_ADDR, X-Forwarded-For, X-Real-Ip, Cf_Connecting_Ip)
	 *
	 * @param array $ips_input type of IP you want to receive
	 * @param bool  $v4_only
	 *
	 * @return array
	 */
	public function ip__get( $ips_input = array( 'real', 'remote_addr', 'x_forwarded_for', 'x_real_ip', 'cloud_flare' ), $v4_only = true )
	{
		$result = Helper::ip__get( $ips_input, $v4_only );
		return ! empty( $result ) ? array( 'real' => $result ) : array();
	}
	
	/**
	 * Loads the FireWall module to the array.
	 * For inner usage only.
	 * Not returns anything, the result is private storage of the modules.
	 *
	 * @param \Cleantalk\Common\Firewall\FirewallModule $module
	 */
	public function load_fw_module( \Cleantalk\Common\Firewall\FirewallModule $module )
	{
		if( ! in_array( $module, $this->fw_modules ) ) {
			$module->setDb( $this->db );
			$module->ip__append_additional( $this->ip_array );
			$this->fw_modules[ $module->module_name ] = $module;
			$module->setIpArray( $this->ip_array );
		}
	}
	
	/**
	 * Do main logic of the module.
	 *
	 * @return void   returns die page or set cookies
	 */
	public function run()
	{
		$this->module_names = array_keys( $this->fw_modules );
		
		$results = array();

		// Checking
		foreach ( $this->fw_modules as $module ) {

		    if( isset( $module->isExcluded ) && $module->isExcluded ) {
		        continue;
            }

			$module_results = $module->check();
			if( ! empty( $module_results ) ) {
				$results[$module->module_name] = $module_results;
			}

			if( $this->is_whitelisted( $results ) ) {
				// Break protection logic if it whitelisted or trusted network.
				break;
			}
			
		}

		// Write Logs
        foreach ( $this->fw_modules as $module ) {
            if( array_key_exists( $module->module_name, $results ) ){
                foreach ( $results[$module->module_name] as $result ) {
                    if( in_array( $result['status'], array( 'PASS_SFW__BY_WHITELIST', 'PASS_SFW', 'PASS_ANTIFLOOD', 'PASS_ANTICRAWLER', 'PASS_ANTICRAWLER_UA' ) ) ){
                        continue;
                    }
                    $module->update_log( $result['ip'], $result['status'] );
                }
            }
        }

        // Get the primary result
		$result = $this->prioritize( $results );

		// Do finish action - die or set cookies
		foreach( $this->module_names as $module_name ){
			
			if( strpos( $result['status'], $module_name ) ){
				// Blocked
				if( strpos( $result['status'], 'DENY' ) !== false ){
					$this->fw_modules[ $module_name ]->actions_for_denied( $result );
					$this->fw_modules[ $module_name ]->_die( $result );
					
				// Allowed
				}else{
					$this->fw_modules[ $module_name ]->actions_for_passed( $result );
				}
			}
			
		}
		
	}
	
	/**
	 * Sets priorities for firewall results.
	 * It generates one main result from multi-level results array.
	 *
	 * @param array $results
	 *
	 * @return array Single element array of result
	 */
	private function prioritize( $results ){
		
		$current_fw_result_priority = 0;
		$result = array( 'status' => 'PASS', 'passed_ip' => '' );
		
		if( is_array( $results ) ) {
            foreach ( $this->fw_modules as $module ) {
                if( array_key_exists( $module->module_name, $results ) ) {
                    foreach ( $results[$module->module_name] as $fw_result ) {
                        $priority = array_search( $fw_result['status'], $this->statuses_priority ) + ( isset($fw_result['is_personal']) && $fw_result['is_personal'] ? count ( $this->statuses_priority ) : 0 );
                        if( $priority >= $current_fw_result_priority ){
                            $current_fw_result_priority = $priority;
                            $result['status'] = $fw_result['status'];
                            $result['passed_ip'] = isset( $fw_result['ip'] ) ? $fw_result['ip'] : $fw_result['passed_ip'];
                            $result['blocked_ip'] = isset( $fw_result['ip'] ) ? $fw_result['ip'] : $fw_result['blocked_ip'];
                            $result['pattern'] = isset( $fw_result['pattern'] ) ? $fw_result['pattern'] : array();
                        }
                    }
                }
            }
		}
		
		$result['ip']     = strpos( $result['status'], 'PASS' ) !== false ? $result['passed_ip'] : $result['blocked_ip'];
		$result['passed'] = strpos( $result['status'], 'PASS' ) !== false;
		
		return $result;
		
	}
	
	/**
	 * Check the result if it whitelisted or trusted network
	 *
	 * @param array $results
	 *
	 * @return bool
	 */
	private function is_whitelisted( $results ) {

        foreach ( $this->fw_modules as $module ) {
            if( array_key_exists( $module->module_name, $results ) ){
                foreach ( $results[$module->module_name] as $fw_result ) {
                    if (
                        strpos( $fw_result['status'], 'PASS_BY_TRUSTED_NETWORK' ) !== false ||
                        strpos( $fw_result['status'], 'PASS_BY_WHITELIST' ) !== false ||
                        strpos( $fw_result['status'], 'PASS_SFW__BY_WHITELIST' ) !== false
                    ) {
                        return true;
                    }
                }
            }
        }
		return false;
		
	}

	/**
	 * Set FW success checked cookies for 20 min.
	 * For emergency usage only.
	 *
	 * @return bool
	 */
	public static function temporary_skip()
	{
		global $apbct, $spbc;
		if( ! empty( $_GET['access'] ) ){
			$apbct_key = ! empty( $apbct->api_key ) ? $apbct->api_key : false;
			$spbc_key  = ! empty( $spbc->api_key )  ? $spbc->api_key  : false;
			if( ( $apbct_key !== false && $_GET['access'] === $apbct_key ) || ( $spbc_key !== false && $_GET['access'] === $spbc_key ) ){
				\Cleantalk\Common\Helper::apbct_cookie__set('spbc_firewall_pass_key', md5(apbct_get_server_variable( 'REMOTE_ADDR' ) . $spbc_key),       time()+1200, '/', '');
				\Cleantalk\Common\Helper::apbct_cookie__set('ct_sfw_pass_key',        md5(apbct_get_server_variable( 'REMOTE_ADDR' ) . $apbct->api_key), time()+1200, '/', null);
				return true;
			}
		}
		return false;
	}
	
}
