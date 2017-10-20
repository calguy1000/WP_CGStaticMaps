<?php
namespace Calguy1000\CGStaticMaps;

final class widget extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct( 'cgstaticmap', __('Static Map', CGSM_TEXTDOMAIN),
                             [  'classname' => 'widget_cgsm', 'description'=>__('A Static map of a single location', CGSM_TEXTDOMAIN) ] );
    }

    protected function get_map_html( $instance )
    {
        // merge instance with default settings
        $plugin = plugin::get_instance();
        $opts = $plugin->get_settings();
        $opts = array_merge( $opts, $instance );

        // generate the object
        $map = new static_map( $opts );
        if( isset( $instance['location']) ) {
            $map = $map->withMarker( $instance['location'] );
        }
        $out = $plugin->get_rendered_map( $map, $opts );
        return $out;
    }

    public function widget( $args, $instance )
    {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		// Use current theme search form if it exists
        echo $this->get_map_html( $instance );
		echo $args['after_widget'];
    }

    public function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $width = ! empty( $instance['width'] ) ? (int) $instance['width'] : null;
        $height = ! empty( $instance['height'] ) ? (int) $instance['height'] : null;
        $zoom = ! empty( $instance['zoom'] ) ? (int) $instance['zoom'] : 14;
        $location = ! empty( $instance['location'] ) ? $instance['location'] : null;

        $do_text_field = function( $name, $val ) {
            $fmt = '<label for="%s">%s</label>';
            $id = $this->get_field_id( $name );
            $out = sprintf( $fmt, $id, __(ucwords($name)).':' ,CGSM_TEXTDOMAIN);
            $fmt = '<input class="widefat" type="text" id="%s" name="%s" value="%s"/>';
            $out .= sprintf( $fmt, $id, $this->get_field_name( $name ), esc_attr( $val ) );
            return $out;
        };

        $do_range_field = function( $name, $val, $min = 1, $max = 100 ) {
            $fmt = '<label for="%s">%s</label>';
            $id = $this->get_field_id( $name );
            $out = sprintf( $fmt, $id, __(ucwords($name)).':' ,CGSM_TEXTDOMAIN);
            $fmt = '<input class="widefat" type="range" id="%s" name="%s" value="%s" min="%s" max="%s"/>';
            $out .= sprintf( $fmt, $id, $this->get_field_name( $name ), (int) $val, (int) $min, (int) $max  );
            return $out;
        };

        $do_li = function( $in ) {
            return '<li>'.$in.'</li>';
        };

        $out = '<ul>';
        $out .= $do_li( $do_text_field( 'title', $title, 1, 20 ) );
        $out .= $do_li( $do_text_field( 'location', $location, 1, 20 ) );
        $out .= $do_li( $do_text_field( 'width', $width ) );
        $out .= $do_li( $do_text_field( 'height', $height ) );
        $out .= $do_li( $do_range_field( 'zoom', $zoom, 1, 20 ) );
        $out .= '</ul>';
        echo $out;
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        $instance['width'] = ! empty($new_instance['width']) ? (int) $new_instance['width'] : null;
        $instance['height'] = ! empty($new_instance['height']) ? (int) $new_instance['height'] : null;
        $instance['zoom'] = ! empty($new_instance['zoom']) ? (int) $new_instance['zoom'] : 14;
        $instance['title'] = ! empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : null;
        $instance['location'] = ! empty($new_instance['location']) ? sanitize_text_field($new_instance['location']) : null;
        return $instance;
    }
} // end of class