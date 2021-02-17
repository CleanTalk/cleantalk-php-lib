<?php

namespace Cleantalk\Common\Firewall;

/**
 * The abstract class for any FireWall modules.
 * Compatible with any CMS.
 *
 * @version       1.0
 * @author        Cleantalk team (welcome@cleantalk.org)
 * @copyright (C) 2014 CleanTalk team (http://cleantalk.org)
 * @license       GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 * @since 2.49
 */

use Cleantalk\Common\Helper;
use Cleantalk\Common\Variables\Get;

abstract class FirewallModule {

	public $module_name;

	protected $ip_array = array();

	protected $db;
	protected $db__table__logs;
	protected $db__table__data;
	/**
	 * @var string
	 */
	protected $real_ip;
	/**
	 * @var string
	 */
	protected $test_ip;
	/**
	 * @var bool
	 */
	protected $test;

    protected $debug;

    protected $debug_data = '';

	/**
	 * FireWall_module constructor.
	 * Use this method to prepare any data for the module working.
	 *
	 * @param $log_table
	 * @param $data_table
	 * @param array $params
	 */
	abstract public function __construct( $log_table, $data_table, $params = array() );

	public function ip__append_additional( & $ips )
	{
		$this->real_ip = isset($ips['real']) ? $ips['real'] : null;

		if( Get::get( 'sfw_test_ip' ) ){
			if( Helper::ip__validate( Get::get( 'sfw_test_ip' ) ) !== false ){
				$ips['sfw_test'] = Get::get( 'sfw_test_ip' );
				$this->test_ip   = Get::get( 'sfw_test_ip' );
				$this->test      = true;
			}
		}
	}
	
	/**
	 * Use this method to execute main logic of the module.
	 *
	 * @return array  Array of the check results
	 */
	abstract public function check();
	
	public function actions_for_denied( $result ){}
	
	public function actions_for_passed( $result ){}
	
	/**
	 * @param mixed $db
	 */
	public function setDb( $db ) {
		$this->db = $db;
	}
	
	/**
	 * @param array $ip_array
	 */
	public function setIpArray( $ip_array ) {
		$this->ip_array = $ip_array;
	}
	
	public function getIpArray() {
		return $this->ip_array;
	}
	
	/**
	 * @param mixed $db__table__data
	 */
	public function setDbTableData( $db__table__data ) {
		$this->db__table__data = $db__table__data;
	}
	
	/**
	 * @param mixed $db__table__logs
	 */
	public function setDbTableLogs( $db__table__logs ) {
		$this->db__table__logs = $db__table__logs;
	}
	
	public function _die( $result ){
		
		// Headers
		if(headers_sent() === false){
			header('Expires: '.date(DATE_RFC822, mktime(0, 0, 0, 1, 1, 1971)));
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', FALSE);
			header('Pragma: no-cache');
			header("HTTP/1.0 403 Forbidden");
		}
		
	}
}