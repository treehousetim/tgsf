<?php
$major = 1;
$minor = 0;
$build = 0;
$release = 0;

$versionString = "{$major}.{$minor}.{$build}-r{$release}";
define( 'TGSF_VERSION', $versionString );
define( 'TGSF_VERSION_INT', $major . $minor . $build . $release );
