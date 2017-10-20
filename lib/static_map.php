<?php
namespace Calguy1000\CGStaticMaps;

function startswith( $str, $sub )
{
    return ( substr( $str, 0, strlen( $sub ) ) == $sub );
}

class static_map implements imap
{
    private $_data = [ 'type'=>self::TYPE_ROADMAP, 'size'=>'400x400', 'center'=>null, 'language'=>null, 'region'=>null, 'scale'=>null, 'zoom'=>null, 'sensor'=>null, ];
    private $_markerSize;
    private $_markerColor;
    private $_markers = [];

    public function __construct( array $opts )
    {
        foreach( $opts as $key => $val ) {
            switch( $key ) {
            case 'type':
                $this->_data[$key] = $this->parseType( $val );
                break;
            case 'scale':
                $this->_data[$key] = $this->parseScale( $val );
                break;
            case 'size':
                $this->_data[$key] = $this->parseSize( $val );
                break;
            case 'language':
                $this->_data[$key] = $this->parseLanguage( $val );
                break;
            case 'region':
                $this->_data[$key] = $this->parseRegion( $val );
                break;
            case 'center':
                $this->_data[$key] = $this->parseLocation( $val );
                break;
            case 'zoom':
                $this->_data[$key] = $this->parseZoom( $val );
                break;
            case 'markersize':
            case 'markerSize':
            case 'marker_size':
                $this->_markerSize = $this->parseMarkerSize( $val );
                break;
            case 'markercolor':
            case 'markerColor':
            case 'marker_color':
                $this->_markerColor = $this->parseColor( $val );
                break;
            case 'sensor':
                $this->_data[$key] = $this->parseBool( $val );
                break;
            case 'visible':
                $this->_data[$key] = $this->parseLocation( $val );
                break;
            }
        }

        $w = !empty($opts['width']) ? (int) $opts['width'] : null;
        $h = !empty($opts['height']) ? (int) $opts['height'] : null;
        if( $w > 0 && $h > 0 ) $this->_data['size'] = "{$w}x{$h}";
    }

    protected function parseType( $in )
    {
        switch( $type ) {
        case self::TYPE_ROADMAP:
        case self::TYPE_SATELLITE:
        case self::TYPE_HYBRID:
        case self::TYPE_TERRAIN:
            return $type;
            break;
        default:
            throw  new \RuntimeException("$type is an invalid map type for ".__CLASS__);
        }
    }

    public function withType( $in )
    {
        $obj = clone $this;
        $obj->_data['type'] = $this->parseType( $in );
        return $obj;
    }

    public function parseScale( $in )
    {
        $scale = (int) $scale;
        if( !in_array($scale,array(1,2,4)) ) throw new \RuntimeException("Invalid value for scale (possible values are 1,2,4)");
        return $scale;
    }

    public function withScale( $in )
    {
        $obj = clone $this;
        $obj->_data['scale'] = $this->parseScale( $in );
        return $obj;
    }

    public function parseSize( $in )
    {
        $w = $h = null;
        if( is_string($in) ) {
            list($w,$h) = explode('x',$in,2);
        }
        else if( is_array($in) && count($in) == 2 ) {
            if( !empty($in[0]) && !empty($in[1]) ) {
                $w = (int) $in[0];
                $h = (int) $in[1];
            }
            if( !empty($in['w']) && !empty($in['h']) ) {
                $w = (int) $in['w'];
                $h = (int) $in['h'];
            }
        }
        if( $w < 1 || $h < 1 ) throw new \RuntimeException("$in is a invalid value for size in ".__CLASS__);
        return "{$w}x{$h}";
    }

    public function withSize( $in )
    {
        $obj = clone $this;
        $obj->_data['size'] = $obj->parseSize( $in );
        return $obj;
    }

    public function getWidth()
    {
        $tmp = trim($this->_data['size']);
        list( $w, $h ) = explode('x',$tmp);
        return (int) $w;
    }

    public function getHeight()
    {
        $tmp = trim($this->_data['size']);
        list( $w, $h ) = explode('x',$tmp);
        return (int) $h;
    }

    protected function parseLanguage( $in )
    {
        if( !is_string($in) || strlen($in) !== 2 ) throw new \RuntimeException("$val is an invalid language for ".__CLASS__);
        return $in;
    }

    public function withLanguage( $in )
    {
        $obj = clone $this;
        $obj->_data['language'] = $obj->parseLanguage( $in );
        return $obj;
    }

    protected function parseRegion( $in )
    {
        if( !is_string($in) || strlen($in) !== 2 ) throw new \RuntimeException("$val is an invalid language for ".__CLASS__);
        return $in;
    }

    public function withRegion( $in )
    {
        $obj = clone $this;
        $obj->_data['language'] = $obj->parseRegion( $in );
        return $obj;
    }

    protected function parseLocation( $loc )
    {
        // input could be a string place name, an array of floats, or a string coordinate (in decimal)
        if( is_string($loc) ) {
            if( strlen($loc) <= 4 ) throw new \RuntimeException("$loc is not a valid location string");
            $tmp = explode(',',$loc);
            if( count($tmp) === 2 ) {
                if( is_float($tmp[0]) && !is_float($tmp[1]) ) {
                    $loc = $tmp;
                } else {
                    return $loc;
                }
            } else {
                return $loc;
            }
        }

        if( !is_array($loc) || count($loc) != 2 ) throw new \RuntimeException("$loc is not a valid location");
        if( !is_float($loc[0]) || !is_float($loc[1]) ) throw new \RuntimeException("$loc is not a valid location");
        if( $loc[0] < -180 || $loc[0] > 180 ) throw new \RuntimeException("$loc is not a valid location");
        if( $loc[1] < -180 || $loc[1] > 180 ) throw new \RuntimeException("$loc is not a valid location");
        return $loc;
    }

    public function withCenter( $in )
    {
        $obj = clone $this;
        $obj->_data['center'] = $obj->parseLocation( $in );
        return $obj;
    }

    protected function parseZoom( $in )
    {
        $in = (int) $in;
        if( $in < 0 || $in > 21 ) throw new \RuntimeException("$zoom is an invalid zoom value");
        return $in;
    }

    public function withZoom( $in )
    {
        $obj = clone $this;
        $obj->_data['zoom'] = $obj->parseZoom( $in );
        return $obj;
    }

    protected function parseMarkerSize( $in )
    {
        switch( strtolower($in) ) {
        case 'tiny':
        case 't':
            return 'tiny';
        case 'mid':
        case 'm':
            return 'mid';
        case 'small':
        case 's':
            return 'small';
        default:
            throw new \RuntimeException("$str is an invalid marker size value");
        }
    }

    public function withMarkerSize( $in )
    {
        $obj = clone $this;
        $obj->_markersize = $this->parseMarkerSize( $in );
        return $obj;
    }

    protected function parseBool( $in )
    {
        $in = strtolower($in);
        if( $in == '1' || $in = 'on' || $in == 'y' || $in == 'yes' || $in == 'true' ) return true;
        return false;
    }

    public function withSensor( $in )
    {
        $obj = clone $this;
        $obj->_data['sensor'] = $this->parseBool( $in );
        return $obj;
    }

    public function withVisible( $in )
    {
        $obj = clone $this;
        $obj->_data['visible'] = $this->parseLocation( $in );
        return $obj;
    }

    public function withMarker( $in )
    {
        $obj = clone $this;
        $obj->_markers[] = $this->parseLocation( $in );
        return $obj;
    }

    protected function parseColor( $str )
    {
        if( !$str ) return;
        $str = strtolower($str);
        if( startswith($str,'#') && strlen($str) == 7 ) $str = '0x'.substr($str,1);
        if( !startswith($str,'0x') ) throw new \RuntimeException("Invalid value for marker color.  must be a hex value i.e 0xFF99CC or a 7 character css color, i.e: '#ff99cc");
        return $str;
    }

    public function withMarkerColor( $in )
    {
        $obj = clone $this;
        $obj->_markerColor = $obj->parseColor( $in );
        return $obj;
    }

    protected function find_center()
    {
        if( !count($this->_markers) ) {
            if( $this->_visible ) return $this->_visible;
        }
        if( count($this->_markers) == 1 ) return; // have exactly one marker... no need for center.

        $min_lat = $min_long = 999;
        $max_lat = $max_long = -999;
        $have_ll = false;
        foreach( $this->_markers as $marker ) {
            if( is_array($marker) && count($marker) == 2 ) {
                $have_ll = true;
                $min_lat = min($min_lat,$marker[0]);
                $min_long = min($min_lat,$marker[1]);
                $max_lat = max($max_lat,$marker[0]);
                $max_long = max($min_lat,$marker[1]);
            }
        }
        if( !$have_ll ) return;

        $center_lat = $min_lat + ($max_lat - $min_lat) / 2.0;
        $center_long = $min_long + ($max_long - $min_long) / 2.0;
        return array($center_lat,$center_long);
    }

    protected function encode_location($loc)
    {
        if( is_string($loc) ) return $loc;
        if( is_array($loc) && count($loc) == 2 ) return implode(',',$loc);
    }

    protected function encode_marker($marker,$idx)
    {
        // todo: allow for marker size and color.
        $idx = $idx % 26; // only 26 labels.
        $label = chr(ord('A')+$idx);
        $out = null;
        if( $this->_markersize ) $out .= 'size:'.$this->_markersize.'|';
        if( $this->_markercolor ) $out .= 'color:'.$this->_markercolor.'|';
        if( $this->_markersize == '' || $this->_markersize != 'tiny' ) $out .= "label:$label|";
        $out .= $this->encode_location($marker);
        return $out;
    }

    public function getUrl()
    {
        if( !count($this->_markers) ) {
            if( $this->_data['center'] == null && $this->_visible == null ) throw new \RuntimeException("Please specify a center location, a visible location OR at least one marker.");
        }

        // make sure we ahve at least one marker OR a center
        if( !count($this->_markers) ) {
            if( $this->_data['center'] == null && $this->_visible == null ) throw new \RuntimeException("Please specify a center location, a visible location OR at least one marker.");
        }

        $parms = array();
        foreach( $this->_data as $key => $val ) {
            if( !strlen($val) ) continue;
            $parms[$key] = $val;
        }

        // if we don't have a center, calculate one
        if( !isset($parms['center']) && !$this->_visible ) {
            $tmp = $this->find_center();
            if( $tmp ) $parms['center'] = $this->encode_location($tmp);
        }

        // add visible
        if( $this->_visible ) {
            $parms['visible'] = $this->encode_location($this->_visible);
        }

        // add markers
        if( count($this->_markers) ) {
            $parms['markers'] = array();
            $idx=0;
            foreach( $this->_markers as $marker ) {
                $parms['markers'][] = $this->encode_marker($marker,$idx++);
            }
        }

        // build the url
        $url = self::STATIC_URL;
        $idx = 0;
        foreach( $parms as $key => $val ) {
            $sep = '&';
            if( $idx == 0 ) $sep = '?';
            $idx++;

            if( !$val ) continue;
            if( is_array($val) ) {
                foreach( $val as $key2 => $val2 ) {
                    $url .= $sep.$key.'='.$val2;
                }
            }
            else {
                $url .= $sep.$key.'='.$val;
            }
        }

        $url = str_replace(' ','%20',$url);
        if( strlen($url) > 2048 ) throw new \RuntimeException("Generated URL is too long for the google static maps API");
        return $url;
    }

} // end of class