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
        return apbct_sfw_update( $this->api_key );
    }

    /**
     * SFW send logs
     *
     * @return string
     */
    public function action__sfw_send_logs()
    {
        return apbct_sfw_send_logs( $this->api_key );
    }

    /**
     * Get available remote calls from the storage.
     *
     * @return array
     */
    protected function getAvailableRcActions()
    {
        // JUST AN EXAMPLE
        return array(
            'sfw_update_init' => array(
                'last_call' => 0,
                'cooldown' => 10
            ),
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
     * @param string $action
     * @return void
     */
    protected function setLastCall( $action )
    {
        // @ToDo this code is just an example
        $remote_calls = $this->getAvailableRcActions();
        $remote_calls[$action]['last_call'] = time();
        // @ToDo do save the remote calls to the storage
    }
}