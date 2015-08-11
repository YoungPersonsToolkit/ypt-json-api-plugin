<?php
	/*
    Plugin Name: YPT JSON API Mods
    Plugin URI: http://www.hpstudios.org
    Description: Modifies the WP REST API to add endpoints and custom functionality.
    Author: Chris Pianto
    Version: 1.0
    Author URI: http://www.hpstudios.org
    */
	
	function ypt_api_init($server) 
	{
		//initialise vars for the endpoints.
		global $ypt_api_events;
		global $ypt_api_events_single;
		global $ypt_api_services;
		global $ypt_api_services_single;
		global $ypt_api_info;
		global $ypt_api_info_single;
		global $ypt_api_posts;
		global $ypt_api_posts_single;
		
		//include the classes
		require_once dirname( __FILE__ ) . '/lib/class-ypt-api-events.php';
		require_once dirname( __FILE__ ) . '/lib/class-ypt-api-events-single.php';
		require_once dirname( __FILE__ ) . '/lib/class-ypt-api-services.php';
		require_once dirname( __FILE__ ) . '/lib/class-ypt-api-services-single.php';
		require_once dirname( __FILE__ ) . '/lib/class-ypt-api-info.php';
		require_once dirname( __FILE__ ) . '/lib/class-ypt-api-info-single.php';
		require_once dirname( __FILE__ ) . '/lib/class-ypt-api-posts.php';
		require_once dirname( __FILE__ ) . '/lib/class-ypt-api-posts-single.php';
		
		//create and register the endpoints
		$ypt_api_events = 			new YPT_API_Events($server);
		$ypt_api_events_single = 	new YPT_API_Events_Single($server);
		$ypt_api_events ->			register_filters();
		$ypt_api_events_single ->	register_filters();
		
		$ypt_api_services = 		new YPT_API_Services($server);
		$ypt_api_services_single = 	new YPT_API_Services_Single($server);
		$ypt_api_services ->		register_filters();
		$ypt_api_services_single ->	register_filters();
		
		$ypt_api_info = 			new YPT_API_Info($server);
		$ypt_api_info_single = 		new YPT_API_Info_Single($server);
		$ypt_api_info ->			register_filters();
		$ypt_api_info_single ->		register_filters();
		
		$ypt_api_posts = 			new YPT_API_Posts($server);
		$ypt_api_posts_single = 	new YPT_API_Posts_Single($server);
		$ypt_api_posts ->			register_filters();
		$ypt_api_posts_single ->	register_filters();
	}
	add_action('wp_json_server_before_serve', 'ypt_api_init');
	
	
?>