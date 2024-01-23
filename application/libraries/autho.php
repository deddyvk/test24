<?php
/*
 * Class penomoran otomatis
 */
class autho
{
	var $CI = null;
	function __construct()
	{
		$this->CI = & get_instance();
	}

	function check_nomor_aktivitas($param)
	{
		$form = isset($param['tipe']) ? $param['tipe'] : '';
		$supp = isset($param['supp']) ? $param['supp'] : '';
		$tanggal = isset($param['tanggal']) ? $param['tanggal'] : '';
		$id_akun = isset($param['id_akun']) ? $param['id_akun'] : '';
		$tipe = '';

		switch ($form)
		{
			case 'REQUISITION' 	: $tipe = 'REQUISITION'; break;
			case 'REQUISITION_NON' 	: $tipe = 'REQUISITION_NON'; break;
			case 'PURCHASING' 	: $tipe = 'PURCHASING'; break;
			case 'PURCHASING_NON' 	: $tipe = 'PURCHASING_NON'; break;
			case 'BARANG_DATANG' 	: $tipe = 'BARANG_DATANG'; break;
			case 'LOGISTIK' 	: $tipe = 'LOGISTIK'; break;
			case 'LOGISTIK_CASH' 	: $tipe = 'LOGISTIK_CASH'; break;
			case 'BARANG_DATANG_LOGISTIK' 	: $tipe = 'BARANG_DATANG_LOGISTIK'; break;
			case 'EKSPEDISI' 	: $tipe = 'EKSPEDISI'; break;
			case 'KAS_KECIL_MASUK' 	: $tipe = 'KAS_KECIL_MASUK'; break;
			case 'KAS_KECIL_KEMBALI' 	: $tipe = 'KAS_KECIL_KEMBALI'; break;
			case 'KAS_BENDAHARA_MASUK' 	: $tipe = 'KAS_BENDAHARA_MASUK'; break;
			case 'KAS_BENDAHARA_KELUAR' 	: $tipe = 'KAS_BENDAHARA_KELUAR'; break;
			case 'KWITANSI' 	: $tipe = 'KWITANSI'; break;
			case 'PEMBAYARAN' 	: $tipe = 'PEMBAYARAN'; break;
			case 'PEMBAYARAN_NON' 	: $tipe = 'PEMBAYARAN_NON'; break;
			case 'PENERIMAAN' 	: $tipe = 'PENERIMAAN'; break;
		}

		/* ambil template sesuai tipe aktivitas */
		$db = $this->CI->db;
		$db->select('template')->from('penomoran')->where('tipe', $form);
		$result = $db->get()->row_array();

		if ( count($result) == 0 ){
			return false;
		}

		$only_nomor = FALSE;
		$template = $result['template'];
		$sql_cek = $template;

		/*  Proses {nomor}
		******************************************************************************************
		*/
		$pattern  = "{nomor:\d+(:AN)?}";
		preg_match_all($pattern, $template, $tmp);
		$tmp_nomor = $tmp[0];
		$digit_ = '';
		$digit = 1;

		//$first_digit = strpos ($template, "{nomor") + 1;
		if (count($tmp_nomor) > 0)
		{
			$nomor = $tmp_nomor[0];
			$tmp = explode(':', $nomor);
			$digit = $tmp[1];
			$digit_ = str_repeat('_', $digit);
			if (isset($tmp[2]) && $tmp[2] === 'AN')
			{
				$only_nomor =  TRUE;
				$tmp_nomor = str_replace(':AN', '', $nomor);
			}
			$sql_cek = str_replace("{".$nomor."}", $digit_, $sql_cek);
		}

		/*  Proses {bulan}
		******************************************************************************************
		*/
		$pattern  = "{bulan}";
		preg_match_all($pattern, $template, $tmp);
		$tmp_bulan = $tmp[0];
		if (count($tmp_bulan) > 0)
		{
			//$bulan = $this->bulan(date("m"));
			if(isset($param['tanggal']))
				$bulan = date('m', strtotime($param['tanggal']));
			else
				$bulan = date("m");
			$sql_cek = str_replace($pattern, $bulan, $sql_cek);
		}
		
		/*  Proses {BULAN}
		******************************************************************************************
		*/
		$pattern  = "{BULAN}";
		preg_match_all($pattern, $template, $tmp);
		$tmp_bulan = $tmp[0];
		if (count($tmp_bulan) > 0)
		{
			if(isset($param['tanggal']))
				$bulan = $this->bulan_romawi(date('m', strtotime($param['tanggal'])));
			else
				$bulan = $this->bulan_romawi(date("m"));
			
			$sql_cek = str_replace($pattern, $bulan, $sql_cek);
		}

		/*  Proses {tahun}
		******************************************************************************************
		*/
		$pattern  = "{tahun}";
		preg_match_all($pattern, $template, $tmp);
		$tmp_tahun = $tmp[0];
		if (count($tmp_tahun) > 0)
		{
			if(isset($param['tanggal']))
				$tahun = date('Y', strtotime($param['tanggal']));
			else
				$tahun = date("Y");
			
			$sql_cek = str_replace($pattern, $tahun, $sql_cek);
		}
		
		/*  Proses {SUPP}
		******************************************************************************************
		*/
		$pattern  = "{SUPP}";
		preg_match_all($pattern, $template, $tmp);
		$tmp_tahun = $tmp[0];
		if (count($tmp_tahun) > 0)
		{
			$db->select('a.inisial');
			$db->from('supplier a');
			$db->where("a.id_supplier",$supp);
			$result = $db->get()->row_array();
			$sql_cek = str_replace($pattern, $result['inisial'], $sql_cek);
		}
		
		/*  Proses {AKUN}
		******************************************************************************************
		*/
		$pattern  = "{AKUN}";
		preg_match_all($pattern, $template, $tmp);
		$tmp_tahun = $tmp[0];
		if (count($tmp_tahun) > 0)
		{
			$db->select('a.kode_akun');
			$db->from('akun a');
			$db->where("a.id_akun",$id_akun);
			$result = $db->get()->row_array();
			$sql_cek = str_replace($pattern, $result['kode_akun'], $sql_cek);
		}
		
		if ($digit_ != '')
		{
			$first_digit = strpos ($sql_cek, $digit_) + 1;

			/* cek nomor terakhir */
			switch ($tipe)
			{
				case 'REQUISITION' : $tabel = 'purchase_request'; break;
				case 'REQUISITION_NON' : $tabel = 'purchase_request'; break;
				case 'PURCHASING' : $tabel = 'purchasing'; break;
				case 'PURCHASING_NON' : $tabel = 'purchasing'; break;
				case 'BARANG_DATANG' : $tabel = 'barang_datang'; break;
				case 'LOGISTIK' : $tabel = 'logistik'; break;
				case 'LOGISTIK_CASH' : $tabel = 'logistik'; break;
				case 'BARANG_DATANG_LOGISTIK' : $tabel = 'barang_datang_logistik'; break;
				case 'EKSPEDISI' : $tabel = 'ekspedisi'; break;
				case 'KAS_KECIL_MASUK' : $tabel = 'kas_kecil'; break;
				case 'KAS_KECIL_KEMBALI' : $tabel = 'kas_kecil'; break;
				case 'KAS_BENDAHARA_MASUK' : $tabel = 'kas_besar'; break;
				case 'KAS_BENDAHARA_KELUAR' : $tabel = 'kas_besar'; break;
				case 'KWITANSI' : $tabel = 'pembayaran'; break;
				case 'PEMBAYARAN' : $tabel = 'pembayaran'; break;
				case 'PEMBAYARAN_NON' : $tabel = 'pembayaran'; break;
				case 'PENERIMAAN' : $tabel = 'penerimaan'; break;
			}
			
			$db->select('max(substring(a.nomor from '.$first_digit.' for '.$digit.')) maxno');
			$db->from($tabel.' a');
			$db->where("a.nomor like '".$sql_cek."'");
			//$db->get(); die(print_r($db->queries));
			$result = $db->get()->row_array();

			if (count($result) > 0)
				$maxno = (int) $result['maxno'] == 0 ? 1 : (int) $result['maxno'] + 1;
			else
				$maxno = 1;

			$nomor = $this->FormatNoTrans($maxno, $digit);
			return substr_replace($sql_cek, $nomor, $first_digit - 1, $digit); 
		}
		else
		return $sql_cek;
	}

	function FormatNoTrans($num,$panjang) {
		$pjg_kar = strlen($num);
		$rpt = $panjang - $pjg_kar;
		$prev = '';
		if($rpt > 0){
			for($u=0;$u<$rpt;$u++){
				$prev.="0";
			}
			$NoTrans = $prev.$num;
		}
		else{
			$NoTrans = $num;
		}

		return $NoTrans;
	}

	function bulan($bulan)
	{
		switch ($bulan)
		{
			case  1 : $bulan = "JAN"; break;
			case  2 : $bulan = "FEB"; break;
			case  3 : $bulan = "MAR"; break;
			case  4 : $bulan = "APR"; break;
			case  5 : $bulan = "MEI"; break;
			case  6 : $bulan = "JUN"; break;
			case  7 : $bulan = "JUL"; break;
			case  8 : $bulan = "AGS"; break;
			case  9 : $bulan = "SEP"; break;
			case 10 : $bulan = "OKT"; break;
			case 11 : $bulan = "NOV"; break;
			case 12 : $bulan = "DES"; break;
		}
		return $bulan;
	}
	
	function bulan_romawi($bulan)
	{
		switch ($bulan)
		{
			case  1 : $bulan = "I"; break;
			case  2 : $bulan = "II"; break;
			case  3 : $bulan = "III"; break;
			case  4 : $bulan = "IV"; break;
			case  5 : $bulan = "V"; break;
			case  6 : $bulan = "VI"; break;
			case  7 : $bulan = "VII"; break;
			case  8 : $bulan = "VIII"; break;
			case  9 : $bulan = "IX"; break;
			case 10 : $bulan = "X"; break;
			case 11 : $bulan = "XI"; break;
			case 12 : $bulan = "XII"; break;
		}
		return $bulan;
	}
}