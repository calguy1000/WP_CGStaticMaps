# CGStaticMaps
## A Simple wordpress plugin to allow generating static maps on your Wordpress site

This simple plugin implements a widget type, and a shortcode to allow generating simple static maps using the Google static maps API.   It is also capable of caching the images on your server for up to 29 days.

When adding a static map widget you can specify the map size in pixels, a single location using coordinates or an address, a zoom level, and the map type.

When using a shortcode there are many more options, including the ability to specify a center, a location that must be visible, and multiple markers.

### Installation
1. Download the plugin from <https://github.com/calguy1000/WP_CGStaticMaps>.
2. Install the plugin in your wp-content/plugins directory.
3. Visit your plugins page in your WordPress installation and activate the plugin.
4. Visit this plugins settings page and ensure that the default settings are appropriate.

### Usage
This plugin provides a widget, and a shortcode for displaying static maps.

#### As a Widget
To use the widget, browse to "Appearance >> Widgets" inside your Wordpress admin panel, and drag a 'Static Map' widget into one of the available areas.
From there, you can easily adjust the title, location, zoom leve, width and height of the widget.   Locations can either be entered as an address or as a coordinate string i.e:  XX.XXXX,YY.YYYY
Once all information is complete, you can save the changes, and preview a page to view your map.

#### As a shortcode
This plugin provides the [cgsm] shortcode to create a static map from within your content area.  This shortcode supports the following attributes.
* location - *required* - Sets the marker for the map.  A location can be specified as an address.  i.e: 'Calgary, AB', or as a coordinate string.
* center - Sets the location of the center of the map, as a location.  Again, location can be specified as an address or as a coordinate string.
* visible - Sets a location that is attempted to be made visible in the generated map.  Setting location, and center and/or visible may override the zoom paramter.
* zoom - Sets the zoom level.  Valid values are between 1 and 21.  The default value is 14.
* maptype - Sets the map type.  Valid values are 'roadmap', 'satellite', 'hybrid', or 'terrain'.  The default value is 'roadmap', unless adjusted in the plugins settings panel.
* width - Sets the map width, in pixels.  The default value is 400.  If using this syntax both the width and height attributes musb e specified.
* height - Sets the map height, in pixels.  The default value is 400.  If using this syntax both the width and height attributes musb e specified.
* size - Sets the map size in pixels in a single argument.  The format is WWxHH.  i.e:  size=300x300.
* scale - Sets the map scale.  Valid values are 1, 2, and 4.
* language - Sets the map language.  Valid values must be a 2 character language code recognized by google maps.
* region - Sets the map region.  Valid values must be a 2 character region code recognized by google maps.
* markersize - Sets the marker size.  Valid values are 't', 'tiny', 'm', 'mid', 's' or 'small'.
* markercolor - Sets the marker color.  Valid values are a 7 character hexadecimal color value beginning with '#'.  i.e: #00ee77.
+ title - Sets the title attribute for the generated image.  If not specified, the location name will be used.
* alt - Sets the alt attribute for the generated image.  If not specified, the location name will be used.
* class - Sets a class for the generated image.
* noattrs - Indicates that (other than the alt attribute for validation purposes) no additional HTML attributes will be output on the resulting image.
* nosize - Indicates that the width and height attributes should not be output.  This attribute has no effect if the 'noattrs' attribute is also specified.
##### Example 1:
<pre><code>[cgsm location="New York, NY" width=250 height=300]</code></pre>

##### Example 2:
<pre>[cgsm location="New York, NY" center="Chicago, IL" visible="Toronto, ON" size="450x400" title="Sample Map"]</pre>
#### Caching

### Future Development

### The Author

### Copyright and License

### Support
If you like this plugin, and my other work and would like to donate a few dollars to my cause I would be eternally grateful.  You can [click here](https://paypal.me/calguy1000/10) to donate a few dollars.
