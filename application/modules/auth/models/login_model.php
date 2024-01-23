<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model
{
	var $real_path;
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('encrypt');
		$this->load->helper('path');
		$this->real_path = dirname( realpath(SELF) )."/assets/img/";
	}

	function check_user($email, $password)
	{
		$this->db->where('email', $email);
		$qryUser = $this->db->get('users');

		if( $user = $qryUser->row_array() )
		{
			// status user aktif ?
			if($user["status"] == '1')
			{
				if ($user['passwd'] == sha1($user['salt'].$password))
				{
					return 2; // sukses login
				}
				else {
					return false;
				}			
			}
			else return 1; // user di blokir
		}
		else
		{
			return false; // username tidak dikenali
		}
	}

	function get_user_info($email)
	{
		$this->db->select("u.*");
		$this->db->from('users u');
		$this->db->where('email', $email);
		$result = $this->db->get();
		$nama = $result->row_array();
		return $nama;
	}
  
	function get_level_akses($uri)
	{
		// jika user sebagai admin, bypass semua akses
		if ($this->session->userdata('admin'))
		{
			return 3;
		}

		$this->db->select('p.aksi');
		$this->db->from('menu m');
		$this->db->join('m_privilege p', 'p.id_menu = m.id');
		$this->db->where('p.id_group', $this->id_group);
		$this->db->where('m.link', $uri);
		$result = $this->db->get()->row_array();

		return isset($result['aksi']) ? $result['aksi'] : 0;
	}

	function update_log($username, $log)
	{
		$data = array('log' => $log);
		$this->db->where('id', $this->session->userdata('id_user'));
		$this->db->update('users', $data);
	}

	function update_log_timelog($username, $log)
	{
		$time = date('d/m/Y h:i:s',time())."X".get_ip_address();
		$data = array('log' => $log, 'timelog' => $time);

		$this->db->where('id', $this->session->userdata('id_user'));
		$this->db->update('users', $data);
	}

	function get_log($username)
	{
		$id = $this->db->query('select log from users where id = '.$this->session->userdata('id_user'))->row_array();
		return $id['log'];
	}

	function get_timelog($username)
	{
		$id = $this->db->query('select timelog from users where id = '.$this->session->userdata('id_user'))->row_array();
		return $id['timelog'];
	}
	
	// simpan password baru
	function save_password()
	{
		$id = $this->session->userdata('id_user');
		$passwd = $this->input->post('newpasswd') ? $this->input->post('newpasswd') : '';

		$this->db->select('passwd, salt')->from('users')->where('id', $id);
		$rs = $this->db->get()->row_array();

		$data = array(
			'passwd' => sha1($rs['salt'].$passwd),
		);
		$this->db->trans_begin();
		$this->db->where('id', $id);
		$this->db->update('users', $data);
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

	// cek apakah password yang diberikan sama dengan password lama
	function check_password()
	{
		$id = $this->session->userdata('id_user');
		$passwd = $this->input->post('newpasswd') ? $this->input->post('newpasswd') : '';

		$this->db->select('passwd, salt')->from('users')->where('id', $id);
		$rs = $this->db->get()->row_array();
		$old_passwd = $rs['passwd'];
		$salt = $rs['salt'];

		return (sha1($salt.$passwd) === $old_passwd);
	}
	
	function get_konfigurasi()
	{
		$result = $this->db->get('settings');
		return $result->row_array();
	}
	
	function get_tahun()
	{
		$result = $this->db->select('tahun')->from('tahun')->where('aktif',2)->get('settings');
		return $result->row_array();
	}
	
	// simpan member
	function simpan_member()
	{
		$passwd = $this->input->post('passwd');
		$salt = strtotime(date('y-m-d h:i:s'));
		
		$filename = $_FILES["image"]["tmp_name"];
		move_uploaded_file($filename,  $this->real_path . $_FILES["image"]["name"]);
			
		$data = array(
			'name' => $this->input->post('name'),
			'email' => $this->input->post('email'),
			'nomor_hp' => $this->input->post('nomor_hp'),
			'tanggal_lahir' => prepare_date($this->input->post('tanggal_lahir')),
			'jenis_kelamin' => $this->input->post('jenis_kelamin'),
			'nomor_ktp' => $this->input->post('nomor_ktp'),
			'foto' => $_FILES["image"]["name"],
			'salt' => $salt,
			'passwd' => sha1($salt.$passwd),
			'status' => 1,
			'sys_admin' => 0,
		);
		$this->db->trans_begin();
		$this->db->insert('users', $data);
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