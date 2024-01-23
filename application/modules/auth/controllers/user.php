<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->modul_name = 'auth/user';
		$this->modul_display = 'Admin';
		$this->view_daftar = 'user_view';
		$this->view_form = 'user_form';
		$this->load->model('auth/user_model', 'data_model');
	}

	public function index()
	{
		$this->daftar();
	}

	public function daftar()
	{
		$this->load->model('auth/login_model', 'auth');
		$data['breadcrumbs'] = 'Daftar '.$this->modul_display;
		$data['modul'] = $this->modul_name;
		$data['grid']['akses'] = $this->auth->get_level_akses($this->uri->slash_segment(1));
		$data['grid']['url'] = base_url($this->modul_name.'/get_daftar');
		$data['grid']['url_add'] = base_url($this->modul_name.'/form');
		$data['grid']['url_del'] = base_url($this->modul_name.'/hapus');
		$data['grid']['data'] = $this->data_model->get_grid_model();
		$data['grid']['fields'] = $this->data_model->get_data_fields();

		$data['main_content'] = $this->view_daftar;
		$this->load->view('layout/template', $data);
	}

	public function form($id=0)
	{
		$data = array();
		$data['breadcrumbs'] = ($id==0)?'Entri '.$this->modul_display:'Edit '.$this->modul_display;
		$data['modul'] = $this->modul_name;
		$data['modul_display'] = $this->modul_display;
		$this->load->model('auth/login_model', 'auth');
		$data['akses'] = $this->auth->get_level_akses($this->uri->slash_segment(1));
		if ($id!==0)
		{
		  $data['data'] = $this->data_model->get_data_by_id($id);
		}
		$data['main_content'] = $this->view_form;
		$this->load->view('layout/template', $data);
	}

	public function info()
	{
		$data = array();
		$data['modul'] = $this->modul_name;
		$this->load->model('auth/login_model', 'auth');
		$id = $this->session->userdata['id_user'];
		if ($id!==0)
		{
			$data['data'] = $this->data_model->get_data_by_id($id);
		}
		$data['main_content'] = 'user_info';
		$this->load->view('layout/template_admin',$data);
	}

	protected function validasi_form()
	{
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[3]|max_length[100]|callback__cek_duplikasi');
		$this->form_validation->set_rules('nama', 'Nama Operator', 'trim|required|max_length[100]');
		//$this->form_validation->set_rules('email', 'Email', 'trim|max_length[100]|valid_email|callback__cek_duplikasi_email');
		$this->form_validation->set_rules('email', 'Email', 'trim|max_length[100]|callback__cek_duplikasi_email');
		if ($this->input->post('mode') == 'new')
		{
			$this->form_validation->set_rules('passwd', 'Password', 'required|trim|min_length[8]|max_length[50]');
			$this->form_validation->set_rules('repasswd', 'Ulangi password', 'required|trim|min_length[8]|max_length[50]|matches[passwd]');
		}

		$this->form_validation->set_message('required', '%s tidak boleh kosong.');
		$this->form_validation->set_message('max_length', '%s tidak boleh melebihi %s karakter.');
		$this->form_validation->set_message('min_length', '%s tidak boleh kurang dari %s karakter.');
		$this->form_validation->set_message('valid_email', '%s tidak valid.');
		$this->form_validation->set_message('_cek_duplikasi', 'Username sudah digunakan.');
		$this->form_validation->set_message('_cek_duplikasi_email', 'Email sudah digunakan.');
		$this->form_validation->set_message('matches', '%s tidak sama.');
	}

	public function _cek_duplikasi()
	{
		return $this->data_model->check_duplikasi_username();
	}

	public function _cek_duplikasi_email()
	{
		return $this->data_model->check_duplikasi_email();
	}
	
	public function _cek_password()
	{
		return $this->data_model->check_password();
	}

	public function get_daftar()
	{
		parent::get_daftar();
		$count = count($this->data_model->get_data($this->search_param));

		$response = (object) NULL;
		$response->sql = $this->db->queries;
		if($count == 0) // tidak ada data
		{
			echo json_encode($response);
			return '';
		}

		$page = $this->page;
		$limit = $this->limit;
		$total_pages = ceil($count/$limit);

		if ($page > $total_pages) $page = $total_pages;
		$start = $limit * $page - $limit;
		if($start < 0) $start = 0;
		$this->search_param['limit'] = array(
			'start' => $start,
			'end' => $limit
		);

		$result = $this->data_model->get_data($this->search_param);

		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $count;
		$response->sql = $this->db->queries;
		$fields = $this->data_model->fieldmap_daftar;
		for($i=0; $i<count($result); $i++)
		{
			$response->rows[$i]['id'] = $result[$i][$fields[0]];
			$data = array();
			for ($n=1; $n < count($fields); $n++)
			{
				$data[] = $result[$i][$fields[$n]];
			}
			$response->rows[$i]['cell'] = $data;
		}
		echo json_encode($response);
	}

  public function ubah_password()
  {
    $response = (object) NULL;
    $this->load->model('auth/login_model', 'auth');

    $this->load->library('form_validation');
    $this->form_validation->set_rules('newpasswd', 'Password Baru', 'required|trim|min_length[8]|max_length[50]');
    $this->form_validation->set_rules('renewpasswd', 'Ulang password Baru', 'required|trim|matches[newpasswd]');

    $this->form_validation->set_message('required', '%s tidak boleh kosong.');
    $this->form_validation->set_message('max_length', '%s tidak boleh melebihi %s karakter.');
    $this->form_validation->set_message('min_length', '%s tidak boleh kurang dari %s karakter.');
    $this->form_validation->set_message('_cek_password', 'Password Lama tidak valid.');
    $this->form_validation->set_message('matches', '%s tidak sama.');


    if($this->form_validation->run() == TRUE)
    {
      $success = $this->data_model->save_password();

      if($success){
        $response->isSuccess = TRUE;
        $response->sql = $this->db->queries;
        $response->message = 'Password berhasil disimpan';
      }
      else
      {
        $response->isSuccess = FALSE;
        $response->message = 'Password gagal disimpan';
        $response->sql = $this->db->queries;
        $response->error = $this->data_model->last_error_message;
      }
    }
    else
    {
      $response->isSuccess = FALSE;
      $response->message = validation_errors();
    }
    echo json_encode($response);
  }

	public function prev($id=0)
	{
		$response = (object) NULL;
		$response->isSuccessful = FALSE;
		if ($id !== 0)
		{
			$result = $this->data_model->get_prev_id($id);
			if ($result)
			{
				$response->isSuccessful = TRUE;
				$response->id = $result;
			}
		}
		echo json_encode($response);
	}

	public function next($id=0)
	{
		$response = (object) NULL;
		$response->isSuccessful = FALSE;
		if ($id !== 0)
		{
			$result = $this->data_model->get_next_id($id);
			if ($result)
			{
				$response->isSuccessful = TRUE;
				$response->id = $result;
			}
		}
		echo json_encode($response);
	}

}