<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Mapbox Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class mapquest {
	
	private $layers;
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{		
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		Event::add('ushahidi_filter.map_base_layers', array($this, '_add_layer'));
		if (Router::$controller != 'settings')
		{
			Event::add('ushahidi_filter.map_layers_js', array($this, '_add_map_layers_js'));
		}
	}
	
	public function _add_layer()
	{
		$this->layers = Event::$data;
		$this->layers = $this->_create_layer();
		
		// Return layers object with new Cloudmade Layer
		Event::$data = $this->layers;
	}
	
	public function _create_layer()
	{

		// MapBoxStreets
		$layer = new stdClass();
		$layer->active = TRUE;
		$layer->name = 'openmapquest';
		$layer->openlayers = "XYZ";
		$layer->title = 'Open Mapquest';
		$layer->description = '.';
		$layer->api_url = '';
		$layer->data = array(
			'baselayer' => TRUE,
			'attribution' => '',
			'url' => 'http://otile1.mqcdn.com/tiles/1.0.0/map/${z}/${x}/${y}.png',
			'type' => ''
		);
		$this->layers[$layer->name] = $layer;

		return $this->layers;
	}

	function _add_map_layers_js ()
	{
		$js = Event::$data;
		
		// Next get the default base layer
		$default_map = Kohana::config('settings.default_map');

		// Hack on Mapbox Attribution layer here
		if (stripos($default_map,'mapquest_') !== FALSE)
		{
			$js .= "$('div#map').append('<div style=\"position:absolute;right:0;z-index:1000;margin: -40px 10px 0 90px;\"><small>Tiles Courtesy of <a href=\"http://www.mapquest.com/\" target=\"_blank\">MapQuest</a> <img src=\"http://developer.mapquest.com/content/osm/mq_logo.png\"> with data from OpenStreet Map.<br/ >&copy;<a href=\"http://opendatacommons.org/licenses/odbl/\">ODbL</a> <a href=\"http://www.openstreetmap.org/\">OpenStreetMap.org</a> contributors</small></div>');";
		}

		Event::$data = $js;
	}
}

new mapquest;
