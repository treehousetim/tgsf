<?php

$file = realpath( dirname( __FILE__ ) . '/../../../' ) . '/tgsf_core_assets/minify_groups/'. $_GET['g'] . '.php';
return require_once( $file );
