<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Messages Controller.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Messages Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Messages_Controller extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->template->this_page = 'messages';
        
        // If user doesn't have access, redirect to dashboard
        if ( ! admin::permissions($this->user, "messages"))
        {
            url::redirect(url::site().'admin/dashboard');
        }
    }

    /**
    * Lists the messages.
    * @param int $service_id
    */
    function index($service_id = 2)
    {
        $this->template->content = new View('admin/messages');

        // Get Title
        $service = ORM::factory('service', $service_id);
        $this->template->content->title = $service->service_name;

        // Display Reply to Option?
        $this->template->content->reply_to = TRUE;
        if ( ! Kohana::config("settings.sms_provider"))
        {
            // Hide Reply to option
			$this->template->content->reply_to = FALSE;
        }

		$r_from = "";
		if( isset($_GET['from']) )
		{
			$r_from = $this->input->xss_clean($_GET['from']);
		}
		$r_to = "";
		if( isset($_GET['to']) )
		{
			$r_to = $this->input->xss_clean($_GET['to']);
		}

		$filter_range = "";
		if( isset($r_from) && empty($r_to) )
		{
			$filter_range = "message_date between \"".date("Y-m-d",strtotime($r_from))." 00:00:00\" and \"".date("Y-m-d")." 23:59:00\"";
		} elseif( isset($r_from) && isset($r_to) )
		{
			$filter_range = "message_date between \"".date("Y-m-d",strtotime($r_from))." 00:00:00\" and \"".date("Y-m-d",strtotime($r_to))." 23:59:00\"";
		} elseif( empty($r_from) && isset($r_to) )
		{
			$filter_range = "message_date between \"".date("Y-m-d",1)." 00:00:00\" and \"".date("Y-m-d",strtotime($r_to))." 23:59:00\"";
		}

        // Is this an Inbox or Outbox Filter?
        if (!empty($_GET['type']))
        {
            $type = $_GET['type'];

            if ($type == '2')
            { // OUTBOX
                $filter = 'message_type = 2';
            }
            else
            { // INBOX
                $type = "1";
                $filter = 'message_type = 1';
            }
        }
        else
        {
            $type = "1";
            $filter = 'message_type = 1';
        }
        
        // Do we have a reporter ID?
        if (isset($_GET['rid']) AND !empty($_GET['rid']))
        {
            $filter .= ' AND reporter_id=\''.$_GET['rid'].'\'';
        }
        
        // ALL / Trusted / Spam
        $level = '0';
        if (isset($_GET['level']) AND !empty($_GET['level']))
        {
            $level = $_GET['level'];
            if ($level == 4)
            {
                $filter .= " AND ( reporter.level_id = '4' OR reporter.level_id = '5' ) AND ( message.message_level != '99' ) ";
            }
            elseif ($level == 2)
            {
                $filter .= " AND ( message.message_level = '99' ) ";
            }
        }

        // filter by type
        $filter_type = array();
		$r_filter="";
        if(!empty($_GET['filter'])) {
		  $r_filter = $this->input->xss_clean($_GET['filter']);
          $filter_form = preg_split('/, ?/', $_GET['filter']);
          foreach($filter_form as $filter_str) {
            $filter_sql = "message.type ";
            switch($filter_str) {
              case "junk":
                $filter_sql .= '> 0';
                break;
              case "rt":
                $filter_sql .= "= 1";
                break;
              case "live":
                $filter_sql .= "= 2";
                break;
              case "news":
                $filter_sql .= "= 3";
                break;
              case "junkstr":
                $filter_sql .= "= 4";
                break;
              case "auto":
                $filter_sql .= "= 5";
                break;
              case "none":
              case "clear":
              case "safe":
                $filter_sql .= "= 0";
                break;
            }
            if($filter_sql != "message.type ")
              array_push($filter_type,$filter_sql);
          }
        }
        if(empty($filter_type)) $filter_type = array('message.type = 0');

        //$type_filter = "message.type ". $filter_type;
        $type_filter = "(".join(" OR ",$filter_type).")";
        $filter .= " AND ". $type_filter;

        // filtering RT and QT
        //$rt_filter = " message.message NOT LIKE '%RT%' AND message.message NOT LIKE '%QT%' AND message.message NOT LIKE '%ＲＴ%'";
        //$filter .= " AND".$rt_filter;
        // filtering reported message
        $filter .= "AND message.incident_id = 0";

        $filter .= ((!empty($filter))? ((!empty($filter_range))? (" AND ".$filter_range):""):$filter_range);

        // check, has the form been submitted?
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";
        
        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
            $post = Validation::factory($_POST);

                //  Add some filters
            $post->pre_filter('trim', TRUE);

            // Add some rules, the input field, followed by a list of checks, carried out in order
            $post->add_rules('action','required', 'alpha', 'length[1,1]');
            $post->add_rules('message_id.*','required','numeric');

            // Test to see if things passed the rule checks
            if ($post->validate())
            {   
                if( $post->action == 'd' )              // Delete Action
                {
                    foreach($post->message_id as $item)
                    {
                        // Delete Message
                        $message = ORM::factory('message')->find($item);
                        $message->delete( $item );
                    }
                    
                    $form_saved = TRUE;
                    $form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
                }
                elseif( $post->action == 'n' )          // Not Spam
                {
                    foreach($post->message_id as $item)
                    {
                        // Update Message Level
                        $message = ORM::factory('message')->find($item);
                        if ($message->loaded)
                        {
                            $message->message_level = '1';
                            $message->save();
                        }
                    }
                    
                    $form_saved = TRUE;
                    $form_action = strtoupper(Kohana::lang('ui_admin.modified'));
                }
                elseif( $post->action == 's' )          // Spam
                {
                    foreach($post->message_id as $item)
                    {
                        // Update Message Level
                        $message = ORM::factory('message')->find($item);
                        if ($message->loaded)
                        {
                            $message->message_level = '99';
                            $message->save();
                        }
                    }
                    
                    $form_saved = TRUE;
                    $form_action = strtoupper(Kohana::lang('ui_admin.modified'));
                }
            }
            // No! We have validation errors, we need to show the form again, with the errors
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('message'));
                $form_error = TRUE;
            }
        }       
        
        
        // Pagination
        $pagination = new Pagination(array(
            'query_string'   => 'page',
            'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
            'total_items'    => ORM::factory('message')
                                            ->join('reporter','message.reporter_id','reporter.id')
                                            ->where($filter)
                                            ->where('service_id', $service_id)
                                            ->count_all()
        ));

        $messages = ORM::factory('message')
                                ->join('reporter','message.reporter_id','reporter.id')
                                ->where('service_id', $service_id)
                                ->where($filter)
                                ->orderby('message_date','asc')
                                ->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
            
        // Get Message Count
        // ALL
        $reporter_ids = array();
        foreach($messages as $message){
            $reporter_ids[] = $message->reporter_id;
        }

        $reporters = array();
        if(count($reporter_ids)){
            $temp_reporters = ORM::factory('reporter')
                        ->in('id',implode(',',$reporter_ids))
                        ->find_all();
            foreach($temp_reporters as $reporter){
                $reporters[$reporter->id] = $reporter->service_account;
                }
        }

        $this->template->content->count_all = ORM::factory('message')
                                                        ->join('reporter','message.reporter_id','reporter.id')
                                                        ->where('service_id', $service_id)
                                                        ->where('message_type', 1)
                                                        ->where($type_filter)
                                                        ->count_all();
            
        // Trusted
        $this->template->content->count_trusted = ORM::factory('message')
            ->join('reporter','message.reporter_id','reporter.id')
            ->where('service_id', $service_id)
            ->where("( reporter.level_id = '4' OR reporter.level_id = '5' ) AND ( message.message_level != '99' )")
            ->where('message_type', 1)
            ->count_all();
        
        // Spam
        $this->template->content->count_spam = ORM::factory('message')
                                                        ->join('reporter','message.reporter_id','reporter.id')
                                                        ->where('service_id', $service_id)
                                                        ->where('message_type', 1)
                                                        ->where("message.message_level = '99'")
                                                        ->count_all();

        $this->template->content->safelist = array();
        $diffdate = (mktime(0,0,0,date("m",strtotime($r_to)),date("d",strtotime($r_to)),date("Y",strtotime($r_to))) / 86400) - (mktime(0,0,0,date("m",strtotime($r_from)),date("d",strtotime($r_from)),date("Y",strtotime($r_from))) / 86400);

        if( !empty($_GET['from']) && !empty($_GET['to']) && $diffdate < 1 )
        {
            $db = new Database;
            $safelist = array();
            $dupcnt = array();
            $dupmsg = array();
            $query = "select id, message, count(*) as count from message where ".$filter_range." group by message order by id asc;";
            $result = $db->query($query);
            foreach ( $result as $item )
            {
                $safelist[] = $item->id;
                $dupcnt[$item->id] = $item->count;
//                $dupmsg[$item->id] = $item->message;
            }
            $this->template->content->safelist = $safelist;
            $this->template->content->dupcnt = $dupcnt;

        }
        // If user doesn't have access to messages_reporters
        if (admin::permissions($this->user, "messages_reporters"))
		{
			$this->template->content->from_action = 'admin/messages/reporters/index/';
		}
		else
		{
			$this->template->content->from_action = 'admin/messages/index/';
		}

        $this->template->content->from = $r_from;
        $this->template->content->to = $r_to;
        $this->template->content->filter = $r_filter;
        $this->template->content->messages = $messages;
        $this->template->content->reporters = $reporters;
        $this->template->content->service_id = $service_id;
        $this->template->content->services = ORM::factory('service')->find_all();
        $this->template->content->pagination = $pagination;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;
        
        $levels = ORM::factory('level')->orderby('level_weight')->find_all();
        $this->template->content->levels = $levels;

        // Total Reports
        $this->template->content->total_items = $pagination->total_items;

        // Message Type Tab - Inbox/Outbox
        $this->template->content->type = $type;
        $this->template->content->level = $level;
        
        // Javascript Header
        $this->template->js = new View('admin/messages_js');
    }

    /**
    * Send A New Message Using Default SMS Provider
    */
    function send()
    {
        $this->template = "";
        $this->auto_render = FALSE;

        // setup and initialize form field names
        $form = array
        (
            'to_id' => '',
            'message' => ''
        );
        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;

        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
            $post = new Validation($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);

            // Add some rules, the input field, followed by a list of checks, carried out in order
            $post->add_rules('to_id', 'required', 'numeric');
            $post->add_rules('message', 'required', 'length[1,160]');

            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                // Yes! everything is valid
                $reply_to = ORM::factory('message', $post->to_id);
                
                if ($reply_to->loaded == true)
                {
                    // Yes! Replyto Exists
                    // This is the message we're replying to
                    $sms_to = intval($reply_to->message_from);

                    // Load Users Settings
                    $settings = new Settings_Model(1);
                    if ($settings->loaded == true) {
                        // Get SMS Numbers
                        if ( ! empty($settings->sms_no3))
                        {
                            $sms_from = $settings->sms_no3;
                        }
                        elseif ( ! empty($settings->sms_no2))
                        {
                            $sms_from = $settings->sms_no2;
                        }
                        elseif ( ! empty($settings->sms_no1))
                        {
                            $sms_from = $settings->sms_no1;
                        }
                        else
                        {
                            $sms_from = "000";      // User needs to set up an SMS number
                        }

                        // Send Message
						$response = sms::send($sms_to, $sms_from, $post->message);

                        // Message Went Through??
                        if ($response === true)
                        {
                            $newmessage = ORM::factory('message');
                            $newmessage->parent_id = $post->to_id;  // The parent message
                            $newmessage->message_from = $sms_from;
                            $newmessage->message_to = $sms_to;
                            $newmessage->message = $post->message;
                            $newmessage->message_type = 2;          // This is an outgoing message
                            $newmessage->reporter_id = $reply_to->reporter_id;
                            $newmessage->message_date = date("Y-m-d H:i:s",time());
                            $newmessage->save();

                            echo json_encode(array("status"=>"sent", "message"=>Kohana::lang('ui_admin.message_sent')));
                        }                        
                        else    // Message Failed 
                        {
                            echo json_encode(array("status"=>"error", "message"=>Kohana::lang('ui_admin.error')." - " . $response));
                        }
                    }
                    else
                    {
                        echo json_encode(array("status"=>"error", "message"=>Kohana::lang('ui_admin.error').Kohana::lang('ui_admin.check_sms_settings')));
                    }
                }
                // Send_To Mobile Number Doesn't Exist
                else {
                    echo json_encode(array("status"=>"error", "message"=>Kohana::lang('ui_admin.error').Kohana::lang('ui_admin.check_number')));
                }
            }

            // No! We have validation errors, we need to show the form again,
            // with the errors
            else
            {
                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('messages'));
                echo json_encode(array("status"=>"error", "message"=>Kohana::lang('ui_admin.error').Kohana::lang('ui_admin.check_message_valid')));
            }
        }

    }


    /**
     * setup simplepie
     * @param string $raw_data
     */
    private function _setup_simplepie( $raw_data )
    {
        $data = new SimplePie();
        $data->set_raw_data( $raw_data );
        $data->enable_cache(false);
        $data->enable_order_by_date(true);
        $data->init();
        $data->handle_content_type();
        return $data;
    }


}
