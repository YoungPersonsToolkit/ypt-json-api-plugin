<?php
/**
* File contains common functions that are used across the whole plugin.
*/
	/**
	* Accepts a JSON post object and formats it to remove all fields
	* that are not required for collections. Also adds extra fields.
	*/
	function json_collection_custom_fields($_post)
	{	
		//ADD CUSTOM FIELDS
		
		//featured image url
		$image_url = null;
		if (has_post_thumbnail( $_post['ID'] ) )
		{
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $_post['ID'] ), 'single-post-thumbnail' );
			$image_url = $image[0];
		}
		
		//area code
		$area = get_post_meta($_post['ID'], 'wpcf-area', true);
		//if area isn't overridden, get it from user meta.
		if ($area == '0')
		{
			$area = get_user_meta($_post['author']['ID'], 'wpcf-user-area', true);
		}
		else
		{
			//$area = $area.'*';
		}
		
		//create the array for the fields
		$custom_fields = array();
		//add the fields to the array
		$custom_fields['featured_image_url'] = $image_url;
		$custom_fields['area'] = $area;
		
		//add the new fields to the end of the post structure for return in JSON
		$_post = array_merge( $_post, $custom_fields );		
		
		//remove unwanted fields
		unset($_post['parent']);
		unset($_post['format']);
		unset($_post['slug']);
		unset($_post['guid']);
		unset($_post['menu_order']);
		unset($_post['comment_status']);
		unset($_post['ping_status']);
		unset($_post['sticky']);
		unset($_post['comment_status']);
		unset($_post['meta']);
		unset($_post['author']);
		unset($_post['date_gmt']);
		unset($_post['status']);
		unset($_post['modified_tz']); 
		unset($_post['modified_gmt']);
		unset($_post['content']);
		unset($_post['terms']);
		unset($_post['modified_gmt']);
		unset($_post['link']);
		unset($_post['excerpt']);
		unset($_post['date_tz']);
		unset($_post['featured_image']);
		
		return $_post;
	}
	
	/**
	* Accepts a JSON post object and formats it to remove all fields
	* that are not required for single post objects. Also adds custom fields.
	*/
	function json_single_custom_fields($_post)
	{		
		//ADD CUSTOM FIELDS
		
		//featured image url
		$image_url = null;
		if (has_post_thumbnail( $_post['ID'] ) )
		{
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $_post['ID'] ), 'single-post-thumbnail' );
			$image_url = $image[0];
		}
		
		//area code
		$area = get_post_meta($_post['ID'], 'wpcf-area', true);
		//if area isn't overridden, get it from user meta.
		if ($area == '0')
		{
			$area = get_user_meta($_post['author']['ID'], 'wpcf-user-area', true);
		}
		else
		{
			//$area = $area.'*';
		}
		
		//create the array for the fields
		$custom_fields = array();
		//add the fields to the array
		$custom_fields['featured_image_url'] = $image_url;
		$custom_fields['area'] = $area;
		
		//add the new fields to the end of the post structure for return in JSON
		$_post = array_merge( $_post, $custom_fields );
		
		//REMOVE UN-NEEDED FIELDS
		
		unset($_post['featured_image']);
		unset($_post['parent']);
		unset($_post['slug']);
		unset($_post['status']);
		unset($_post['guid']);
		unset($_post['menu_order']);
		unset($_post['comment_status']);
		unset($_post['format']);
		unset($_post['excerpt']);
		unset($_post['ping_status']);
		unset($_post['sticky']);
		unset($_post['date_tz']);
		unset($_post['date_gmt']);
		unset($_post['modified_tz']);
		unset($_post['modified_gmt']);
		unset($_post['meta']);
		
		
		//remove fields from categories
		$count = 0;
		foreach($_post['terms']['category'] as $category)
		{
			unset($category['description']);
			unset($category['slug']);
			unset($category['taxonomy']);
			unset($category['parent']);
			unset($category['count']);
			unset($category['link']);
			unset($category['meta']);
			
			$_post['terms']['category'][$count] = $category;
			$count += 1;
		}
		
		//remove fields from tags
		$count = 0;
		foreach($_post['terms']['post_tag'] as $post_tag)
		{
			unset($post_tag['description']);
			unset($post_tag['slug']);
			unset($post_tag['taxonomy']);
			unset($post_tag['parent']);
			unset($post_tag['count']);
			unset($post_tag['link']);
			unset($post_tag['meta']);
			
			$_post['terms']['post_tag'][$count] = $post_tag;
			$count += 1;
		}
		
		//remove fields from the author
		unset($_post['author']['username']);
		unset($_post['author']['name']);
		unset($_post['author']['first_name']);
		unset($_post['author']['last_name']);
		unset($_post['author']['nickname']);
		unset($_post['author']['slug']);
		unset($_post['author']['URL']);
		unset($_post['author']['avatar']);
		unset($_post['author']['description']);
		unset($_post['author']['registered']);
		unset($_post['author']['meta']);
		
		return $_post;
	}
	
	function json_collection_post_fields($_post)
	{
		unset($_post['author']);
		return $_post;
	}
?>