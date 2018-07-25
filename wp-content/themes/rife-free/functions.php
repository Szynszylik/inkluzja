<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* Here you can insert your functions, filters and actions. */







/* That's all, stop editing! Happy blogging. */

/* Init of framework */
/** @noinspection PhpIncludeInspection */
require_once( get_theme_file_path( '/advance/class-apollo13-framework.php' ) );

$apollo13framework_a13 = new Apollo13Framework();
$apollo13framework_a13->start();