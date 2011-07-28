<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Incidents_Api_Object
 *
 * This class handles reports activities via the API.
 *
 * @version 26 - Emmanuel Kala 2010-10-22
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Incidents_Api_Object extends Api_Object_Core {

    private $sort; // Sort descriptor ASC or DESC
    private $order_field; // Column name by which to order the records    
    
    public function __construct($api_service)
    {
        parent::__construct($api_service);
    }
    
    /**
     * Implementation of abstract method in parent
     *
     * Handles the API task parameters
     */
    public function perform_task()
    {
        // Check if the 'by' parameter has been specified
        if ( ! $this->api_service->verify_array_index($this->request, 'by'))
        {
            // Set "all" as the default method for fetching incidents
            $this->by = 'all';
        }
        else
        {
            $this->by = $this->request['by'];
        }

        // Check optional parameters
        $this->_check_optional_parameters();
        
        // Begin task switching
        switch ($this->by)
        {
            // Get all incidents
            case "all":
                $this->response_data = $this->get_incidents_by_all();
            break;
            
            // Get specific incident by ID
            case "incidentid":
                if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_incident_by_id(
                            $this->check_id_value($this->request['id']));
                }
            break;
            
            // Get incidents by latitude and longitude
            case "latlon":
                if ($this->api_service->verify_array_index($this->request, 'latitude')
                    AND $this->api_service->verify_array_index($this->request, 'longitude'))
                { 
                    $this->response_data = $this->_get_incidents_by_lat_lon(
                        $this->check_id_value($this->request['latitude']),
                        $this->check_id_value($this->request['longitude']));
                }
                else
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'latitude or longitude')
                    ));
                    
                    return;
                }
            break;
            
            // Get incidents by latitude and longitude distance
            case "dist":
                if ($this->api_service->verify_array_index($this->request, 'distance'))
                { 
                    $this->response_data = $this->_get_incidents_by_distance($this->request['distance']);
                }
                else
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'distance')
                    ));
                    
                    return;
                }
            break;
            
            // Get incidents by location id
            case "locid":
                if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_incidents_by_location_id($this->check_id_value($this->request['id']));
                }
            break;
            
            // Get incidents by location name
            case "locname":
                if ( ! $this->api_service->verify_array_index($this->request, 'name'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'name') 
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_incidents_by_location_name($this->request['name']);
                }
            break;
            
            // Get incidents by category id
            case "catid":
                if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_incidents_by_category_id($this->check_id_value($this->request['id']));
                }
            break;
            
            // Get incidents by category name
            case "catname":
                if ( ! $this->api_service->verify_array_index($this->request, 'name'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'name')
                    ));
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_incidents_by_category_name($this->request['name']);
                }
            break;
            
            // Get incidents greater than a specific incident_id in the DB
            case "sinceid":
                if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_incidents_by_since_id($this->check_id_value($this->request['id']));
                }
            break;
            
            // Get incidents less that a specific incident_id
            case "maxid":
                if ( ! $this->api_service->verify_array_index($this->request, 'id'))
                {
                    $this->set_error_message(array(
                        "error" => $this->api_service->get_error_msg(001, 'id')
                    ));
                    
                    return;
                }
                else
                {
                    $this->response_data = $this->_get_incidents_by_max_id($this->check_id_value($this->request['id']));
                }
            break;
            
            // Get incidents based on a box using two lat,lon coords
            case "bounds":
                if(!isset($this->request['keyword'])){
                    $this->request['keyword'] = "";
                }
                $this->response_data = $this->_get_incidents_by_bounds($this->request['sw'],$this->request['ne'],$this->request['c'],$this->request['keyword']);
            break;
            
            case "keyword":
                    $this->response_data = $this->_get_incidents_by_keyword($this->request['c'],$this->request['keyword'],$this->request['distance']);
            break;
            // Error therefore set error message 
            default:
                $this->set_error_message(array(
                    "error" => $this->api_service->get_error_msg(002)
                ));
        }
    }

    /**
     * Checks for optional parameters in the request and sets the values
     * in the respective class members
     */
    private function _check_optional_parameters()
    {
        // Check if the sort parameter has been specified
        if ($this->api_service->verify_array_index($this->request, 'sort'))
        {
            $this->sort = ($this->request['sort'] == '0') ? 'ASC' : 'DESC';
        }
        else
        {
            $this->sort = 'DESC';
        }
        
        // Check if the limit parameter has been specified
        if ($this->api_service->verify_array_index($this->request, 'limit'))
        {
            $this->set_list_limit($this->request['limit']);
        }               
        
        if ($this->api_service->verify_array_index($this->request, 'offset'))
        {
            $this->set_list_offset($this->request['offset']);
        } 
        // Check if the orderfield parameter has been specified
        if ($this->api_service->verify_array_index($this->request, 'orderfield'))
        {
            switch ($this->request['orderfield'])
            {
                case "incidentid":
                    $this->order_field = 'i.id';
                break;
                
                case "locationid":
                    $this->order_field = 'i.location_id';
                break;
                
                case "incidentdate":
                    $this->order_field = 'i.incident_date';
                break;
                
                default:
                    $this->order_field = 'i.incident_date';
            }
        }
        else
        {
            $this->order_field = 'i.incident_date';
        }
    }
    
    /**
     * Generic function to get reports by given set of parameters
     */
    public function _get_incidents($where = '',$limit = '')
    {
        $ret_json_or_xml = ''; // Will hold the XML/JSON string to return
        
        $json_reports = array();
        $json_report_media = array();
        $json_report_categories = array();
        $json_incident_media = array();
        $upload_path = str_replace("media/uploads/", "", Kohana::config('upload.relative_directory')."/");        
        //XML elements
        $xml = new XmlWriter();
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('response');
        $xml->startElement('payload');
        $xml->writeElement('domain',$this->domain);
        $xml->startElement('incidents');

        // Find incidents
        $this->query = "SELECT i.id AS incidentid,
                i.incident_title AS incidenttitle,"
                ."i.incident_description AS incidentdescription, "
                ."i.incident_date AS incidentdate, "
                ."i.incident_mode AS incidentmode, "
                ."i.incident_active AS incidentactive, "
                ."i.incident_verified AS incidentverified, "
                ."l.id AS locationid, "
                ."l.location_name AS locationname, "
                ."l.latitude AS locationlatitude, "
                ."l.longitude AS locationlongitude "
                ."FROM ".$this->table_prefix."s_incident AS i "
                ."INNER JOIN ".$this->table_prefix.
                "location as l on l.id = i.location_id "."$where $limit";
        $items = $this->db->query($this->query);
        // Set the no. of records returned
        $this->record_count = $items->count();

        $incidentids = array();
        foreach($items as $item){
			$incidentids[] = $item->incidentid;
        }

        // Fetch categories
        $this->query = " SELECT c.category_title AS categorytitle, 
            c.id as cid ,ic.incident_id as incidentid " . "FROM ".$this->table_prefix.
            "category AS c INNER JOIN ".
            $this->table_prefix."incident_category AS ic ON " .
            "ic.category_id = c.id WHERE ic.incident_id IN(".implode(',',$incidentids).")";

        $category_items_temp = $this->db->query( $this->query );
        $category_items = array();
        $temp_index = 1;
        foreach($category_items_temp as $category_item){
            $category_items[$category_item->incidentid][$temp_index]['categorytitle'] = $category_item->categorytitle;
            $category_items[$category_item->incidentid][$temp_index]['cid'] = $category_item->cid;
            $temp_index++;
        }
        unset($category_items_temp);
        //fetch media associated with an incident
        $this->query = "SELECT i.id as incidentid , m.id as mediaid, m.media_title AS 
            mediatitle, " .
            "m.media_type AS mediatype, m.media_link AS medialink, " .
            "m.media_thumb AS mediathumb FROM ".$this->table_prefix.
            "media AS m " . "INNER JOIN ".$this->table_prefix.
            "s_incident AS i ON i.id = m.incident_id " .
            "WHERE i.id IN(".implode(',',$incidentids).")";

        $media_items = $this->db->query($this->query);
        $media_items_temp = $this->db->query( $this->query );
        $media_items = array();
        $temp_index = 1;
        foreach($media_items_temp as $media_item){
            $media_items[$media_item->incidentid][$temp_index]['mediaid'] = $media_item->mediaid;
            $media_items[$media_item->incidentid][$temp_index]['mediatitle'] = $media_item->mediatitle;
            $media_items[$media_item->incidentid][$temp_index]['mediatype'] = $media_item->mediatype;
            $media_items[$media_item->incidentid][$temp_index]['medialink'] = $media_item->medialink;
            $media_items[$media_item->incidentid][$temp_index]['mediathumb'] = $media_item->mediathumb;
            $temp_index++;
        }
        unset($media_items_temp);
        unset($temp_index);


        $i = 0;
        
        //No record found.
        if ($items->count() == 0)
        {
            return $this->response(4, $this->error_messages);
        }

        foreach ($items as $item)
        {
            // Build xml file
            $xml->startElement('incident');

            $xml->writeElement('id',$item->incidentid);
            $xml->writeElement('title',$item->incidenttitle);
            $xml->writeElement('description',$item->incidentdescription);
            $xml->writeElement('date',$item->incidentdate);
            $xml->writeElement('mode',$item->incidentmode);
            $xml->writeElement('active',$item->incidentactive);
            $xml->writeElement('verified',$item->incidentverified);
            $xml->startElement('location');
            $xml->writeElement('id',$item->locationid);
            $xml->writeElement('name',$item->locationname);
            $xml->writeElement('latitude',$item->locationlatitude);
            $xml->writeElement('longitude',$item->locationlongitude);
            $xml->endElement();
            $xml->startElement('categories');
            $json_report_categories[$item->incidentid] = array();           
            foreach ($category_items[$item->incidentid] as $category_item)
            {
                if ($this->response_type == 'json')
                {
                    $json_report_categories[$item->incidentid][] = array(
                            "category"=> array(
                                "id" => $category_item['cid'],
                                "title" => $category_item['categorytitle']
                            )
                        );
                } 
                else 
                {
                    $xml->startElement('category');
                    $xml->writeElement('id',$category_item['cid']);
                    $xml->writeElement('title', $category_item['categorytitle'] );
                    $xml->endElement();
                }
                
            }

            $xml->endElement();//end categories

            $json_report_media[$item->incidentid] = array();

            if (isset($media_items[$item->incidentid]) && count($media_items[$item->incidentid]) > 0)
            {
                $xml->startElement('mediaItems');
                
                foreach ($media_items[$item->incidentid] as $media_item)
                {
	                if ($media_item['mediatype'] != 1)
					{
                        $upload_path = "";
                    }

                    if($this->response_type == 'json')
                    {	
                        $json_report_media[$item->incidentid] = array(
                            "id" => $media_item['mediaid'],
                            "type" => $media_item['mediatype'],
                            "link" => $upload_path.$media_item['medialink'],
                            "thumb" => $upload_path.$media_item['mediathumb'],
                        );
                    } 
                    else 
                    {
                        $xml->startElement('media');
                        
                        if( $media_item['mediaid'] != "" )
                        {
                            $xml->writeElement('id',$media_item['mediaid']);
                        }

                        if( $media_item['mediatitle'] != "" )
                        {
                            $xml->writeElement('title',
                                $media_item['mediatitle']);
                        }

                        if( $media_item['mediatype'] != "" )
                        {
                            $xml->writeElement('type',
                                $media_item['mediatype']);
                        }

                        if( $media_item['medialink'] != "" ) 
                        {
                            $xml->writeElement('link',
                                $upload_path.$media_item['medialink']);
                        }

                        if( $media_item['mediathumb'] != "" ) 
                        {
                            $xml->writeElement('thumb',
                                $upload_path.$media_item['mediathumb']);
                        }

                        $xml->endElement();
                    }
                }

                $xml->endElement(); // media

            }

            $xml->endElement(); // end incident

            //needs different treatment depending on the output
            if ($this->response_type == 'json')
            {
                $json_reports[] = array(
                    "incident" => $item, 
                    "categories" => $json_report_categories[$item->incidentid], 
                    "media" => $json_report_media[$item->incidentid]
                );
            }
        }

        //create the json array
        $data = array(
            "payload" => array(
                "domain" => $this->domain,
                "incidents" => $json_reports
            ),
            "error" => $this->api_service->get_error_msg(0)
        );

        if ($this->response_type == 'json')
        {
            $ret_json_or_xml = $this->array_as_json($data);
            
            return $ret_json_or_xml;
        } 
        else 
        {
            $xml->endElement(); //end incidents
            $xml->endElement(); // end payload
            $xml->startElement('error');
            $xml->writeElement('code',0);
            $xml->writeElement('message','No Error');
            $xml->endElement();//end error
            $xml->endElement(); // end response
            return $xml->outputMemory(true);
        }

    }

    /**
     * Fetch all incidents
     * 
     * @param string orderfield - the order in which to order query output
     * @param string sort
     */
    private function get_incidents_by_all() 
    {
        $where = "\nWHERE i.incident_active = 1 ";
        
        $sortby = "\nGROUP BY i.id ORDER BY $this->order_field $this->sort";
        
        $limit = "\nLIMIT $this->list_offset, $this->list_limit";

        /* Not elegant but works */
        return $this->_get_incidents($where.$sortby, $limit);
    }
    
    /**
     * Get the incidents by latitude and longitude.
     * 
     */
    private function _get_incidents_by_lat_lon($lat, $long)
    {
        
        $where = "\nWHERE l.latitude = $lat AND l.longitude = $long AND 
            i.incident_active = 1 ";
        
        $sortby = "\nORDER BY $this->order_field $this->sort ";
        
        $limit = "\n LIMIT $this->list_offset, $this->list_limit";
        
        return $this->_get_incidents($where.$sortby, $limit);
    }
    private function _get_incidents_by_distance($distance)
    /**
     * Get the incidents by latitude and longitude distance.
     * 
     */
    {
        $param = explode(',',$distance);

        $where = "\nWHERE round(sqrt(pow((l.latitude - $param[1])/0.0111, 2) + pow((l.longitude - $param[0])/0.0091, 2)), 1) <= $param[2] AND i.incident_active = 1 ";
        if(!isset($this->request['orderfield']) && !isset($this->request['sort'])){
            $sortby = "\nORDER BY round(sqrt(pow((l.latitude - $param[1])/0.0111, 2) + pow((l.longitude - $param[0])/0.0091, 2)), 1) ASC ";
        }elseif(!isset($this->order_field) && isset($this->request['sort'])){
            $sortby = "\nORDER BY round(sqrt(pow((l.latitude - $param[1])/0.0111, 2) + pow((l.longitude - $param[0])/0.0091, 2)), 1) $this->sort ";
		}else{
            $sortby = "\nORDER BY $this->order_field $this->sort ";
        }
        
        $limit = "\n LIMIT $this->list_offset, $this->list_limit";
        
        return $this->_get_incidents($where.$sortby, $limit);
    }
    
    private function _get_incidents_by_keyword($catid,$keyword,$distance)
    /**
     * Get the incidents by keyword
     * 
     */
    {
        $param = explode(',',$distance);
        $join = "\nINNER JOIN ".$this->table_prefix."incident_category AS 
            ic ON ic.incident_id = i.id";
        
        $join .= "\nINNER JOIN ".$this->table_prefix."category AS c ON 
            c.id = ic.category_id ";
      
        if( intval($catid) != 0){
            $where = $join."\nWHERE c.id = $catid AND";
        }else{
            $where = "\nWHERE ";
        }
        
        $keyword_raw = (isset($keyword))? mysql_real_escape_string($keyword) : "";          
        $keyword_raw = strip_tags($keyword_raw);
        $keyword_raw =  Input::instance()->xss_clean($keyword_raw);
        $keywords = explode(' ', $keyword_raw);
        
        if (is_array($keywords) && !empty($keywords) && $keyword_raw != "") 
        {
            $match = " MATCH(i.incident_text) AGAINST(\"$keyword_raw\" IN BOOLEAN MODE) AND";
            $where .= $match;
        }
        $where .=  "\nround(sqrt(pow((l.latitude - $param[1])/0.0111, 2) + pow((l.longitude - $param[0])/0.0091, 2)), 1) <= $param[2] AND i.incident_active = 1 ";
        if(!isset($this->request['orderfield']) && !isset($this->request['sort'])){
            $sortby = "\nORDER BY round(sqrt(pow((l.latitude - $param[1])/0.0111, 2) + pow((l.longitude - $param[0])/0.0091, 2)), 1) ASC ";
        }elseif(!isset($this->order_field) && isset($this->request['sort'])){
            $sortby = "\nORDER BY round(sqrt(pow((l.latitude - $param[1])/0.0111, 2) + pow((l.longitude - $param[0])/0.0091, 2)), 1) $this->sort ";
		}else{
            $sortby = "\nORDER BY $this->order_field $this->sort ";
        }
        $limit = "\n LIMIT $this->list_offset, $this->list_limit";
        
        return $this->_get_incidents($where.$sortby, $limit);
    }
    

    /**
     * Get the incidents by location id
     */
    private function _get_incidents_by_location_id($locid)
    {
        $where = "\nWHERE i.location_id = $locid AND i.incident_active = 1 ";
        
        $sortby = "\nGROUP BY i.id ORDER BY $this->order_field $this->sort";
        
        $limit = "\nLIMIT $this->list_offset, $this->list_limit";
        
        return $this->_get_incidents($where.$sortby, $limit);
    }

    /**
     * Get the incidents by location name
     */
    private function _get_incidents_by_location_name($locname)
    {
        $where = "\nWHERE l.location_name = '$locname' AND
                i.incident_active = 1 ";
        
        $sortby = "\nGROUP BY i.id ORDER BY $this->order_field $this->sort";
        
        $limit = "\nLIMIT $this->list_offset, $this->list_limit";
        
        return $this->_get_incidents($where.$sortby, $limit);
    }

    /**
     * Get the incidents by category id
     */
    private function _get_incidents_by_category_id($catid)
    {
        // Needs Extra Join
        $join = "\nINNER JOIN ".$this->table_prefix."incident_category AS 
            ic ON ic.incident_id = i.id";
        
        $join .= "\nINNER JOIN ".$this->table_prefix."category AS c ON 
            c.id = ic.category_id ";
        
        $where = $join."\nWHERE c.id = $catid AND i.incident_active = 1";
        
        $sortby = "\nORDER BY $this->order_field $this->sort";
        
        $limit = "\nLIMIT $this->list_offset, $this->list_limit";
        
        return $this->_get_incidents($where.$sortby, $limit);
    }

    /**
     * Get the incidents by category name
     */
    private function _get_incidents_by_category_name($catname)
    {
        // Needs Extra Join
        $join = "\nINNER JOIN ".$this->table_prefix."incident_category AS 
            ic ON ic.incident_id = i.id";

        $join .= "\nINNER JOIN ".$this->table_prefix."category AS c ON 
            c.id = ic.category_id";
        
        $where = $join."\nWHERE c.category_title LIKE '%$catname%' AND
                i.incident_active = 1";
        
        $sortby = "\nORDER BY $this->order_field $this->sort";
        
        $limit = "\nLIMIT $this->list_offset, $this->list_limit";
        
        return $this->_get_incidents($where.$sortby, $limit);
    }
    
    /**
     * Get a single incident by its ID in the database
     * @param incident_id ID of the incident in the databases
     */
    private function _get_incident_by_id($incident_id)
    {
        $where = "\nWHERE i.id = $incident_id AND i.incident_active = 1 ";
        
        return $this->_get_incidents($where);
    }

    /**
     * Get the incidents by since an incidents was updated
     *
     * @param since_id Database id from which incidents are to be fetched
     */
    private function _get_incidents_by_since_id($since_id)
    {
        // Needs Extra Join
        $join = "\nINNER JOIN ".$this->table_prefix."incident_category AS 
            ic ON ic.incident_id = i.id";
            
        $join .= "\nINNER JOIN ".$this->table_prefix.
            "category AS c ON c.id = ic.category_id";
            
        $where = $join."\nWHERE i.id > $since_id AND
                i.incident_active = 1";
                
        $sortby = "\nGROUP BY i.id ORDER BY $this->order_field $this->sort";
        $limit = "\nLIMIT $this->list_offset, $this->list_limit";
        
        return $this->_get_incidents($where.$sortby, $limit);
    }
    
    /**
     * Get incidents with a database id less than then one specified in $max_id
     *
     * @param max_id Maximum incident id
     */
    private function _get_incidents_by_max_id($max_id)
    {
        // Needs Extra Join
        $join = "\nINNER JOIN ".$this->table_prefix."incident_category AS 
            ic ON ic.incident_id = i.id";
            
        $join .= "\nINNER JOIN ".$this->table_prefix.
            "category AS c ON c.id = ic.category_id";
            
        $where = $join."\nWHERE i.id < $max_id AND
                i.incident_active = 1";
                
        $sortby = "\nGROUP BY i.id ORDER BY $this->order_field $this->sort";
        $limit = "\nLIMIT $this->list_offset, $this->list_limit";
        
        return $this->_get_incidents($where.$sortby, $limit);
        
    }

    /**
     * Get incidents within a certain lat,lon bounding box
     *
     * @param sw is the southwest lat,lon of the box
     * @param ne is the northeast lat,lon of the box
     * @param c is the categoryid
     */
    private function _get_incidents_by_bounds($sw, $ne, $c = 0,$keyword = "")
    {
		// Get location_ids if we are to filter by location
		$location_ids = array();

		// Break apart location variables, if necessary
		$southwest = array();
		if (isset($sw))
		{
			$southwest = explode(",",$sw);
		}

		$northeast = array();
		if (isset($ne))
		{
			$northeast = explode(",",$ne);
		}

		if ( count($southwest) == 2 AND count($northeast) == 2 )
		{
			$lon_min = (float) $southwest[0];
			$lon_max = (float) $northeast[0];
			$lat_min = (float) $southwest[1];
			$lat_max = (float) $northeast[1];

			$query = 'SELECT id FROM '.$this->table_prefix.'location WHERE latitude >='.$lat_min.' AND latitude <='.$lat_max.' AND longitude >='.$lon_min.' AND longitude <='.$lon_max;

			$items = $this->db->query($query);

			foreach ( $items as $item )
			{
				$location_ids[] =  $item->id;
			}
		}
		
		$location_id_in = '1=1';
		
		if (count($location_ids) > 0)
		{
			$location_id_in = 'l.id IN ('.implode(',',$location_ids).')';
		}

        $keyword_raw = (isset($keyword))? mysql_real_escape_string($keyword) : "";          
        $keyword_raw = strip_tags($keyword_raw);
        $keyword_raw =  Input::instance()->xss_clean($keyword_raw);
        $keywords = explode(' ', $keyword_raw);
        $match = "";
        if (is_array($keywords) && !empty($keywords) && $keyword_raw != "") 
        {
            $match = " MATCH(i.incident_text) AGAINST(\"$keyword_raw\" IN BOOLEAN MODE) AND";
        }

		$where = ' WHERE '.$match.' i.incident_active = 1 AND '.$location_id_in.' ';

		// Fix for pulling categories using the bounding box
		// Credits to Antonoio Lettieri http://github.com/alettieri
		// Check if the specified category id is valid
		if (Category_Model::is_valid_category($c))
		{
			// Filter incidents by the specified category
			$join = "\nINNER JOIN ".$this->table_prefix."incident_category AS ic ON ic.incident_id = i.id ";
			$join .= "\nINNER JOIN ".$this->table_prefix."category AS c ON c.id=ic.category_id ";

			// Overwrite the current where clause in $where
			$where = $join."\nWHERE $match c.id = $c AND i.incident_active = 1 AND $location_id_in";
		}
		
		$sortby = " GROUP BY i.id ORDER BY $this->order_field $this->sort";
		$limit = " LIMIT $this->list_offset, $this->list_limit";
		
		return $this->_get_incidents($where.$sortby, $limit);
        
    }


    /**
     * Gets the number of approved reports
     * 
     * @param string response_type - XML or JSON
     *
     * @return string
     */
    public function get_incident_count()
    {
    
        $json_count = array();
        $ret_json_or_xml = ''; // Will hold the XML/JSON string to return
        
        $this->query = 'SELECT COUNT(*) as count FROM '.
            $this->table_prefix.
            'incident WHERE incident_active = 1';

        $items = $this->db->query($this->query);

        foreach ($items as $item)
        {
            $count = $item->count;
            break;
        }

        if ($this->response_type == 'json')
        {
            $json_count[] = array("count" => $count);
        }
        else
        {
            $json_count['count'] = array("count" => $count);
            $this->replar[] = 'count';
        }

        //create the json array
        $data = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "count" => $json_count
                ),
                "error" => $this->api_service->get_error_msg(0));

        if ($this->response_type == 'json') 
        {
            $ret_json_or_xml = $this->array_as_json($data);
        }
        else
        {
            $ret_json_or_xml = $this->array_as_xml($data, $this->replar);
        }

        $this->response_data =  $ret_json_or_xml;
    }
    
    /**
     * Get an approximate geographic midpoint of al approved reports.
     *
     * @params string response_type - XML or JSON
     *
     * @return string
     */
    public function get_geographic_midpoint()
    {
        $json_latlon = array();

        $this->query = 'SELECT AVG( latitude ) AS avglat, AVG( longitude ) 
            AS avglon FROM '.$this->table_prefix.'location WHERE id IN 
            (SELECT location_id FROM '.$this->table_prefix.'incident WHERE 
             incident_active = 1)';
        
        $items = $this->db->query($this->query);

        foreach ($items as $item)
        {
            $latitude = $item->avglat;
            $longitude = $item->avglon;
            break;
        }

        if ($this->response_type == 'json')
        {
            $json_latlon[] = array(
                "latitude" => $latitude, 
                "longitude" => $longitude
            );
        }
        else
        {
            $json_latlon['geographic_midpoint'] = array(
                "latitude" => $latitude, 
                "longitude" => $longitude
            );

            $replar[] = 'geographic_midpoint';
        }

        //create the json array
        $data = array(
                "payload" => array(
                    "domain" => $this->domain,
                    "geographic_midpoint" => $json_latlon
                ),
                "error" => $this->api_service->get_error_msg(0)
        );
        
        // Return data
        $this->response_data =  ($this->response_type == 'json')
            ? $this->array_as_json($data)
            : $this->array_as_xml($data, $replar);
    }

}
