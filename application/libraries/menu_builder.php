<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Class build menu for backend and frontend
*/
class menu_builder
{
	var $CI = null;

	function __construct()
	{
		$this->CI = & get_instance();
	}
	
	function build_backend($menu)
	{
		$html = '';
		$id = isset($menu['id']) ? $menu['id'] : '';
		$aktif = isset($menu['aktif']) ? $menu['aktif'] : '';
		$judul = isset($menu['title']) ? $menu['title'] : '';
		$link = isset($menu['link']) ? $menu['link'] : '';
		$aksi = isset($menu['aksi']) ? $menu['aksi'] : '';

		$data = '';
		$data .= isset($menu['id']) ? 'data-id="'.$id.'" ' : '';
		$data .= isset($menu['title']) ? 'data-title="'.$judul.'" ' : '';
		$data .= isset($menu['link']) ? 'data-link="'.$link.'" ' : '';
		$data .= isset($menu['aktif']) ? 'data-aktif="'.$aktif.'" ' : '';
		$data .= isset($menu['aksi']) ? 'data-aksi="'.$aksi.'" ' : '';

		$html .= '<li class="dd-item dd3-item" '.$data.' >';
		//$html .= '<div class="dd-handle dd3-handle">drag</div>';
		$html .= '<div class="dd-handle dd3-handle">';
		$html .= '<i class="normal-icon ace-icon fa fa-comments blue bigger-130" ></i>';
		$html .= '<i class="drag-icon ace-icon fa fa-arrows bigger-125"></i>';
		$html .= '</div>';
		$html .= '<div class="dd3-content">';
		$html .= '<div class="control-group pull-left menu-isi" style="margin-top:4px">'. $judul.'</div>';
		$html .= '<div class="control-group pull-right menu-hover">';
		$html .= '</div></div>';
		if (count($menu['child']) > 0)
		{
			$html .= '<ol class="dd-list">';
			foreach($menu['child'] as $x => $sub_menu)
			{
				$html .= $this->build_backend($sub_menu);
			}
			$html .= '</ol>';
		}
		$html .= '</li>';

		return $html;
	}

	function build_backend_menu($arr_menu)
	{
		$menu_html = '<div class="dd dd-draghandle" style="margin-top:10px">';
		$menu_html .= '<ol class="dd-list">';
		foreach($arr_menu as $x => $menu)
		{
			$menu_html .= $this->build_backend($menu);
		}
		$menu_html .= '</ol>';
		$menu_html .= '</div>';
		return $menu_html;
	}
	
	function build_frontend_menu($group)
	{
		$fa = array('fa-cogs', 'fa-barcode ','fa-group','fa-fire','fa-exchange','fa-truck','fa-credit-card','fa-inbox','fa-align-justify');
		$arr_menu = $this->get_menu(0, $group);

		$menu_html = '<ul class="nav nav-list">';
		$menu_html .= '<li class="">';
		$menu_html .= '<a href="'.base_url().'home'.'">';
		$menu_html .= '<i class="menu-icon fa fa-desktop"></i>';
		$menu_html .= '<span class="menu-text"> Dashboard </span>';	
		$menu_html .= '</a>';
		$menu_html .= '<b class="arrow"></b>';
		$menu_html .= '</li>';
		
		for($i=0;$i<count($arr_menu);$i++){
			if($arr_menu[$i]['child'] > 0){
				$arr_sub_menu = $this->get_menu($arr_menu[$i]['id'], $group);
				
				$menu_html .= '<li class="">';
				$menu_html .= '<a href="#" class="dropdown-toggle">';
				$menu_html .= '<i class="menu-icon fa '.$fa[$i].'"></i>';
				$menu_html .= '<span class="menu-text"> '.$arr_menu[$i]['title'].' </span>';
				$menu_html .= '<b class="arrow fa fa-angle-down"></b>';
				$menu_html .= '</a>';
				$menu_html .= '<b class="arrow"></b>';
				$menu_html .= '<ul class="submenu">';
				for($i2=0;$i2<count($arr_sub_menu);$i2++){
					if($arr_sub_menu[$i2]['child'] > 0){
						$arr_sub_menu3 = $this->get_menu($arr_sub_menu[$i2]['id'], $group);
						
						$menu_html .= '<li class="">';
						$menu_html .= '<a href="#" class="dropdown-toggle">';
						$menu_html .= '<i class="menu-icon fa fa-caret-right"></i>';
						$menu_html .= '<span class="menu-text"> '.$arr_sub_menu[$i2]['title'].' </span>';
						$menu_html .= '<b class="arrow fa fa-angle-down"></b>';
						$menu_html .= '</a>';
						$menu_html .= '<b class="arrow"></b>';
						$menu_html .= '<ul class="submenu">';
						for($i3=0;$i3<count($arr_sub_menu3);$i3++){
							$menu_html .= '<li class="">';
							$menu_html .= '<a href="'.base_url().$arr_sub_menu3[$i3]['link'].'">';
							$menu_html .= '<i class="menu-icon "></i>';
							$menu_html .= '<span class="menu-text"> '.$arr_sub_menu3[$i3]['title'].' </span>';
							$menu_html .= '</a>';
							$menu_html .= '<b class="arrow"></b>';
							$menu_html .= '</li>';
						}
						$menu_html .= '</ul>';
						$menu_html .= '</li>';
					}
					else{
						$menu_html .= '<li class="">';
						$menu_html .= '<a href="'.base_url().$arr_sub_menu[$i2]['link'].'">';
						$menu_html .= '<i class="menu-icon fa fa-caret-right"></i>';
						$menu_html .= '<span class="menu-text"> '.$arr_sub_menu[$i2]['title'].' </span>';
						$menu_html .= '</a>';
						$menu_html .= '<b class="arrow"></b>';
						$menu_html .= '</li>';
					}
				}
				$menu_html .= '</ul>';
				$menu_html .= '</li>';
			}
			else{
				$menu_html .= '<li class="">';
				$menu_html .= '<a href="'.base_url().'home'.'">';
				$menu_html .= '<i class="menu-icon fa fa-pencil-square-o"></i>';
				$menu_html .= '<span class="menu-text"> Dashboard </span>';	
				$menu_html .= '</a>';
				$menu_html .= '<b class="arrow"></b>';
				$menu_html .= '</li>';
			}
		}
		$menu_html .= '</ul>';
		return $menu_html;
	}
	
	function get_menu($id_parent = 0, $id_group = 0)
	{
		$query_menu = "
			select a.id, a.parent_id, a.title, a.link, a.order_by, (select coalesce(count(n.id),0) from menu n where n.parent_id = a.id) child
			from menu a
			left join m_privilege b on b.id_menu = a.id and b.id_group =".$id_group."
			where a.parent_id = ".$id_parent."
			and a.aktif = 1
			and b.aksi > 0
			order by 5
		";
		$result = $this->CI->db->query( $query_menu );
		
		$x = 0;
		$menu = array();
		if (!$result) return $menu;
		$res_menu = $result->result_array();

		return $res_menu;
	}

	

}