<?php
namespace Calguy1000\CGStaticMaps;

final class plugin
{
    private static $_instance;

    protected function __construct()
    {
        register_activation_hook( __FILE__, [ $this, 'on_activate'] );
        register_deactivation_hook( __FILE__, [ $this, 'on_deactivate'] );
        add_action('widgets_init', [ $this, 'setup_widgets'] );
        add_shortcode('cgsm', [ $this, 'process_shortcode'] );
        if( is_admin() ) {
            //add_action('admin_init', [ $this, 'on_admin_init'] );
            add_action('admin_menu', [ $this, 'setup_admin_nav'] );
        }
    }

    public static function get_instance()
    {
        if( !self::$_instance ) self::$_instance = new self();
        return self::$_instance;
    }

    public function get_name()
    {
        $tmp = plugin_basename(__FILE__);
        $name = explode('/',$tmp)[0];
        return $name;
    }

    public function get_settings()
    {
        return get_option( $this->get_name(), ['api_key'=>'testing'] );
    }

    public function on_activate()
    {

    }

    public function on_deactivate()
    {

    }

    public function on_admin_init()
    {
        register_setting( $this->get_name(), $this->get_name(), [ 'sanitize_callback' => [ $this, 'sanitize_settings'] ] );
    }

    public function sanitize_settings( $args )
    {
        return $args;
    }

    public function setup_widgets()
    {
        require_once(__DIR__.'/widget.php');
        register_widget( '\\Calguy1000\\CGStaticMaps\\widget' );
    }

    public function setup_admin_nav()
    {
        require_once(__DIR__.'/settings_page.php');
        $obj = new settings_page( $this );
    }

    public function process_shortcode( $attribs )
    {
        $opts = $this->get_settings();
        $opts = array_merge( $opts, $attribs );

        $map = new static_map( $opts );
        if( isset( $opts['location']) ) {
            $map = $map->withMarker( $opts['location'] );
        }
        $title = !empty( $opts['title'] ) ? trim($opts['title']) : null;

        echo '<div class="cgstaticmap">';
        if( $title ) echo '<h1>'.$title.'</h1>';
        $out = $this->get_rendered_map( $map, $opts );
        echo $out;
        echo '</div>';
    }

    protected function get_cache_dir()
    {
        return dirname(__DIR__).'/cache';
    }

    protected function get_cache_file( $signature )
    {
        return $this->get_cache_dir()."/{$signature}.png";
    }

    protected function get_cached_url( static_map $map )
    {
        $signature = sha1(__CLASS__.serialize($map));
        $file = $this->get_cache_file( $signature );
        if( is_file( $file ) ) {
            $mtime = filemtime( $file );
            if( $mtime < time() - 29 * 24 * 3600 ) @unlink( $file );
        }
        if( !is_file( $file ) ) {
            // copy the map url to static location.
            $dir = $this->get_cache_dir();
            if( !is_dir( $dir ) ) @mkdir( $dir );
            if( !is_dir( $dir ) ) throw new \RuntimeException('Cannot create cache dir '.$dir);
            $data = file_get_contents( $map->getURL() );
            file_put_contents( $file, $data );
        }

        $url = plugin_dir_url( __DIR__)."cache/{$signature}.png";
        return $url;
    }

    public function get_rendered_map( static_map $map, array $opts )
    {
        // this outputs a single img tag.
        $settings = $this->get_settings();
        $url = $map->getURL();
        if( $settings['docache'] ) {
            $tmp = $this->get_cached_url( $map );
            if( $tmp ) $url = $tmp;
        }

        // now, we output the img tag, and any alt/title/class attributes
        $out = '<img';
        $out .= ' src="'.trim($url).'"';
        if( !empty( $opts['alt']) ) $out .= ' alt="'.trim($opts['alt']).'"';
        if( empty( $opts['noatts']) || !$opts['noattrs'] ) {
            if( !empty( $opts['id']) ) $out .= ' id="'.trim($opts['id']).'"';
            if( !empty( $opts['title']) ) $out .= ' title="'.trim($opts['title']).'"';
            if( !empty( $opts['class']) ) $out .= ' class="'.trim($opts['class']).'"';
            if( empty( $opts['nosize']) || !$opts['nosize'] ) {
                $out .= ' width="'.$map->getWidth().'"';
                $out .= ' height="'.$map->getHeight().'"';
            }
        }
        $out .= "/>";
        return $out;
    }

} // class