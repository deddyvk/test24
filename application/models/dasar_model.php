<?php
class Dasar_model extends CI_Model {
	var $real_path;
	
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->helper('path');
		$this->real_path = dirname( realpath(SELF) )."/assets/img/";
		if (!file_exists($this->real_path)) {
			mkdir('./assets/img', 0777, true);
		}
	}
	
	public function filter($search, $limit, $start, $order_field, $order_ascdesc){
		$this->db->like('email', $search); // Untuk menambahkan query where LIKE    
		$this->db->or_like('name', $search); // Untuk menambahkan query where OR LIKE    
		$this->db->or_like('nomor_hp', $search); // Untuk menambahkan query where OR LIKE
		$this->db->order_by($order_field, $order_ascdesc); // Untuk menambahkan query ORDER BY    
		$this->db->limit($limit, $start); // Untuk menambahkan query LIMIT
		return $this->db->get('users')->result_array(); // Eksekusi query sql sesuai kondisi diatas  
	}  
	
	public function count_all(){    
		return $this->db->count_all('users'); // Untuk menghitung semua data siswa  
	}  
	
	public function count_filter($search){    
		$this->db->like('email', $search); // Untuk menambahkan query where LIKE    
		$this->db->or_like('name', $search); // Untuk menambahkan query where OR LIKE    
		$this->db->or_like('nomor_hp', $search); // Untuk menambahkan query where OR LIKE     
		return $this->db->get('users')->num_rows(); // Untuk menghitung jumlah data sesuai dengan filter pada textbox pencarian  
	}
	
	// Email hanya boleh dipakai sekali
	function check_duplikasi_email()
	{
		$id = $this->input->post('id') ? $this->input->post('id') : 0;
		$email = $this->input->post('email') ? $this->input->post('email') : '';

		$this->db->select('count(1) dup')
		->from('users a')
		->where('a.id <>', $id)
		->where('a.email', $email);
		$rs = $this->db->get()->row_array();

		return (integer) $rs['dup'] < 1;
	}
	
	function deleteMember($id=0)
	{
		$this->db->select("u.*");
		$this->db->from('users u');
		$this->db->where('id', $id);
		$result = $this->db->get();
		$data = $result->row_array();
		
		$logo_file 	= dirname($_SERVER['SCRIPT_FILENAME']).'/assets/img/'.$data['foto'];
		
		if (file_exists($logo_file) && $id > 0) {
			unlink($logo_file);
		}
		
		$this->db->where('id', $id)->delete('users');
	}
	
	function get_all_user()
	{
		$this->db->select("u.*");
		$this->db->from('users u');
		$result = $this->db->get();
		$nama = $result->result_array();
		return $nama;
	}
	
	function get_user_info($id)
	{
		$this->db->select("u.*");
		$this->db->from('users u');
		$this->db->where('id', $id);
		$result = $this->db->get();
		$nama = $result->row_array();
		return $nama;
	}
	
	// simpan member
	function simpan_member()
	{
		$id = $this->input->post('id');
		$passwd = $this->input->post('passwd');
		
		$data = array(
			'name' => $this->input->post('name'),
			'email' => $this->input->post('email'),
			'nomor_hp' => $this->input->post('nomor_hp'),
			'tanggal_lahir' => prepare_date($this->input->post('tanggal_lahir')),
			'jenis_kelamin' => $this->input->post('jenis_kelamin'),
			'nomor_ktp' => $this->input->post('nomor_ktp'),
			'status' => $this->input->post('status'),
			'sys_admin' => $this->input->post('sys_admin')
		);
		
		if(!empty($_FILES["image"]['name'])){
			$filename = $_FILES["image"]["tmp_name"];
			move_uploaded_file($filename,  $this->real_path . $_FILES["image"]["name"]);
			$data['foto'] = $_FILES["image"]["name"];
		}
		
		if($passwd){
			$salt = strtotime(date('y-m-d h:i:s'));
			$data['salt'] = $salt;
			$data['passwd'] = sha1($salt.$passwd);
		}
			
		//die(print_r($data));
		$this->db->trans_begin();
		if($id > 0){
			$this->db->where('id', $id);
			$this->db->update('users', $data);
		}
		else{
			$this->db->insert('users', $data);
		}
		
		if ($this->db->trans_status() === false)
		{
			$this->last_error_id = $this->db->_error_number();
			$this->last_error_message = $this->db->_error_message();
			$this->db->trans_rollback();
			return false;
		}

		$this->db->trans_commit();
		return true;
	}
}
