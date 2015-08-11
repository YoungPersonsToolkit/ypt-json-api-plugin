<?php
	/**
	* Extends WP_JSON_CustomPostType.
	* Route and Endpoint for single events (/ypt/event/<n>)
	* Contains method overrides for registering routes, as well as formatting
	* the output of the JSON view.
	*/
	class YPT_API_Events_Single extends WP_JSON_CustomPostType
	{
		//set the variables for the parent.
		protected $base = '/ypt/event';
		protected $type = 'event';
		
		/**
		* Override.
		* Registers only the singlular route for Events.
		*/
		public function register_routes($routes)
		{
			//$routes = parent::register_routes($routes); //implements the parent route.
			
			$routes[ $this->base . '/(?P<id>\d+)' ] = array
			(
				array( array( $this, 'get_post' ),    WP_JSON_Server::READABLE )
			);
			
			//register my other routes (for other functions) here
			
			return $routes;
		}
		
		/**
		* Override.
		* Returns an error if this route is used for a collection.
		*/
		public function get_posts( $filter = array(), $context = 'view', $type = null, $page = 1 )
		{
			return new WP_Error( 'ypt_api_bad_request', __( 'Don\'t use this route for collection requests.' ), array( 'status' => 404 ) );
		}
		
		/**
		* Override. 
		* Adds custom fields, and removes unnecessary fields from the response.
		*/
		protected function prepare_post ($post, $context = 'view')
		{
			$_post = parent::prepare_post($post, $context);
			
			require_once ABSPATH . 'wp-content/plugins/ypt-json-api/lib/functions.php';
			$_post = json_single_custom_fields($_post);
			
			//add the custom event fields:
			//create the array for the fields
			$event_fields = array();
			
			//get the fields from the post meta
			$location = get_post_meta($post['ID'], 'wpcf-location', true);
			$eventdate = date('c', get_post_meta($post['ID'], 'wpcf-event-date', true));
			
			//add the fields to the array
			$event_fields['event_location'] = $location;
			$event_fields['event_date'] = $eventdate;
			
			//add the new fields to the end of the post structure for return in JSON
			$_post = array_merge( $_post, $event_fields );
			
			return apply_filters( "json_prepare_{$this->type}", $_post, $post, $context );
		}
	}

?>