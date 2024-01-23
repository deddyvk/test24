<?php

class Home extends CI_Controller{
	public function __construct()
	{
		parent:: __construct();
		$this->modul_name = 'home';
		$this->load->model('dasar_model', 'data_model');
	}

	function index()
	{
		if($this->session->userdata('admin') <> 1)
			$this->form_member($this->session->userdata('user_id'));
		else{
			$data['main_content'] = 'v_home';
			$data['modul'] = $this->modul_name;
			$this->load->view('layout/template', $data);
		}
	}
	
	public function form_member($id=0)
	{
		$data['main_content'] = 'FormMember';
		$data['modul'] = $this->modul_name;
		$data['id'] = $id;
		if ($id!==0){
			$data['user'] = $this->data_model->get_user_info($id);
		}
		$this->load->view('layout/template', $data);
	}
	
	public function list_member()
	{
		$data['main_content'] = 'ListMember';
		$data['modul'] = $this->modul_name; 
		$data['user'] = $this->data_model->get_all_user();
		$this->load->view('layout/template', $data);
	}
	
	public function view(){    
		$search = $_POST['search']['value']; // Ambil data yang di ketik user pada textbox pencarian    
		$limit = $_POST['length']; // Ambil data limit per page    
		$start = $_POST['start']; // Ambil data start    
		$order_index = $_POST['order'][0]['column']; // Untuk mengambil index yg menjadi acuan untuk sorting    
		$order_field = $_POST['columns'][$order_index]['data']; // Untuk mengambil nama field yg menjadi acuan untuk sorting    
		$order_ascdesc = $_POST['order'][0]['dir']; // Untuk menentukan order by "ASC" atau "DESC"    
		$sql_total = $this->data_model->count_all(); // Panggil fungsi count_all pada     
		$sql_data = $this->data_model->filter($search, $limit, $start, $order_field, $order_ascdesc); // Panggil fungsi filter pada     
		$sql_filter = $this->data_model->count_filter($search); // Panggil fungsi count_filter pada data_model    
		$callback = array(
			'draw'=>$_POST['draw'], // Ini dari datatablenya
			'recordsTotal'=>$sql_total,
			'recordsFiltered'=>$sql_filter,
			'queris'=>$this->db->queries,
			'data'=>$sql_data
		);
		header('Content-Type: application/json');
		echo json_encode($callback); // Convert array $callback ke json  }
	
	}
	
	function process_member()
	{
		$this->load->library('form_validation');
		$this->load->helper('email');
		$this->load->library('user_agent');

		$this->form_validation->set_rules('name','Nama', 'required');
		$this->form_validation->set_rules('tanggal_lahir','Tanggal Lahir', 'required');
		$this->form_validation->set_rules('jenis_kelamin','Jenis Kelamin', 'required');
		$this->form_validation->set_rules('nomor_ktp','Nomor KTP', 'required');
		$this->form_validation->set_rules('nomor_hp','Nomor Handphone', 'required');
		$this->form_validation->set_rules('email','Email', 'required|valid_email|callback__cek_duplikasi_email');
		
		$this->form_validation->set_message('required', '%s tidak boleh kosong.');
		$this->form_validation->set_message('is_unique', '%s sudah dipergunakan.');
		$this->form_validation->set_message('matches', '%s harus sama.');
		$this->form_validation->set_message('image', '%s harus bertipe image.');
		$this->form_validation->set_message('_cek_duplikasi_email', 'Email sudah digunakan.');
		
		if($this->form_validation->run() == TRUE)
		{
			if(!empty($_FILES["image"]['name'])){
				$allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
				$temp = explode(".", $_FILES["image"]["name"]);
				$extension = end($temp);
				$maxsize = 1024 * 1000; // maksimal 10000 KB (1KB = 1024 Byte)
				if (in_array($extension, $allowedExts))
				{
					if ($_FILES["image"]["size"] > $maxsize)
					{
						$this->session->set_flashdata('message', "<strong>Ukuran File Melebihi 1 MB</strong>");
						redirect($this->agent->referrer());
					}
					else if ($_FILES["image"]["error"] > 0)
					{
						$this->session->set_flashdata('message', "ERROR Return Code: ". $_FILES["image"]["error"]);
						redirect($this->agent->referrer());
					}
				}
				else
				{
					$this->session->set_flashdata('message', "<strong>Format file tidak di dukung</strong>");
					redirect($this->agent->referrer());
				}	
			}
			
			if($this->data_model->simpan_member()){
				$this->session->set_flashdata('message', "<strong>Data Berhasil Disimpan</strong>");
				redirect('home');
			}
			else{
				$this->session->set_flashdata('message', "<strong>Data Gagal Disimpan</strong>");
				redirect($this->agent->referrer());
			}
					
		}
		else
		{
			$this->session->set_flashdata('message', validation_errors());
			redirect($this->agent->referrer());
		}
	}
	
	public function _cek_duplikasi_email()
	{
		return $this->data_model->check_duplikasi_email();
	}
	
	public function delete_member() {
        $json = array();
        $staffID = $this->input->post('staff_id');
        $this->data_model->deleteMember($staffID);
        $this->output->set_header('Content-Type: application/json');
        echo json_encode($json);        
    }
}