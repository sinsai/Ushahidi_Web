<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Form helper class.
 *
 * $Id: form.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class form_ex_Core extends form_Core{
	public static function hidden($data, $value = '')
	{
		if ( ! is_array($data))
		{
			$data = array
			(
				$data => $value
			);
		}

		$input = '';
		foreach ($data as $name => $value)
		{
			$attr = array
			(
				'type'  => 'hidden',
				'name'  => $name,
				'value' => $value
			);

			$input .= form::input($attr)."\n";
		}

		return $input;
	}
	public static function checkbox($data, $value = '', $checked = FALSE, $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'checkbox';

		if ($checked == TRUE OR (isset($data['checked']) AND $data['checked'] == TRUE))
		{
			$data['checked'] = 'checked';
		}
		else
		{
			unset($data['checked']);
		}

		return form_ex::input($data, $value, $extra);
	}

	/**
	 * Creates an HTML form input tag. Defaults to a text type.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string        input value, when using a name
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   boolean       encode existing entities
	 * @return  string
	 */
	public static function input($data, $value = '', $extra = '', $double_encode = TRUE )
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		// Type and value are required attributes
		$data += array
		(
			'type'  => 'text',
			'value' => $value
		);

		// For safe form data
		$data['value'] = html::specialchars($data['value'], $double_encode);

		return '<input'.form_ex::attributes($data).' '.$extra.' />';
	}

	/**
	 * Sorts a key/value array of HTML attributes, putting form attributes first,
	 * and returns an attribute string.
	 *
	 * @param   array   HTML attributes array
	 * @return  string
	 */
	public static function attributes($attr, $type = NULL)
	{
		if (empty($attr))
			return '';

		if (isset($attr['name']) AND empty($attr['id']))
		{
			if ($type === NULL AND ! empty($attr['type']))
			{
				// Set the type by the attributes
				$type = $attr['type'];
			}

			switch ($type)
			{
				case 'text':
				case 'textarea':
				case 'password':
				case 'select':
				case 'checkbox':
				case 'file':
				case 'image':
				case 'button':
				case 'submit':
					// Only specific types of inputs use name to id matching
					$attr['id'] = $attr['name'];
				break;
			}
		}

		$order = array
		(
			'action',
			'method',
			'type',
			'id',
			'name',
			'value',
			'src',
			'size',
			'maxlength',
			'rows',
			'cols',
			'accept',
			'tabindex',
			'accesskey',
			'align',
			'alt',
			'title',
			'class',
			'style',
			'selected',
			'checked',
			'readonly',
			'disabled'
		);

		$sorted = array();
		foreach ($order as $key)
		{
			if (isset($attr[$key]))
			{
				// Move the attribute to the sorted array
				$sorted[$key] = $attr[$key];

				// Remove the attribute from unsorted array
				unset($attr[$key]);
			}
		}

		// Combine the sorted and unsorted attributes and create an HTML string
		return html::attributes(array_merge($sorted, $attr));
	}
	/*                                                              */
	/* 共通レポート検索フォームヘルパー                             */
	/* Created 2011/04/15 ggslyman                                  */
	/* 使用するGETパラメータ                                        */
	/* address 住所テキストボックスに入力する値                     */
	/* distance 検索半径 ドロップダウンリストより入力               */
	/* category 選択カテゴリ ドロップダウンより入力                 */
	/* keyword 検索キーワード キーワードテキストボックスより入力    */
	/* sort レポートのソート順 チェックボックスより指定             */
	function reportSearchForm($action){
		// 初期化パラメータ
		// GET parameter hidden set
		$category_select_id = "category_select_".date("Ymdhis");
		$end = "\r\n";
		$form  = '<form action="'.$action.'" name="area-search" id="area-search" method="GET">'.$end;
		// hidden 初期化
		$hidden = '<input type="hidden" name="mode" value="areasearch">'.$end;
		$hidden .= '<input type="hidden" name="c"   value="'. valid::initGetVal('c' ,'number').'">'.$end;
		$hidden .= '<input type="hidden" name="l"   value="'. valid::initGetVal('l' ,'text'  ).'">'.$end;
		$hidden .= '<input type="hidden" name="sw"  value="'. valid::initGetVal('sw','text'  ).'">'.$end;
		$hidden .= '<input type="hidden" name="ne"  value="'. valid::initGetVal('ne','text'  ).'">'.$end;
		// hidden 初期化
		//住所入力欄
		$addr_text = '住所<input type="text" name="address" value="';
		$addr_text .= valid::initGetVal('address','text');
		$addr_text .= '" />周辺の'.$end;
		// 住所入力欄 end
		// 検索半径
		$select_dist = '半径<select name="distance">'.$end;
			//配列べたうち
			$distance_selected = array(
				   0=>array("selected"=>"","value"=>0.5,"disp"=>"500m")
				,  1=>array("selected"=>"","value"=>  1,"disp"=>"1km")
				,  2=>array("selected"=>"","value"=>  2,"disp"=>"2km")
				,  3=>array("selected"=>"","value"=>  3,"disp"=>"3km")
				,  5=>array("selected"=>"","value"=>  5,"disp"=>"5km")
				, 10=>array("selected"=>"","value"=> 10,"disp"=>"10km")
				, 20=>array("selected"=>"","value"=> 20,"disp"=>"20km")
				, 30=>array("selected"=>"","value"=> 30,"disp"=>"30km")
				, 50=>array("selected"=>"","value"=> 50,"disp"=>"50km")
				,100=>array("selected"=>"","value"=>100,"disp"=>"100km")
				,150=>array("selected"=>"","value"=>150,"disp"=>"150km")
				,200=>array("selected"=>"","value"=>200,"disp"=>"200km")
				,250=>array("selected"=>"","value"=>250,"disp"=>"250km")
				,300=>array("selected"=>"","value"=>300,"disp"=>"300km")
			);
		if(isset($_GET["distance"]) && $_GET["distance"]==0.5)$_GET["distance"] =0;
		if(isset($_GET["distance"]))$distance_selected[$_GET["distance"]]["selected"] = 'selected';
		// option生成
		foreach($distance_selected as $key => $val){
			$select_dist .= '<option value="'.$val["value"].'" '.$val["selected"].'>'.$val["disp"].'</option>'.$end;
		}
		$select_dist .= '</select>のレポート'.$end;
		// 検索半径 end
		// カテゴリ選択
		$select_cat  = '<select name="c" id="'.$category_select_id.'">'.$end;
		//全カテゴリ
		$select_cat .= '<option value="0" title="/ushahidi/media/img/all.png"';
		if(!isset($_GET["c"]))$select_cat .= 'selected="selected"';
		$select_cat .= '>全カテゴリ</option>'.$end;
		// 全カテゴリ end
		// カテゴリ周り整備
		$db = new Database;
		$query = 'SELECT id,category_title,category_color,category_image_thumb FROM category ORDER BY category_type desc;';
		$query = $db->query($query);
		$category_master = array();
		$localized_categories = array();
		foreach($query as $row){
			$category_master[$row->id]['title'] = $row->category_title; 
			$category_master[$row->id]['color'] = $row->category_color; 
			$category_master[$row->id]['category_image_thumb'] = $row->category_image_thumb; 
			$localized_categories[(string)$row->category_title] = $row->category_title;
			$localized_categories[(string)$row->category_title]['title'] = $row->category_title;
			$localized_categories[(string)$row->category_title]['color'] = $row->category_title;
		}
		unset($db);
		foreach ($category_master AS $key =>  $category)
		{
				$select_cat .= '<option value="'.$key.'" title="/ushahidi/media/uploads/'.$category['category_image_thumb'].'"';
				if(isset($_GET["c"]) && $key == $_GET["c"])$select_cat .= 'selected';
				$select_cat .= '>'.$localized_categories[(string)$category['title']].'</option>'.$end;
		}
		// カテゴリ選択 end
		$select_cat .= '</select>'.$end;

		// セレクトボックスアイコン追加処理コールスクリプト
		$scripts  = '<script><!--'.$end;
		$scripts .= '$(document).ready(function(e) {'.$end;
		$scripts .= '	try {'.$end;
		$scripts .= '			$("#'.$category_select_id.'").msDropDown({visibleRows:'.(count($category_master)-1).', rowHeight:23})'.$end;
		$scripts .= '	} catch(e) {'.$end;
		$scripts .= '		alert(e.message);'.$end;
		$scripts .= '	}'.$end;
		$scripts .= '});'.$end;
		$scripts .= '-->'.$end;
		$scripts .= '</script>'.$end;
		// セレクトボックスアイコン追加処理コールスクリプト end
		// カテゴリ選択 end
		// 検索キーワード
		$keyword  = '×キーワード：<input type="text" name="keyword" value="';
		$keyword .= valid::initGetVal('keyword','text');
		$keyword .= '" />';
		// 検索キーワードend
		// sort
		$report_order = array(
			 "new"=>""
			,"dist"=>""
		);
		if(isset($_GET["order"])){
			$report_order[$_GET["order"]] = "checked";
		}else{
			$report_order["new"] = "checked";
		}
		$sort  = '<input type="radio" name="order" value="new" '.$report_order["new"].'>新着から表示&nbsp;';
		$sort .= '<input type="radio" name="order" value="dist" '.$report_order["dist"].'>住所に近い順から表示';
		$sort .= '&nbsp;';
		// sort end
		// layout
		$result  = '<div id="report_search_form_container">';
		$result .= $scripts;
		$result .= $form.$end;
		$result .= $hidden.$end;
		$result .= '<div id="report_search_form" class="clearfix">'.$end;
			$result .= '<div id="left_search_box">'.$end;
				$result .= '<div class="left-container clearfix">'.$end;
					$result .= '<div class="left-container-header">'.$end;
					$result .= '表示エリア'.$end;
					$result .= '</div>'.$end;
					$result .= '<div class="left-container-detail">'.$end;
					$result .= $addr_text.$select_dist.$end;
					$result .= '</div>';
				$result .= '</div>';
				$result .= '<div class="left-container clearfix" >'.$end;
					$result .= '<div class="left-container-header">'.$end;
					$result .= '絞り込み'.$end;
					$result .= '</div>';
					$result .= '<div class="left-container-detail">'.$end;
					$result .= $select_cat.$keyword;
					$result .= '</div>';
				$result .= '</div>';
				$result .= '<div class="left-container clearfix" >'.$end;
					$result .= '<div class="left-container-header">'.$end;
					$result .= '表示順'.$end;
					$result .= '</div>';
					$result .= '<div class="left-container-detail">'.$end;
					$result .= $sort.$end;
					$result .= '</div>'.$end;
				$result .= '</div>'.$end;
			$result .= '</div>'.$end;
			$result .= '<div id="right_search_box">'.$end;
			$result .= '<input type="submit" name="submit" value="指定の条件で&#13;&#10;レポートを検索" style="height:3em;width;150px;" />'.$end;
			$result .= '</div>'.$end;
		$result .= '</div>'.$end;
		$result .= '</form>'.$end;
		$result .= '</div>'.$end;
		return $result;
	}
} // End form