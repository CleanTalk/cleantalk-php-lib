<?php

use Cleantalk\Common\DB;
use Cleantalk\Common\Firewall\Firewall;
use Cleantalk\Common\Firewall\Modules\SFW;
use Cleantalk\Common\RC;
use Cleantalk\Common\Variables\Server;

// For example
define( 'APBCT_TBL_FIREWALL_DATA', 'cleantalk_sfw_logs' );
define( 'APBCT_TBL_FIREWALL_LOG', 'cleantalk_sfw_logs' );

/**
 * Example function for SpamFireWall check
 *
 * @param string $api_key
 * @return void
 */
function apbct_sfw_check( $api_key = '' )
{
    // Here is checking the option.
    $sfw_enabled = true;
    $set_cookies = true;
    $sfw_counter = 0;

    if( empty( $api_key ) ){
        return;
    }

    if(
        Firewall::temporarySkip() ||
        RC::check()
    ) {
        return;
    }

    if( $sfw_enabled ){

        $firewall = new Firewall(
            $api_key,
            DB::getInstance(),
            APBCT_TBL_FIREWALL_LOG
        );

        // Here need to set extended API and Helper classes
        //$firewall->setSpecificHelper( new \Cleantalk\YourCmsNamespace\Helper() );
        //$firewall->setSpecificApi( new \Cleantalk\YourCmsNamespace\API() );

        $firewall->loadFwModule( new SFW(
            APBCT_TBL_FIREWALL_DATA,
            array(
                'sfw_counter'   => $sfw_counter,
                'cookie_domain' => Server::get('HTTP_HOST'),
                'set_cookies'    => $set_cookies,
            )
        ) );

        $firewall->run();
    }

}

/**
 * Example function for sending SpamFireWall logs.
 *
 * @param string $api_key
 * @return array|bool
 */
function apbct_sfw_send_logs( $api_key = '' )
{
    // Here is checking the option.
    $sfw_enabled = true;

    if( empty( $api_key ) ){
        return true;
    }

    if( $sfw_enabled ) {

        $firewall = new Firewall( $api_key, DB::getInstance(), APBCT_TBL_FIREWALL_LOG );
        // Here need to set extended API class
        //$firewall->setSpecificApi( new \Cleantalk\YourCmsNamespace\API() );
        $result = $firewall->sendLogs();

        if( empty( $result['error'] ) ){
            // There are actions for success sending here.
            return $result;
        }
        return array( 'error' => 'SFW_LOGS_SENDING_ERROR: ' . $result['error'] );

    }

    return array('error' => 'SFW_DISABLED');
}

/**
 * Example function for updating SpamFireWall.
 *
 * @param string $api_key
 */
function apbct_sfw_update( $api_key = '' )
{
    $sfw_enabled = true;

    if( $sfw_enabled ) {

        $firewall = new Firewall(
            $api_key,
            DB::getInstance(),
            APBCT_TBL_FIREWALL_LOG
        );

        // Here need to set extended API and Helper classes
        //$firewall->setSpecificHelper( new \Cleantalk\YourCmsNamespace\Helper() );
        //$firewall->setSpecificApi( new \Cleantalk\YourCmsNamespace\API() );

        $fw_updater = $firewall->getUpdater( APBCT_TBL_FIREWALL_DATA );
        $fw_updater->update();

    }
}