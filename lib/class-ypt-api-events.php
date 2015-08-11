<?php
	/**
	* Extends WP_JSON_CustomPostType.
	* Route and Endpoint for Event collections.
	* Contains method overrides for registering routes, as well as formatting
	* the output of the JSON view.
	*/
	class YPT_API_Events extends WP_JSON_CustomPostType
	{
		//set the variables for the parent.
		protected $base = '/ypt/events';
		protected $type = 'event';
		
		
		/**
		* Override.
		* Registers only the collection route for Events.
		*/
		public function register_routes($routes)
		{
			//$routes = parent::register_routes($routes); //to implement the parent routes
			
			$routes[ $this->base ] = array
			(
				array( array( $this, 'get_posts' ),   WP_JSON_Server::READABLE )
			);
			
			//register my other routes (for other functions) here
			
			return $routes;
		}
		
		/**
		* Override.
		* Gets the events, sorted according to event date
		*/
		public function get_posts( $filter = array(), $context = 'view', $type = null, $page = 1 )
		{
			
			//create the query and add parameters
			$query = array();
			
			$query['post_type'] 		= 'event';
			$query['meta_key'] 			= 'wpcf-event-date';
			$query['orderby'] 			= 'meta_value';
			$query['order'] 			= 'ASC';
			$query['posts_per_page'] 	= -1;
			
			$query = array_merge($query, $filter);
			
			if (isset($filter['cat']))
			{
				$query['category__in'] = $filter['cat'];
			}
			
			
			//execute the query and create the response
			$post_query 	= new WP_Query();
			$post_list 		= $post_query->query($query);
			
			$response 		= new WP_JSON_Response();
			
			$response->query_navigation_headers($post_query);
			
			//return nothing if we don't have a post list (no posts?)
			if(!$post_list)
			{
				$response = set_data(array());
				return $response;
			}
			
			//this will hold all of the post data.
			$struct = array();
			
			//I think this is required for JSON
			$response->header( 'Last-Modified', mysql2date( 'D, d M Y H:i:s', get_lastpostmodified( 'GMT' ), 0 ).' GMT' );
			
			foreach($post_list as $post)
			{
				//strip the variables from the object into an array (easier)
				$post = get_object_vars($post);
				
				//check permission
				if ( ! json_check_post_permission( $post, 'read' ) ) 
				{
					continue;
				}
				
				//get the url of the post and link it in the header.
				$response->link_header( 'item', json_url( '/posts/' . $post['ID'] ), array( 'title' => $post['post_title'] ) );
				
				//prepare the post data
				$post_data = $this->prepare_post($post, $context);
				if(is_wp_error($post_data))
				{
					continue;
				}
				
				//add the post data to the array
				$struct[] = $post_data;
			}
			
			$response->set_data($struct);
			return $response;
		}
		
		/**
		* Override.
		* Returns an error if this route is used for a single post
		*/
		public function get_post( $id, $context = 'view' )
		{
			return new WP_Error( 'ypt_api_bad_request', __( 'Don\'t use this route for single post requests.' ), array( 'status' => 404 ) );
		}
		
		/**
		* Override. 
		* Removes a lot of fields from the returned JSON, so that less
		* data is used on the App end, and for quicker return.
		*/
		protected function prepare_post ($post, $context = 'view')
		{
			$_post = parent::prepare_post($post, $context);
			
			//format the post for common fields
			require_once ABSPATH . 'wp-content/plugins/ypt-json-api/lib/functions.php';
			$_post = json_collection_custom_fields($_post);
			
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