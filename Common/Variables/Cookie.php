<?php

namespace Cleantalk\Common\Variables;

/**
 * Class Cookie
 * Safety handler for $_COOKIE
 *
 * @since 3.0
 * @package Cleantalk\Variables
 */
class Cookie extends SuperGlobalVariables{
	
	static $instance;
	
	/**
	 * Gets given $_COOKIE variable and save it to memory
	 * @param $name
	 *
	 * @return mixed|string
	 */
	protected function get_variable( $name, $do_decode = true ){
		
		// Return from memory. From $this->variables
		if(isset(static::$instance->variables[$name]))
			return static::$instance->variables[$name];
		
		if( function_exists( 'filter_input' ) )
			$value = filter_input( INPUT_COOKIE, $name );
		
		if( empty( $value ) )
			$value = isset( $_COOKIE[ $name ] ) ? $_COOKIE[ $name ]	: '';
		
		$value = $do_decode ? urldecode( $value ) : $value;
		
		return $value;
	}

    /**
     * Universal method to adding cookies
     * Wrapper for setcookie() Considering PHP version
     *
     * @see https://www.php.net/manual/ru/function.setcookie.php
     *
     * @param string $name Cookie name
     * @param string $value Cookie value
     * @param int $expires Expiration timestamp. 0 - expiration with session
     * @param string $path
     * @param null $domain
     * @param bool $httponly
     * @param string $samesite
     *
     * @return void
     */
    public static function set ( $name, $value = '', $expires = 0, $path = '', $domain = null, $httponly = false, $samesite = 'Lax' ) {

        $secure = Server::get('HTTPS') !== 'off' || Server::get('SERVER_PORT') == 443;

        // For PHP 7.3+ and above
        if( version_compare( phpversion(), '7.3.0', '>=' ) ){

            $params = array(
                'expires'  => $expires,
                'path'     => $path,
                'domain'   => $domain,
                'secure'   => $secure,
                'httponly' => $httponly,
            );

            if($samesite)
                $params['samesite'] = $samesite;

            setcookie( $name, $value, $params );

            // For PHP 5.6 - 7.2
        }else {
            setcookie( $name, $value, $expires, $path, $domain, $secure, $httponly );
        }

    }

}