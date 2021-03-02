<?php

use Cleantalk\Common\RemoteCalls;

/**
 * Example function for adding Remote Calls handler.
 *
 * @return void
 */
function apbct_install_rc()
{
    $api_key = 'api_key';

    // Remote calls
    if( RemoteCalls::check() ) {
        $rc = new CustomRemoteCalls( $api_key );
        $rc->perform();
    }
}


class CustomRemoteCalls extends RemoteCalls
{
    /**
     * SFW update
     *
     * @return string
     */
    public function action__sfw_update()
    {
        $api_key = 'api_key';
        return apbct_sfw_update( $api_key );
    }

    /**
     * SFW send logs
     *
     * @return string
     */
    public function action__sfw_send_logs()
    {
        return apbct_sfw_send_logs();
    }

    /**
     * Get available remote calls from the storage.
     *
     * @return array
     */
    protected function getAvailableRcActions()
    {
        return array(
            'sfw_update' => array(
                'last_call' => 0,
                'cooldown' => 0
            ),
            'sfw_send_logs' => array(
                'last_call' => 0,
                'cooldown' => 0
            )
        );
    }

    /**
     * Set last call timestamp and save it to the storage.
     *
     * @param array $action
     * @return void
     */
    protected function setLastCall( $action )
    {
        // TODO: Implement setLastCall() method.
    }
}