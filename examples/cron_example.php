<?php

use Cleantalk\Common\Cron;
use Cleantalk\Common\RemoteCalls;

/**
 * Example function for adding cron jobs.
 *
 * @return void
 */
function apbct_install_cron()
{
    $cron = new CustomCron();
    // From global function
    $cron->addTask( 'sfw_update', 'apbct_sfw_update', 86400, time() + 60 );
    $cron->addTask( 'sfw_send_logs', 'apbct_sfw_send_logs', 3600 );
    // Or from any namespace
    $cron->addTask( 'sfw_update', '\SomeNameSpace\Class::apbct_sfw_update', 86400, time() + 60 );
    $cron->addTask( 'sfw_send_logs', '\SomeNameSpace\Class::apbct_sfw_send_logs', 3600 );
}

/**
 * Example function for check and running cron jobs.
 *
 * @return void
 */
function apbct_run_cron()
{
    $cron = new CustomCron();
    $tasks_to_run = $cron->checkTasks(); // Check for current tasks. Drop tasks inner counters.
    if(
        ! empty( $tasks_to_run ) && // There is tasks to run
        ! RemoteCalls::check() && // Do not doing CRON in remote call action
        (
            ! defined( 'DOING_CRON' ) ||
            ( defined( 'DOING_CRON' ) && DOING_CRON !== true )
        )
    ){
        $cron_res = $cron->runTasks( $tasks_to_run );

        // Errors handling
        if( is_array( $cron_res ) ) {
            foreach( $cron_res as $task => $res ) {
                if( $res === true ) {
                    // Do clearing the error
                } else {
                    // Do logging the error
                }
            }
        }

    }
}


class CustomCron extends Cron {

    /**
     * Save option with tasks
     *
     * @param array $tasks
     * @return bool
     */
    public function saveTasks($tasks)
    {
        // TODO: Implement saveTasks() method.
    }

    /**
     * Getting all tasks
     *
     * @return array
     */
    public function getTasks()
    {
        // TODO: Implement getTasks() method.
    }

    /**
     * Save option with tasks
     *
     * @return int timestamp
     */
    public function getCronLastStart()
    {
        // TODO: Implement getCronLastStart() method.
    }

    /**
     * Save timestamp of running Cron.
     *
     * @return bool
     */
    public function setCronLastStart()
    {
        // TODO: Implement setCronLastStart() method.
    }
}