<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Mobile Controller
 * Generates KML with PlaceMarkers and Category Styles
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Mobile Controller	
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
* 
*/

class Reports_Controller extends Mobile_Controller {
	var $api_timeout = 60;

    public function __construct()
    {
		parent::__construct();
	}
	
	/**
	 * Displays a list of reports
	 * @param boolean $category_id If category_id is supplied filter by
	 * that category
	 */
	public function index($category_id = false)
	{
		$this->template->content = new View('mobile/reports');
		$get_params = "?";
		if(isset($_GET['c']) AND !empty($_GET['c']) AND $_GET['c']!=0)$get_params .= "c=".$_GET['c']."&";
		if(isset($_GET['sw']))$get_params .= "sw=".$_GET['sw']."&";
		if(isset($_GET['ne']))$get_params .= "ne=".$_GET['ne']."&";
		if(isset($_GET['l']) AND !empty($_GET['l']) AND $_GET['l']!=0)$get_params .= "l=".$_GET['l'];
		$get_params = rtrim(rtrim($get_params,'&'),'?');
		$this->template->content->get_params = $get_params;
		$db = new Database;
		
		$filter = ( $category_id )
			? " AND ( c.id='".$category_id."' OR 
				c.parent_id='".$category_id."' )  "
			: " AND 1 = 1";
		// 検索キーワード取得
		if(isset($_GET["keyword"]) && trim($_GET["keyword"]) !==""){
			$keywords = array();
			$keyword = str_replace("　"," ",$_GET["keyword"]);
			$keywords = explode(" ",$keyword);
		}
		$keyword_like = "1=1";
		if(isset($keywords) && count($keywords)){
			$keyword_like = "";
			foreach($keywords as $val){
				$keyword_like .= "(incident_title like '%".addslashes($val)."%' OR incident_description like '%".addslashes($val)."%') AND ";
			}
			$keyword_like = rtrim($keyword_like," AND ");
		}
		//ジオコーディング
		$this->template->content->area_name = "";
		$this->template->content->disp_distance = "";
		//指定地区の指定半径内インシデント取得でAPIで緯度経度を取得できなかった場合DBを取りに行かないようにするためのフラグ
		$dbget_flg = true;
		$this->template->content->choices_flg = false;
		//指定地区の指定半径内インシデント取得処理
		$location_ids = array();
		$select_dist = "";
		if(isset($_GET["address"]) && trim($_GET["address"]) !== "" && isset($_GET["distance"]) && is_numeric($_GET["distance"]) && $_GET["distance"] > 0){
			$address = urlencode($_GET["address"]);
			// http://www.geocoding.jp/を利用して指定地区名の緯度経度を取得
			$geocoding_url = 'http://www.geocoding.jp/api/?q='.$address;
		    $geo_geocoding = @file_get_contents($geocoding_url,false,stream_context_create(array('http' => array('timeout'=>$this->api_timeout))));
			// APIのエラーハンドリング
			if($geo_geocoding === FALSE){
				if(count($http_response_header) > 0){
					$stat_tokens = explode(' ', $http_response_header[0]);
					switch($stat_tokens[1]){
						case 404:
						// 404 Not found の場合
						break;
						case 500:
						// 500 Internal Server Error の場合
						break;
						default:
						// その他
						break;
					}
				}else{
					// タイムアウトの場合
				}
			}else{
				$geo_geocoding = simplexml_load_string($geo_geocoding);
			}
			//結果の取得とインシデントの取得
			if(isset($geo_geocoding->coordinate)){
				if(isset($geo_geocoding->coordinate->lat) && isset($geo_geocoding->coordinate->lng)){
					$lat_center = $geo_geocoding->coordinate->lat;
					$lon_center = $geo_geocoding->coordinate->lng;
					$area_name = $geo_geocoding->address;
					$_GET["address"] = $this->template->content->area_name = trim($area_name);
					if($_GET["distance"] >= 1){
						$this->template->content->disp_distance = $_GET["distance"]."km";
					}else{
						$this->template->content->disp_distance = ($_GET["distance"]*1000)."m";
					}
					$query = 'SELECT id FROM '.$this->table_prefix.'location WHERE (round(sqrt(pow(('.$this->table_prefix.'location.latitude - '.$lat_center.')/0.0111, 2) + pow(('.$this->table_prefix.'location.longitude - '.$lon_center.')/0.0091, 2)), 1)) <= '.$_GET["distance"];
					$query = $db->query($query);
					foreach ( $query as $items )
					{
						$location_ids[] =  $items->id;
					}
					$select_dist = ",(round(sqrt(pow((l.latitude - $lat_center)/0.0111, 2) + pow((l.longitude - $lon_center)/0.0091, 2)), 1)) as dist";
				}
			}elseif(isset($geo_geocoding->choices)){
				$this->template->content->choices_flg = true;
				$dbget_flg = false;
			}
		}
		$location_id_in = '1=1';
		if (count($location_ids) > 0)
		{
			$location_id_in = 'location_id IN ('.implode(',',$location_ids).')';
		}
    $query =  "SELECT DISTINCT i.*, l.location_name ".$select_dist.
             " FROM `".$this->table_prefix."incident`".
             " AS i JOIN `".$this->table_prefix."incident_category`".
             " AS ic ON (i.`id` = ic.`incident_id`)".
             " JOIN `".$this->table_prefix."category`".
             " AS c ON (c.`id` = ic.`category_id`)".
             " JOIN `".$this->table_prefix."location`".
             " AS l ON (i.`location_id` = l.`id`)".
             " WHERE `incident_active` = '1' AND $keyword_like AND $location_id_in $filter";
    // Location
    if(isset($_COOKIE["lat"]) && isset($_COOKIE["lng"]) && $_COOKIE["lat"] != "na" && $_COOKIE["lng"] != "na") {
      $lat_center = (float)$_COOKIE["lat"];
      $lon_center = (float)$_COOKIE["lng"];
      //$query .= ' AND round(sqrt(pow(('.$this->table_prefix.'l.latitude - '.$lat_center.')/0.0111, 2) +'.
      //  ' pow(('.$this->table_prefix.'l.longitude - '.$lon_center.')/0.0091, 2)), 1) <= 300';
    }
		// Pagination
		$pagination = new Pagination(array(
				'style' => 'mobile',
				'query_string' => 'page',
				'items_per_page' => (int) Kohana::config('mobile.items_per_page'),
				'total_items' => $db->query($query)->count()
				));
		$this->template->content->pagination = $pagination;
    $query_for_incidents = $query;
    if(isset($_GET["order"])){
		// ソート順を定義
		if(isset($_GET["order"]) && $_GET["order"]=="new"){
			// 新着順
			$query_for_incidents .= " ORDER BY incident_date DESC , (round(sqrt(pow((".$this->table_prefix."l.latitude - ".$lat_center.")/0.0111, 2) + pow((".$this->table_prefix."l.longitude - ".$lon_center.")/0.0091, 2)), 1)) ASC LIMIT ";
		}elseif(isset($_GET["order"]) && $_GET["order"]=="dist"){
			// 近隣順
			$query_for_incidents .= " ORDER BY (round(sqrt(pow((".$this->table_prefix."l.latitude - ".$lat_center.")/0.0111, 2) + pow((".$this->table_prefix."l.longitude - ".$lon_center.")/0.0091, 2)), 1)) ASC , incident_date DESC LIMIT ";
		}
    }elseif(isset($_COOKIE["lat"]) && isset($_COOKIE["lng"]) && $_COOKIE["lat"] != "na" && $_COOKIE["lng"] != "na") {
    	echo "2";
      $query_for_incidents .= " ORDER BY (round(sqrt(pow((".$this->table_prefix."l.latitude - ".$lat_center.")/0.0111, 2) + pow((".$this->table_prefix."l.longitude - ".$lon_center.")/0.0091, 2)), 1)) ASC LIMIT ";
    }else{
    	echo "3";
      $query_for_incidents .= " ORDER BY incident_date DESC LIMIT ";
    }
    $query_for_incidents .= (int) Kohana::config('mobile.items_per_page') . " OFFSET ".$pagination->sql_offset;
    $incidents = $db->query($query_for_incidents);
		
		// If Category Exists
		if ($category_id)
		{
			$category = ORM::factory("category", $category_id);
		}
		else
		{
			$category = FALSE;
		}
			
		$this->template->content->incidents = $incidents;
		$this->template->content->category = $category;
	}
	
	/**
	 * Displays a report.
	 * @param boolean $id If id is supplied, a report with that id will be
	 * retrieved.
	 */
	public function view($id = false)
	{	
		$this->template->header->show_map = TRUE;
		$this->template->header->js = new View('mobile/reports_view_js');
		$this->template->content = new View('mobile/reports_view');
		
		if ( ! $id )
		{
			url::redirect('mobile');
		}
		else
		{
			$incident = ORM::factory('incident', $id);
			if ( ! $incident->loaded)
			{
				url::redirect('mobile');
			}
			
			$incident->incident_description = preg_replace('/((https?|http)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+))/', '<a href="$1">$1</a>', $incident->incident_description);
			$this->template->content->incident = $incident;
			
			$this->template->header->js->latitude = $incident->location->latitude;
			$this->template->header->js->longitude = $incident->location->longitude;
			
			$page_no = (isset($_GET['p'])) ? $_GET['p'] : "";
			$category_id = (isset($_GET['c'])) ? $_GET['c'] : "";
			if ($category_id)
			{
				$category = ORM::factory('category')
					->find($category_id);
				if ($category->loaded)
				{
					$this->template->header->breadcrumbs = "&nbsp;&raquo;&nbsp;<a href=\"".url::site()."mobile/reports/index/".$category_id."?page=".$page_no."\">".$category->category_title."</a>";
				}
			}
		}
	}
	
	public function submit($saved = false)
	{
		// Cacheable Controller
		$this->is_cachable = FALSE;
		
		$this->template->header->show_map = TRUE;
		$this->template->content  = new View('mobile/reports_submit');
		
		// First, are we allowed to submit new reports?
		if ( ! Kohana::config('settings.allow_reports'))
		{
			url::redirect(url::site().'main');
		}

		// setup and initialize form field names
		$form = array
		(
			'incident_title' => '',
			'incident_description' => '',
			'incident_month' => '',
			'incident_day' => '',
			'incident_year' => '',
			'incident_hour' => '',
			'incident_minute' => '',
			'incident_ampm' => '',
			'latitude' => '',
			'longitude' => '',
			'location_name' => '',
			'country_id' => '',
			'incident_category' => array(),
		);
		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;

		if ($saved == 'saved')
		{
			$form_saved = TRUE;
		}
		else
		{
			$form_saved = FALSE;
		}


		// Initialize Default Values
		$form['incident_month'] = date('m');
		$form['incident_day'] = date('d');
		$form['incident_year'] = date('Y');
		$form['incident_hour'] = date('h');
		$form['incident_minute'] = date('i');
		$form['incident_ampm'] = date('a');
		// initialize custom field array
		// $form['custom_field'] = $this->_get_custom_form_fields($id,'',true);
		//GET custom forms
		//$forms = array();
		//foreach (ORM::factory('form')->find_all() as $custom_forms)
		//{
		//	$forms[$custom_forms->id] = $custom_forms->form_title;
		//}
		//$this->template->content->forms = $forms;
		
		
		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = Validation::factory(array_merge($_POST,$_FILES));

			 //  Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('incident_title', 'required', 'length[3,200]');
			$post->add_rules('incident_description', 'required');
			$post->add_rules('incident_month', 'required', 'numeric', 'between[1,12]');
			$post->add_rules('incident_day', 'required', 'numeric', 'between[1,31]');
			$post->add_rules('incident_year', 'required', 'numeric', 'length[4,4]');
			
			if ( ! checkdate($_POST['incident_month'], $_POST['incident_day'], $_POST['incident_year']) )
			{
				$post->add_error('incident_date','date_mmddyyyy');
			}
			
			$post->add_rules('incident_hour', 'required', 'between[1,12]');
			$post->add_rules('incident_minute', 'required', 'between[0,59]');

			if ($_POST['incident_ampm'] != "am" && $_POST['incident_ampm'] != "pm")
			{
				$post->add_error('incident_ampm','values');
			}

			// Validate for maximum and minimum latitude values
			$post->add_rules('latitude', 'between[-90,90]');
			$post->add_rules('longitude', 'between[-180,180]');
			$post->add_rules('location_name', 'required', 'length[3,200]');

			//XXX: Hack to validate for no checkboxes checked
			if (!isset($_POST['incident_category'])) {
				$post->incident_category = "";
				$post->add_error('incident_category', 'required');
			}
			else
			{
				$post->add_rules('incident_category.*', 'required', 'numeric');
			}
			
			// Geocode Location
			if ( empty($_POST['latitude']) AND empty($_POST['longitude']) 
				AND ! empty($_POST['location_name']) )
			{
				$default_country = Kohana::config('settings.default_country');
				$country_name = "";
				if ($default_country)
				{
					$country = ORM::factory('country', $default_country);
					if ($country->loaded)
					{
						$country_name = $country->country;
					}
				}
				
				$geocode = mobile_geocoder::geocode($_POST['location_name'].", ".$country_name);
				if ($geocode)
				{
					$post->latitude = $geocode['lat'];
					$post->longitude = $geocode['lon'];
				}
				else
				{
					$post->add_error('location_name', 'geocode');
				}
			}

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				if ($post->latitude AND $post->longitude)
				{
					// STEP 1: SAVE LOCATION
					$location = new Location_Model();
					$location->location_name = $post->location_name;
					$location->latitude = $post->latitude;
					$location->longitude = $post->longitude;
					$location->location_date = date("Y-m-d H:i:s",time());
					$location->save();
				}
				
				// STEP 2: SAVE INCIDENT
				$incident = new Incident_Model();
				if (isset($location) AND $location->loaded)
				{
					$incident->location_id = $location->id;
				}
				$incident->user_id = 0;
				$incident->incident_title = $post->incident_title;
				$incident->incident_description = $post->incident_description;

				$incident_date = $post->incident_year."-".$post->incident_month."-".$post->incident_day;
				$incident_time = $post->incident_hour
					.":".$post->incident_minute
					.":00 ".$post->incident_ampm;
				$incident->incident_date = date( "Y-m-d H:i:s", strtotime($incident_date . " " . $incident_time) );				
				$incident->incident_dateadd = date("Y-m-d H:i:s",time());
				$incident->save();

				// STEP 3: SAVE CATEGORIES
				foreach($post->incident_category as $item)
				{
					$incident_category = new Incident_Category_Model();
					$incident_category->incident_id = $incident->id;
					$incident_category->category_id = $item;
					$incident_category->save();
				}
				
				url::redirect('reports/thanks');
				
			}
			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('mobile_report'));
				$form_error = TRUE;
			}
			
		}
		
		
		
		
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->categories = $this->_get_categories($form['incident_category']);
		
		$this->template->content->cities = $this->_get_cities();
		
		$this->template->header->js = new View('mobile/reports_submit_js');
		if (!$form['latitude'] || !$form['latitude'])
		{
			$this->template->header->js->latitude = Kohana::config('settings.default_lat');
			$this->template->header->js->longitude = Kohana::config('settings.default_lon');
		}else{
			$this->template->header->js->latitude = $form['latitude'];
			$this->template->header->js->longitude = $form['longitude'];
		}
	}

	/**
	 * Report Thanks Page
	 */
	function thanks()
	{
		$this->template->header->show_map = FALSE;
		$this->template->content = new View('mobile/reports_submit_thanks');
	}

	/*
	 * Retrieves Categories
	 */
	private function _get_categories($selected_categories)
	{
		$categories = ORM::factory('category')
			->where('category_visible', '1')
			->where('parent_id', '0')
			->where('category_trusted != 1')
			->orderby('category_title', 'ASC')
			->find_all();

		return $categories;
	}
	
	
	/*
	 * Retrieves Cities
	 */
	private function _get_cities()
	{
		$cities = ORM::factory('city')->orderby('city', 'asc')->find_all();
		$city_select = array('' => Kohana::lang('ui_main.reports_select_city'));

		foreach ($cities as $city)
		{
			$city_select[$city->city_lon.",".$city->city_lat] = $city->city;
		}

		return $city_select;
	}
}
