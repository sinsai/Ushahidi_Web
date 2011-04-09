<?php defined('SYSPATH') or die('No direct script access.');
/**
 * keitai Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   keitai Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Keitai_Controller extends Template_Controller {
	
	
	public $auto_render = TRUE;
	public $keitai = TRUE;
	
	// Cacheable Controller
	public $is_cachable = TRUE;
	
	// Main template
    public $template = 'keitai/layout';

	// Table Prefix
	protected $table_prefix;

    public function __construct()
    {
		parent::__construct();
		
		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');
		
		// Load Header & Footer
        $this->template->header  = new View('keitai/header');
        $this->template->footer  = new View('keitai/footer');

		$this->template->header->site_name = Kohana::config('settings.site_name');
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');

		plugin::add_javascript('keitai/views/js/jquery');
		plugin::add_javascript('keitai/views/js/jquery.treeview');
		plugin::add_javascript('keitai/views/js/expand');
		plugin::add_stylesheet('keitai/views/css/styles');
		plugin::add_stylesheet('keitai/views/css/jquery.treeview');
		
		$this->template->header->show_map = FALSE;
		$this->template->header->js = "";
		$this->template->header->breadcrumbs = "";
		
		// Google Analytics
		$google_analytics = Kohana::config('settings.google_analytics');
		$this->template->footer->google_analytics = $this->_google_analytics($google_analytics);

		$this->template->header->latlong = (isset($_GET['latlong'])) ? $_GET['latlong'] : "";
		$this->template->footer->latlong = (isset($_GET['latlong'])) ? $_GET['latlong'] : "";
	}
	
    public function index()
    {
        $this->template->content  = new View('keitai/main');
		
	$latlong = "";
	$lat = "";
	$lon = "";
	$latmin = "";
	$latmax = "";
	$lonmin = "";
	$lonmax = "";
	$this->template->content->area_name = "";
	if (isset($_GET['latlong'])) {
	    $latlong = $_GET['latlong'];
	    if (preg_match("/^([0-9\.]+),([0-9\.]+)/", $latlong, $matches)) {
	        $lat = $matches[1];
		$lon = $matches[2];
		$latmin = $lat - 0.0277778;
		$latmax = $lat + 0.0277778;
		$lonmin = $lon - 0.0277778;
		$lonmax = $lon + 0.0277778;
		$lon_center = ($lonmin+$lonmax) / 2;
		$lat_center = ($latmin+$latmax) / 2;
		$geo_url = 'http://maps.google.com/maps/api/geocode/json?region=jp&language=ja&latlng='.$lat_center.','.$lon_center.'&sensor=false';
		$geo_google = json_decode(file_get_contents($geo_url) , true);
		foreach($geo_google["results"] as $geo_val){
			if($geo_val["types"][0]=="locality" && $geo_val["types"][1]=="political" && count($geo_val["types"])==2){
				$area_name = explode(',',$geo_val["formatted_address"]);
			}
		}
		if(isset($area_name))$this->template->content->area_name =  $area_name[1];
	    }
	}
	$this->template->content->lat = $lat;
	$this->template->content->lon = $lon;
	$this->template->content->latlong = $latlong;
	$this->template->content->device = $this->checkdevice($_SERVER['HTTP_USER_AGENT']);

	// Get 10 Most Recent Reports
	if ($latlong) {
	  $this->template->content->incidents = ORM::factory('incident')
	    ->where(array('location.latitude >=' => $latmin))
	    ->where(array('location.latitude <=' => $latmax))
	    ->where(array('location.longitude >=' => $lonmin))
	    ->where(array('location.longitude <=' => $lonmax))
	    ->where('incident_active', '1')
	    ->limit('3')
	    ->orderby('incident_date', 'desc')
	    ->with('location')
	    ->find_all();
	} else {
	  $this->template->content->incidents = ORM::factory('incident')
	    ->where('incident_active', '1')
	    ->limit('3')
	    ->orderby('incident_date', 'desc')
	    ->with('location')
	    ->find_all();
	}
		
	// Get all active top level categories
        $parent_categories = array();
        foreach (ORM::factory('category')
	    ->where('category_visible', '1')
	    ->where('parent_id', '0')
	    ->orderby('category_title')
	    ->find_all() as $category) {
	    // Get The Children
	    $children = array();
	    foreach ($category->children as $child) {
	        $children[$child->id] = array(
					      $child->category_title,
					      $child->category_color,
					      $child->category_image,
					      $this->_category_count($child->id,$latmin,$latmax,$lonmin,$lonmax)
					      );
	    }

	    // Put it all together
	    $parent_categories[$category->id] = array(
						      $category->category_title,
						      $category->category_color,
						      $category->category_image,
						      $this->_category_count($category->id,$latmin,$latmax,$lonmin,$lonmax),
						      $children
						      );
			
	    if ($category->category_trusted) { // Get Trusted Category Count
	        $trusted = ORM::factory("incident")
		  ->join("incident_category","incident.id","incident_category.incident_id")
		  ->where("category_id",$category->id);
		if ( ! $trusted->count_all()) {
		  unset($parent_categories[$category->id]);
		}
	    }
        }
        $this->template->content->categories = $parent_categories;

	// Get RSS News Feeds
	$this->template->content->feeds = ORM::factory('feed_item')
	  ->limit('3')
	  ->orderby('item_date', 'desc')
	  ->find_all();
    }
	
    private function _category_count($category_id = false,$latmin=false,$latmax=false,$lonmin=false,$lonmax=false)
    {
      if ($category_id) {
	if ($latmin && $latmax && $lonmin && $lonmax) {
	  return ORM::factory('incident_category')
	    ->join('incident', 'incident_category.incident_id', 'incident.id')
	    ->join("location","incident.location_id","location.id")
	    ->where('category_id', $category_id)
	    ->where(array('location.latitude >=' => $latmin))
	    ->where(array('location.latitude <=' => $latmax))
	    ->where(array('location.longitude >=' => $lonmin))
	    ->where(array('location.longitude <=' => $lonmax))
	    ->where('incident_active', '1')
	    ->count_all();
	} else {
	  return ORM::factory('incident_category')
	    ->join('incident', 'incident_category.incident_id', 'incident.id')
	    ->where('category_id', $category_id)
	    ->where('incident_active', '1')
	    ->count_all();
	}
      } else {
	return 0;
      }
    }
	
	/*
	* Google Analytics
	* @param text mixed  Input google analytics web property ID.
    * @return mixed  Return google analytics HTML code.
	*/
	private function _google_analytics($google_analytics = false)
	{
		$html = "";
		if (!empty($google_analytics)) {
			$html = "<script type=\"text/javascript\">
				var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");
				document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));
				</script>
				<script type=\"text/javascript\">
				var pageTracker = _gat._getTracker(\"" . $google_analytics . "\");
				pageTracker._trackPageview();
				</script>";
		}
		return $html;
	}	

	private function _dms2deg($dms)
	{
	  $d = explode('.', $dms);
	  return sprintf("%0.6f", $deg = $d[0] + ($d[1] / 60.0) + ($d[2] / 3600.0) + ($d[3] / 360000.0));
	  #return $dms;
	}

	public function checkdevice($agent)
	{
	  if (preg_match("/^DoCoMo/i", $agent)) {
	    return "d";
	  } else if (preg_match("/^(J\-PHONE|Vodafone|MOT\-[CV]|SoftBank)/i", $agent)) {
	    return "s";
	  } else if (preg_match("/^KDDI\-/i", $agent) || preg_match("/UP\.Browser/i", $agent)) {
	    return "a";
	  } else {
	    return "p";
	  }
	}
}
