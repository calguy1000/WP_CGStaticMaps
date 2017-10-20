<?php
namespace Calguy1000\CGStaticMaps;

interface imap
{
    const STATIC_URL = "https://maps.googleapis.com/maps/api/staticmap";
    const TYPE_ROADMAP = 'roadmap';
    const TYPE_SATELLITE = 'satellite';
    const TYPE_HYBRID = 'hybrid';
    const TYPE_TERRAIN = 'terrain';

    public function getURL();
    public function withSize( $str );
    public function getWidth();
    public function getHeight();
    public function withScale( $scale );
    public function withType( $type );
    public function withLanguage( $language );
    public function withRegion( $region );
    public function withCenter( $location );
    public function withZoom( $zoom );
    public function withMarkerSize( $size );
    public function withMarkerColor( $color );
    public function withSensor( $sensor );
    public function withVisible( $location );
    public function withMarker( $location );
} // end of class