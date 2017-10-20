<?php
namespace Calguy1000\CGStaticMaps;

final class settings_page
{
    private $plugin;
    private $title;
    private $pagename;
    private $settings_group;
    private $settings_page;

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
        $this->settings_page = add_options_page('Static Maps', $this->title, 'manage_options', $this->pagename, [ $this, 'render_page' ] );
        add_settings_section( $this->pagename.'_1', $this->title, null, $this->pagename );
        add_action('load-'.$this->settings_page, [ $this, 'add_help'] );

        // here, I could read field definitions from a file.
        $types = [ static_map::TYPE_ROADMAP => __('Roadmap',CGSM_TEXTDOMAIN),
                   static_map::TYPE_SATELLITE => __('Satellite', CGSM_TEXTDOMAIN),
                   static_map::TYPE_HYBRID => __('Hybrid', CGSM_TEXTDOMAIN),
                   static_map::TYPE_TERRAIN => __('Terrain', CGSM_TEXTDOMAIN),
            ];
        $this->add_field( 'type', __('Default Type', CGSM_TEXTDOMAIN), 'dropdown', [ 'options'=>$types ] );
        $this->add_field( 'markercolor', __('Marker Color',CGSM_TEXTDOMAIN), 'color' );
        $this->add_field( 'docache', __('Cache Images',CGSM_TEXTDOMAIN), 'checkbox' );
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
        case 'color':
        case 'text':
        case 'checkbox':
            $create_method = "create_{$type}_field";
            call_user_func( [ $this, $create_method ], $input_id, $field_name, $value );
            break;
        case 'dropdown':
        case 'select':
            $options = $args['options'];
            call_user_func( [ $this, 'create_select_field'], $input_id, $field_name, $value, $options );
            break;
        case 'radio':
        case 'textarea':
        default:
            wp_die('unhandled field type '.$type);
        }

    }

    public function create_select_field( $id, $name, $value, array $options )
    {
        if( !count($options) ) throw new \LogicException('No options passed to '.__METHOD__);
        $out = "<select id=\"$id\" name=\"$name\">";
        foreach( $options as $key => $text ) {
            $out .= "<option value=\"$key\"";
            if( $key == $value ) $out .= " selected";
            $out .= ">{$text}</option>";
        }
        $out .= "</select>";
        echo $out;
    }

    public function create_color_field( $id, $name, $value )
    {
        echo "<input type=\"color\" id=\"$id\" name=\"$name\" value=\"$value\"/>";
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

    public function add_help()
    {
        get_current_screen()->add_help_tab(
            [
                'id'=> $this->pagename.'-help',
                'title' => __( $this->title ),
                'content' => '<p>This is some help</p>'
            ] );
    }

    public function render_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die( __('You do not have sufficient permissions to access this page.',CGSM_TEXTDOMAIN) );
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

    public function add_field( $name, $title, $type = 'text', array $more = null )
    {
        $opts = [ 'name' => $name, 'type'=>$type ];
        if( is_array($more) && count($more) ) $opts = array_merge( $more, $opts );
        add_settings_field( $name, __($title,CGSM_TEXTDOMAIN), [ $this, 'render_field'], $this->pagename, $this->pagename.'_1', $opts );
    }
} // end of class
