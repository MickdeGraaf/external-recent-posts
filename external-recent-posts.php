<?php
/*
Plugin Name: External Recent Posts
Plugin URI: http://mickdegraaf.nl
Description: Get recent WordPress posts from a different WordPress install
Author: Mick de Graaf
Version: 0.0.0.1
Author URI: http://mickdegraaf.nl
*/



class ExternalRecentPosts extends WP_Widget {


	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'External Recent Posts' );
	}

	function widget( $args, $instance ) {

    extract($args);

    if(! isset($instance['timestamp']) || $instance['timestamp'] < (time() - 60 * 60) ){

      $id = $args['widget_id'];
      $id = str_replace("externalrecentposts-", "", $id);
      $widget_options = get_option('widget_externalrecentposts');
      $links = array();
      $rss = fetch_feed($instance['feed_url']);

      if ( ! is_wp_error( $rss ) ){

        $maxitems = $rss->get_item_quantity( 5 );
        $rss_items = $rss->get_items( 0, $maxitems );

          foreach ( $rss_items as $item ) {
            $links[] = array("link" => $item->get_permalink(),
                            "anchor" => $item->get_title());
          }
      }

      $widget_options[$id]['links'] = $links;
      $widget_options[$id]['timestamp'] = time();

      update_option('widget_externalrecentposts', $widget_options);

    }

    $before_widget = str_replace('class="', 'class=" widget_recent_entries ', $before_widget);
    echo $before_widget;

    if ( ! empty( $instance['site_url'] ) && ! empty( $instance['title'] && ! empty( $instance['place_link'] ) ) ) {
		    echo $before_title . '<a href="' . esc_url( $instance['site_url'] ) . '" title="' . esc_attr( $instance['title'] ) . '">' . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . '</a>' . $after_title;
			// If the title not empty, display it.
			} elseif ( ! empty( $instance['title'] ) ) {
				echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;
			}?>
    <ul>

      <?php foreach ($instance['links'] as $link){ ?>
          <li><a href="<?php echo esc_url($link['link']) ?>"><?php echo strip_tags($link['anchor']); ?></a></li>
      <?php  } ?>

    </ul>
<?php
    echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		// Save widget options
    $instance = array();
    $instance['timestamp'] = $old_instance['timestamp'];
    $instance['links'] = $old_instance['links'];
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    $instance['site_url'] = ( ! empty( $new_instance['site_url'] ) ) ? strip_tags( $new_instance['site_url'] ) : '';
    $instance['feed_url'] = ( ! empty( $new_instance['feed_url'] ) ) ? strip_tags( $new_instance['feed_url'] ) : '';
    $instance['place_link'] = ( ! empty( $new_instance['place_link'] ) ) ? strip_tags( $new_instance['place_link'] ) : '';
		return $instance;
	}

	function form( $instance ) {
		// Output admin widget options form
    $title =  isset($instance['title']) ? $instance['title'] : "";
    $site_url =  isset($instance['site_url']) ? $instance['site_url'] : "";
    $feed_url =  isset($instance['feed_url']) ? $instance['feed_url'] : "";
    $instance['place_link'] = isset($instance['place_link']) ? $instance['place_link'] : 0

  	?>
		<p>
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">

      <label for="<?php echo $this->get_field_id( 'site_url' ); ?>"><?php _e( 'Title URL:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'site_url' ); ?>" name="<?php echo $this->get_field_name( 'site_url' ); ?>" type="text" value="<?php echo esc_attr( $site_url ); ?>">

      <input class="checkbox" type="checkbox" <?php checked($instance['place_link'], 'on'); ?> id="<?php echo $this->get_field_id('place_link'); ?>" name="<?php echo $this->get_field_name('place_link'); ?>" />
      <label for="<?php echo $this->get_field_id('place_link'); ?>">Put link on widget title</label>

      <label for="<?php echo $this->get_field_id( 'feed_url' ); ?>"><?php _e( 'Feed URL:' ); ?></label>
      <input class="widefat" id="<?php echo $this->get_field_id( 'feed_url' ); ?>" name="<?php echo $this->get_field_name( 'feed_url' ); ?>" type="text" value="<?php echo esc_attr( $feed_url ); ?>">




		</p>
		<?php


	}
}

function xtrp_register_widgets() {
	register_widget( 'ExternalRecentPosts' );
}

add_action( 'widgets_init', 'xtrp_register_widgets' );
?>
