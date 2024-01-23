<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller
{
	var $real_path;
	public function __construct()
	{
		parent::__construct();		
		$this->load->model('Login_model');
		$this->load->helper('path');
		$this->real_path = dirname( realpath(SELF) )."/assets/img/";
		if (!file_exists($this->real_path)) {
			mkdir('./assets/img', 0777, true);
		}
	}

	function index()
	{
		if ($this->session->userdata('login') == TRUE)
		{
			redirect('home');
		}
		else
		{
			// belum login, tampilkan halaman login
			$this->load->view('login_view');
		}
	}
	
	function register()
	{
		$this->load->view('register_view');
	}

	function process_register()
	{
		$this->load->library('form_validation');
		$this->load->helper('email');

		$this->form_validation->set_rules('name','Nama', 'required');
		$this->form_validation->set_rules('tanggal_lahir','Tanggal Lahir', 'required');
		$this->form_validation->set_rules('jenis_kelamin','Jenis Kelamin', 'required');
		$this->form_validation->set_rules('nomor_ktp','Nomor KTP', 'required');
		$this->form_validation->set_rules('nomor_hp','Nomor Handphone', 'required');
		$this->form_validation->set_rules('email','Email', 'required|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('passwd','Password', 'required');
		$this->form_validation->set_rules('repasswd','Ulangi Password', 'required|matches[passwd]');
		$this->form_validation->set_rules('image','Foto', 'image|file|max:1024');
		
		$this->form_validation->set_message('required', '%s tidak boleh kosong.');
		$this->form_validation->set_message('is_unique', '%s sudah dipergunakan.');
		$this->form_validation->set_message('matches', '%s harus sama.');
		$this->form_validation->set_message('image', '%s harus bertipe image.');
		
		if($this->form_validation->run() == TRUE)
		{
			//die(print_r($this->input->post('email')));
			$allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
			$temp = explode(".", $_FILES["image"]["name"]);
			$extension = end($temp);
			$maxsize = 1024 * 1000; // maksimal 10000 KB (1KB = 1024 Byte)
			if (in_array($extension, $allowedExts))
			{
				if ($_FILES["image"]["size"] > $maxsize)
				{
					$this->session->set_flashdata('message', "<strong>Ukuran File Melebihi 1 MB</strong>");
					redirect('register');
				}
				else if ($_FILES["image"]["error"] > 0)
				{
					$this->session->set_flashdata('message', "ERROR Return Code: ". $_FILES["image"]["error"]);
					redirect('register');
				}
				else{
					if($this->Login_model->simpan_member()){
						$this->session->set_flashdata('message', "<strong>Pendaftaran Berhasil</strong>");
						redirect('login');
					}
					else{
						$this->session->set_flashdata('message', "<strong>Pendaftaran Gagal</strong>");
						redirect('register');
					}
				}
			}
			else
			{
				$this->session->set_flashdata('message', "<strong>Format file tidak di dukung</strong>");
				redirect('register');
			}			
		}
		else
		{
			$this->session->set_flashdata('message', validation_errors());
			redirect('register');
			//die(print_r(validation_errors()));
		}
	}
	
	function process_login()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('email','Email', 'required');
		$this->form_validation->set_rules('password','Password', 'required');
		
		if($this->form_validation->run() == TRUE)
		{
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$cek = $this->Login_model->check_user($email, $password);

			switch($cek){
				case 1 : // akun di blokir
					$error = "<strong>Status akun anda tidak aktif. Hubungi Administrator</strong>";
					$this->session->set_flashdata('message', $error);
					break;

				case 2 : // sukses login
					$user = $this->Login_model->get_user_info($email);
					$user_data = array(
						'name' => $user['name'],
						'nama_operator' => $user['name'],
						'foto' => $user['foto'],
						'login'  => TRUE,
						'id_user' => $user['id'],
						'admin' => $user['sys_admin'],
						'user_id' => $user['id'],
					);

					$this->session->set_userdata($user_data);

					break;

				case 3 : // group tdk aktif
					$error = "<strong>Status Group anda tidak aktif.</strong>";
					$this->session->set_flashdata('message', $error);
					break;

				default:
					$error = "<strong>Email atau password Anda salah!</strong>";
					$this->session->set_flashdata('message', $error);
			}
			redirect('login');
		}
		else
		{
			
			$this->load->view('login_view',$data);
		}
	}

	function logout()
	{
		// jika user belum login, tidak perlu di proses
		if ( !$this->session->userdata('login') ) redirect('login', 'refresh');

		$this->session->sess_destroy();
			redirect('login','refresh');
	}

	function updatetimelogIP()
	{
		$this->Login_model->update_log_timelog($this->session->userdata('username'), 1);
		echo json_encode('Sukses Update');
	}
}