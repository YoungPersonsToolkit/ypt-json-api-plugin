<?php
	/**
	* Extends WP_JSON_CustomPostType.
	* Route and Endpoint for single Services (/ypt/service/<n>)
	* Contains method overrides for registering routes, as well as formatting
	* the output of the JSON view.
	*/
	class YPT_API_Services_Single extends WP_JSON_CustomPostType
	{
		//set the variables for the parent.
		protected $base = '/ypt/service';
		protected $type = 'service';
		
		/**
		* Override.
		* Registers only the singlular route for services.
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
			
			//get the custom fields
			
			$custom_fields = array();
			
			$phones = get_post_meta($post['ID'], 'wpcf-phone', false);
			$websites = get_post_meta($post['ID'], 'wpcf-website', false);
			$emails = get_post_meta($post['ID'], 'wpcf-email', false);
			
			$custom_fields['phones'] = $phones;
			$custom_fields['websites'] = $websites;
			$custom_fields['emails'] = $emails;
			
			$_post = array_merge( $_post, $custom_fields );
			
			return apply_filters( "json_prepare_{$this->type}", $_post, $post, $context );
		}
	}

?>