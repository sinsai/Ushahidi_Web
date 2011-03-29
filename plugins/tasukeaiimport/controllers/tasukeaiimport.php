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

class Tasukeaiimport_Controller extends Template_Controller {
	// Cacheable Controller
	public $is_cachable = FALSE;
	public $template = 'tasukeaiimport/layout';
    public function __construct()
    {
		parent::__construct();
	}
	
    public function index()
    {
        $apiurl = "http://tasukeai.heroku.com/all.xml";
        #$apiurl = "http://localhost/message.xml";
        $messages = simplexml_load_file($apiurl);
        foreach ($messages as $message) {
                $title = "";
                $lat = "";
                $active = 1;
                $long = "";
                $matches = array();
                if(preg_match("/\s*\[ボランティア名称\]\s*\n([^\n]+)\n/", $message->body, $matches)){
                    $title = $matches[1];
                }else if(preg_match("/\s*\[主催\]\s*([^\n]+)\n/", $message->body, $matches)){
                    $title = $matches[1];
                }else{
                    $title = "無題";
                    $active = 0;
                }
                if(preg_match("/\s*\[緯度経度\]\s*\n([^,]+),([^\n]+)/", $message->body, $matches)){
                    $lat = $matches[1];
                    $long = $matches[2];
                }
                $link = $this->input->xss_clean($message->link);
                $where_string = "media_link = '".$link."'";
                $db = new Database();
                $count = $db->count_records('media',$where_string);
                if($count > 0){
                    if(strcmp($message->{"valid-f"},"false") == 0){
                        $search_query = "SELECT incident_id FROM media".
                                    " WHERE (".$where_string.")";
                        $query = $db->query($search_query);
                        ORM::factory('Incident')->where('id',$query[0]->incident_id)->delete_all();
                        ORM::factory('Media')->where('incident_id',$query[0]->incident_id)->delete_all();
                    }
                    continue;
                }
                if(strcmp($message->{"valid-f"},"true") != 0){
                    continue;
                }
                $incident = new Incident_Model();
                
                // STEP 1: SAVE LOCATION
                if(isset($lat) && isset($long)){
                    $location = new Location_Model("");
                    $location->location_name = $message->address." ";
                    $location->latitude = $lat;
                    $location->longitude = $long;
                    $location->location_date = date("Y-m-d H:i:s",time());
                    
                    $location->save();
                    $incident->location_id = $location->id;
                    
                }
                $incident->incident_title = $title;
                $incident->incident_description = $message->body." ";
                
                
                $incident->incident_date = date( "Y-m-d H:i:s", strtotime($message->{"created-at"}) );
                $incident->incident_dateadd = date("Y-m-d H:i:s",time());
                $incident->incident_mode = 1;
                $incident->incident_active = $active;
                $incident->incident_verified = 1;
                $incident->incident_source = 3;
                $incident->incident_information = 1;
                //Save
                $incident->save();
                
                $news = new Media_Model();
                $news->incident_id = $incident->id;
                if(isset($location)){
                    $news->location_id = $location->id;
                }
                $news->media_type = 4;      // News
                $news->media_link = $link;
                $news->media_date = date("Y-m-d H:i:s",strtotime($message->{"created-at"}));
                $news->save();
                
                
                $incident_category = new Incident_Category_Model();
                $incident_category->incident_id = $incident->id;
                $incident_category->category_id = 13; //求む
                $incident_category->save();

        }
        $this->template->content  = new View('tasukeaiimport/main');
    }
}
