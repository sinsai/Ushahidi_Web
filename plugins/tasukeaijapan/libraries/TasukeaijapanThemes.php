<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Themes Library
 * These are regularly used templating functions
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @module	   Themes Library
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class TasukeaijapanThemes {
	
	public $map_enabled = false;
	public $api_url = null;
	public $main_page = false;
	public $this_page = false;
	public $treeview_enabled = false;
	public $validator_enabled = false;
	public $photoslider_enabled = false;
	public $videoslider_enabled = false;
	public $site_style = false;
	public $js = null;
	
	public $css_url = null;
	public $js_url = null;
	
	public function __construct()
	{
		// Load cache
		$this->cache = new Cache;

		// Load Session
		$this->session = Session::instance();
		
		// Load Local or CDN?
		$this->css_url = (Kohana::config("cache.cdn_css")) ? 
			Kohana::config("cache.cdn_css") : url::base();
		$this->js_url = (Kohana::config("cache.cdn_js")) ? 
			Kohana::config("cache.cdn_js") : url::base();

		$this->css_url .= 'plugins/tasukeaijapan/';
		$this->js_url .= 'plugins/tasukeaijapan/';
	}
	
	/**
	 * Header Block Contains CSS, JS and Feeds
	 * Css is loaded before JS
	 */
	public function header_block()
	{
		return $this->_header_css().
			$this->_header_js();
	}
	
	/**
	 * Css Items
	 */
	private function _header_css()
	{
		$core_css = "";
		$core_css .= html::stylesheet($this->css_url."views/css/jquery-ui-themeroller", "", true);
		$core_css .= "<!--[if lte IE 7]>".html::stylesheet($this->css_url."views/css/iehacks","",true)."<![endif]-->";
		$core_css .= "<!--[if IE 7]>".html::stylesheet($this->css_url."views/css/ie7hacks","",true)."<![endif]-->";
		$core_css .= "<!--[if IE 6]>".html::stylesheet($this->css_url."views/css/ie6hacks","",true)."<![endif]-->";
			
		if ($this->map_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."views/css/openlayers","",true);
		}
		
		if ($this->treeview_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."views/css/jquery.treeview","",true);
		}
		
		if ($this->photoslider_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."views/css/picbox/picbox","",true);
		}
		
		if ($this->videoslider_enabled)
		{
			$core_css .= html::stylesheet($this->css_url."views/css/videoslider","",true);
		}
		
		$core_css .= html::stylesheet($this->css_url."views/css/style.css");
		
		// Render CSS
		$plugin_css = plugin::render('stylesheet');
		
		$iframe_css = html::stylesheet($this->css_url."views/css/iframe","",true);
		
		return $core_css.$plugin_css.$iframe_css;
	}
	
	/**
	 * Javascript Files and Inline JS
	 */
	private function _header_js()
	{
		$core_js = "";
		if ($this->map_enabled)
		{
			$core_js .= html::script($this->js_url."views/js/OpenLayers", true);
			$core_js .= "<script type=\"text/javascript\">OpenLayers.ImgPath = '".$this->js_url."views/img/openlayers/"."';</script>";
		}
		
		$core_js .= html::script($this->js_url."views/js/jquery", true);
		$core_js .= html::script($this->js_url."views/js/jquery.ui.min", true);
		$core_js .= html::script($this->js_url."views/js/jquery.pngFix.pack", true);
		
		if ($this->map_enabled)
		{
			$core_js .= $this->api_url;

			if ($this->main_page || $this->this_page == "alerts")
			{
				$core_js .= html::script($this->js_url."views/js/selectToUISlider.jQuery", true);
			}

			if ($this->main_page)
			{
				$core_js .= html::script($this->js_url."views/js/jquery.flot", true);
				$core_js .= html::script($this->js_url."views/js/timeline", true);
				$core_js .= "<!--[if IE]>".html::script($this->js_url."views/js/excanvas.min", true)."<![endif]-->";
			}
		}

		// Javascript files from plugins
		$plugin_js = plugin::render('javascript');
		
		// Inline Javascript
		$inline_js = "<script type=\"text/javascript\">
                        <!--//
			".'$(document).ready(function(){$("#map").pngFix();});'.$this->js.
                        "//-->
                        </script>";
		
		return $core_js.$plugin_js.$inline_js;
	}
	
	public function languages()
	{
		// *** Locales/Languages ***
		// First Get Available Locales

		$locales = $this->cache->get('locales');

		// If we didn't find any languages, we need to look them up and set the cache
		if( ! $locales)
		{
			$locales = locale::get_i18n();
			$this->cache->set('locales', $locales, array('locales'), 604800);
		}
		
		// Locale form submitted?
		if (isset($_GET['l']) && !empty($_GET['l']))
		{
			$this->session->set('locale', $_GET['l']);
		}
		// Has a locale session been set?
		if ($this->session->get('locale',FALSE))
		{
			// Change current locale
			Kohana::config_set('locale.language', $_SESSION['locale']);
		}
		
		$languages = "";
		$languages .= "<div class=\"language-box\">";
		$languages .= "<form action=\"\">";
		$languages .= form::dropdown('l', $locales, Kohana::config('locale.language'),
			' onchange="this.form.submit()" ');
		$languages .= "</form>";
		$languages .= "</div>";
		
		return $languages;
	}
	
	/*
	* Google Analytics
	* @param text mixed	 Input google analytics web property ID.
	* @return mixed	 Return google analytics HTML code.
	*/
	public function google_analytics($google_analytics = false)
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
}
