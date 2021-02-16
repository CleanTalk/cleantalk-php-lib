<?php

/**
 * Autoloader for \Cleantalk\* classes
 *
 * @param string $class
 *
 * @return void
 */
spl_autoload_register( function( $class ){

    // Register class auto loader
    // Custom modules
    if(strpos($class, 'Cleantalk') !== false){
        $class = str_replace( array('\\', 'Cleantalk' ), array( DIRECTORY_SEPARATOR, '' ), $class);
        $class_file = dirname(__DIR__ ) . DIRECTORY_SEPARATOR . $class . '.php';
        if(file_exists($class_file)){
            require_once($class_file);
        }
    }
});

