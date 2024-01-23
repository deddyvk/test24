<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
function pdf_create($html, $filename='', $paper, $orientation, $stream=TRUE) 
{
    require_once("dompdf/dompdf_config.inc.php");

    $dompdf = new DOMPDF();
    $dompdf->set_paper($paper,$orientation);
    $dompdf->load_html($html);
    $dompdf->render();
    if ($stream) {
        $dompdf->stream($filename.".pdf");
    } else {
        //return $dompdf->output();
		$CI =& get_instance();
        $CI->load->helper('file');
		$CI->load->helper('path');
		$CI->path2engine = dirname( realpath(SELF) )."/assets/fr/";
		$CI->path2fr3 = $CI->path2engine . 'fr3/';
		$CI->path2output = $CI->path2engine . 'output/';
        write_file($CI->path2output.$filename.".pdf", $dompdf->output());
    }
}
?>