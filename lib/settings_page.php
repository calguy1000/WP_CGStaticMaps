<?php
namespace Calguy1000\CGStaticMaps;

final class settings_page
{
    private $plugin;
    private $title;
    private $pagename;
    private $settings_group;

    public function __construct( plugin $plugin, $title = null, $name = 'settings', $settings_group = null, $option_name = null )
    {
        if( !$title ) $title = $plugin->get_name();
        if( !$name ) $name = 'settings';   // name for this page.
        if( !$settings_group ) $settings_group = $plugin->get_name();
        if( !$option_name ) $option_name = $plugin->get_name();
        $this->plugin = $plugin;
        $this->title = $title;
        $this->pagename = $plugin->get_name().$name;
        $this->settings_group = $settings_group;

        register_setting( $settings_group, $option_name, [ 'sanitize_callback' => [ $this, 'sanitize_settings'] ] );
        add_options_page('Static Maps', $this->title, 'manage_options', $this->pagename, [ $this, 'render_page' ] );
        add_settings_section( $this->pagename.'_1', $this->title, null, $this->pagename );
        // here, I could read field definitions from a file.

        $this->add_field( 'api_key', __('API Key'), 'text' );
        $this->add_field( 'docache', __('Cache Images'), 'checkbox' );
    }

    public function sanitize_settings( $args )
    {
        // return the array that will be saved.
        return $args;
    }

    public function render_field( $args )
    {
        $input_name = $input_id = $args['name'];
        $settings = $this->plugin->get_settings();
        $value = !empty( $settings[$input_name] ) ? $settings[$args['name']] : null;
        $field_name = $this->settings_group.'['.$input_name.']';
        $type = ! empty( $args['type'] ) ? strtolower( $args['type'] ) : 'text';
        switch( $type ) {
        case 'text':
        case 'checkbox':
            $create_method = 'create_'.$type.'_field';
            break;
        case 'radio':
        case 'textarea':
        default:
            wp_die('unhandled field type '.$type);
        }

        call_user_func( [ $this, $create_method ], $input_id, $field_name, $value );
    }

    public function create_text_field( $id, $name, $value )
    {
        echo "<input type=\"text\" id=\"$id\" name=\"$name\" value=\"$value\"/>";
    }

    public function create_checkbox_field( $id, $name, $value )
    {
        $txt = "<input type=\"hidden\" name=\"$name\" value=\"0\"/>";
        $txt .= "<input type=\"checkbox\" id=\"$id\" name=\"$name\" value=\"1\"";
        if( $value ) $txt .= " checked";
        $txt .= "/>";
        echo $txt;
    }

    public function render_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }

        $settings = $this->plugin->get_settings();
        echo '<div class="wrap">';
        echo '<form name="settings" method="post" action="options.php">';
        settings_fields( $this->settings_group );
        do_settings_sections( $this->pagename );
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function add_field( $name, $title, $type = 'text' )
    {
        add_settings_field( $name, __($title), [ $this, 'render_field'], $this->pagename, $this->pagename.'_1', [ 'name'=>$name, 'type'=>$type ] );
    }
} // end of class
