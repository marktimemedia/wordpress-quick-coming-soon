<?php
/**
 * Config for Landing Page
 *
 */

/**
 * Landing Page Redirect
 */
function landpg_redirect_requirements() {
	
	if( !is_user_logged_in() ) {

		if( '/coming-soon/' != $_SERVER['REQUEST_URI'] && '/xmlrpc.php' != $_SERVER['REQUEST_URI'] && '/wp-login.php' != $_SERVER['REQUEST_URI'] ) {
			wp_safe_redirect( trailingslashit( '/coming-soon' ) );
			exit;
		}
	} 
}
add_action( 'init', 'landpg_redirect_requirements' );

/**
 * Create New Coming Soon Page on plugin activation (if one does not exist)
 * From https://clicknathan.com/web-design/automatically-create-pages-wordpress/
 */
function landpg_slug_exists( $post_name ) {
	global $wpdb;
	
	if( $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "'", 'ARRAY_A' ) ) {
		return true;
	} else {
		return false;
	}
}

function landpg_create_landing_page() {

    $landpg_page_created = get_option( 'landpg_page_created' );

    /**
     * Checking that we have an options value set, this means the plugin has created a page
     */
    if ( !empty( $landpg_page_created ) ) {

	    $post_check = get_post( $landpg_page_created );

	    /**
	     * We have manually deleted the page, and we will remove the
	     * option here
	     */
	    if ( false === $post_check ) {

	    	delete_option( 'landpg_page_created' );
	    
	    } else {

		   	/**
		     * The plugin has already created this post but it's not published
		     * Let's publish it again
		     */
		    if ( 'publish' != get_post_status( $landpg_page_created ) ) { 

		    	$landpg_update = array(
			    	'ID'           => $landpg_page_created,
			    	'post_status'   => 'publish',
		  		);

			    wp_update_post( $landpg_update ); 
		    }
	    }
	}

    $landpg_page = array(
	    'post_type' => 'page',
	    'post_title' => 'Coming Soon',
	    'post_status' => 'publish',
	    'post_author' => 1,
	    'post_name' => 'coming-soon',
	    'post_content' => 'This website is coming soon!'
    );

	/**
	 * No page slug or set option exists
	 * Let's make a new page and publish it!
	 */
    if( !landpg_slug_exists( 'coming-soon' ) && !$landpg_page_created ) { 
        
        $landpg_page_id = wp_insert_post( $landpg_page );
        update_option( 'landpg_page_created', $landpg_page_id );
    }
    /**
	 * Page slug exists but option does not, probably user-created page, still working on this
	 */
    // elseif( landpg_slug_exists( 'coming-soon' ) && !$landpg_page_created ) { 

    // 	$landpg_page_id = get_page_by_path( 'coming-soon' )->ID;
    // 	update_option( 'landpg_page_created', $landpg_page_id );
    // }
}

/**
 * Force plugin-created post to Draft on plugin deactivation
 */
function landpg_unpublish_page_on_deactivation() { 

	$landpg_page_id = get_option( 'landpg_page_created' );

	if( $landpg_page_id ) {
		
		$landpg_update = array(
	    	'ID'           => $landpg_page_id,
	    	'post_status'   => 'trash',
  		);

	    wp_update_post( $landpg_update ); 
	}
}

/**
 * Prevent people from chaning the slug on page view
 * From http://wordpress.stackexchange.com/questions/31627/removing-edit-permalink-view-custom-post-type-areas
 */
function landpg_hide_edit_permalink() {
	
	if( get_option( 'landpg_page_created' ) == $post->ID ) {
        $ret2 = preg_replace('/<span id="edit-slug-buttons">.*<\/span>|<span id=\'view-post-btn\'>.*<\/span>/i', '', $return);
    }

    return $ret2;
}

add_filter( 'get_sample_permalink_html', 'landpg_hide_edit_permalink', '', 4 );