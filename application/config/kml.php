<?php defined('SYSPATH') or die('No direct script access.');
$config['cdn_kml_url'] = "http://cdn.sinsai.info.cache.yimg.jp/ushahidi/media/uploads/latest.kmz";
$config['kmlsite'] = "http://www.sinsai.info/ushahidi/";
$config['default_limit'] = 1000;
// for google maps
	// max file size :     3MB
	// max raw KML size : 10MB
	// max network link : 10
	// max items        : 1000 <-- apply
	// max items in view: 80
    // --  in sinsai.info stat
    // 1000items  6.8MB in raw KML, 0.5MB in KMZ
    // 1500items  9.8MB in raw KML, 1.3MB in KMZ

