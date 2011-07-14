<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Search controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Search Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Search_Controller extends Keitai_Controller  {
	
    function __construct()
    {
        parent::__construct();
    }
	
	
    /**
  	 * Build a search query with relevancy
     * Stop word control included
     */
    public function index($page = 1) 
    {
        $this->template->content = new View('keitai/search');
        
        $search_query = "";
        $keyword_string = "";
        $where_string = "";
        $plus = "";
        $or = "";
        $search_info = "";
        $html = "";
        $pagination = "";
        
        // Stop words that we won't search for
        // Add words as needed!!
        $stop_words = array('the', 'and', 'a', 'to', 'of', 'in', 'i', 'is', 'that', 'it', 
            'on', 'you', 'this', 'for', 'but', 'with', 'are', 'have', 'be', 
            'at', 'or', 'as', 'was', 'so', 'if', 'out', 'not'
        );
       error_log("raw_data ".$_GET['k']); 
        if ($_GET)
        {
            /**
              * NOTES: 15/10/2010 - Emmanuel Kala <emmanuel@ushahidi.com>
              *
              * The search string undergoes a 3-phase sanitization process. This is not optimal
              * but it works for now. The Kohana provided XSS cleaning mechanism does not expel
              * content contained in between HTML tags this the "bruteforce" input sanitization.
              *
              * However, XSS is attempted using Javascript tags, Kohana's routing mechanism strips
              * the "<script>" tags from the URL variables and passes inline text as part of the URL
              * variable - This has to be fixed
              */
              
            // Phase 1 - Fetch the search string and perform initial sanitization
            $keyword_raw = (isset($_GET['k']))? mysql_real_escape_string($_GET['k']) : "";
error_log("Phase 1 ".mb_detect_encoding($keyword_raw)." ".$keyword_raw);     
          
            // Phase 2 - Strip the search string of any HTML and PHP tags that may be present for additional safety              
            $keyword_raw = strip_tags($keyword_raw);
error_log("Phase 2 ".mb_detect_encoding($keyword_raw)." ".$keyword_raw);               
            // Phase 3 - Apply Kohana's XSS cleaning mechanism
            $keyword_raw = $this->input->xss_clean($keyword_raw);
error_log("Phase 3 ".mb_detect_encoding($keyword_raw)." ".$keyword_raw);   
#            $keyword_raw = mb_convert_encoding($keyword_raw,'UTF-8','EUC-JP,SJIS,ASCII,JIS');
#error_log("Phase 4 ".mb_detect_encoding($keyword_raw)." ".$keyword_raw);   
        }
        else
        {
            $keyword_raw = "";
        }

#        error_log("Phase 4 ".$keyword_raw);
        $keywords = explode(' ', $keyword_raw);

        if (is_array($keywords) && !empty($keywords)) 
        {
            #error_log("Match ".$match);
            $match = "MATCH(incident_text) AGAINST(\"$keyword_raw\" IN BOOLEAN MODE)";
            $where_string = $match.' AND incident_active = 1';
            $search_query = "SELECT * FROM ".$this->table_prefix."s_incident".
                            " WHERE (".$where_string.") ORDER BY _score DESC LIMIT ";
        }
        
        if (!empty($search_query))
        {
            // Pagination
            $db = new Database();
            $pagination = new Pagination(array(
                'style' => 'keitai',
                'query_string'    => 'page',
                'items_per_page' => (int) Kohana::config('settings.items_per_page'),
		'total_items'    => $db->count_records('s_incident',$where_string)
            ));
            $query = $db->query($search_query . $pagination->sql_offset . ",". (int)Kohana::config('settings.items_per_page'));
            // Results Bar
            if ($pagination->total_items != 0)
            {
                $search_info .= "$keyword_raw ".$pagination->total_items."件";
                $search_info .= "<br>";
                  
            } else { 
                $search_info .= "0 ".Kohana::lang('ui_admin.results')."";
                
                $html .= Kohana::lang('ui_admin.your_search_for')." ".$keyword_raw." ".Kohana::lang('ui_admin.match_no_documents');
                $pagination = "";
            }
            
            foreach ($query as $search)
            {
                $incident_id = $search->id;
                $incident_title = $search->incident_title;
                $highlight_title = "";
                $incident_title_arr = explode(' ', $incident_title); 
                
                foreach($incident_title_arr as $value)
                {
                    if (in_array(strtolower($value),$keywords) && !in_array(strtolower($value),$stop_words))
                    {
                        $highlight_title .= "" . $value . " ";
                    }
                    else
                    {
                        $highlight_title .= $value . " ";
                    }
                }
                
                $incident_description = $search->incident_description;
                
                // Remove any markup, otherwise trimming below will mess things up
                $incident_description = strip_tags($incident_description);
                
                // Trim to 180 characters without cutting words
                if ((strlen($incident_description) > 180) && (strlen($incident_description) > 1))
                {
                    $whitespaceposition = strpos($incident_description," ",175)-1;
                    $incident_description = substr($incident_description, 0, $whitespaceposition);
                }
                
                $highlight_description = "";
                $incident_description_arr = explode(' ', $incident_description);
                 
                foreach($incident_description_arr as $value)
                {
                    if (in_array(strtolower($value),$keywords) && !in_array(strtolower($value),$stop_words))
                    {
                        $highlight_description .= "" . $value . "";
                    }
                    else
                    {
                        $highlight_description .= $value . " ";
                    }
                }
                
                $incident_date = date('Y/m/d', strtotime($search->incident_date));
		$html .= $highlight_title."<br>";
                $html .= $incident_date." "; 
                $html .= "<a href=\"" . url::base() . "/keitai/reports/view/" . $incident_id . "\">[詳細]</a><br>";
                $html .= "<hr size='1' noshade>";
            }
        }
        else
        {
            // Results Bar
            $search_info .= Kohana::lang('ui_admin.results');
            
            $html .= Kohana::lang('ui_admin.your_search_for')."<strong>".$keyword_raw."</strong> ".Kohana::lang('ui_admin.match_no_documents');
        }
        
        $html .= $pagination;
        
        $this->template->content->search_info = $search_info;
        $this->template->content->search_results = $html;
        
        // Rebuild Header Block
     }
}
