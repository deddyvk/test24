 <!-- Begin Page Content -->
<div class="container-fluid">

	<!-- Page Heading -->
	<h1 class="h3 mb-2 text-gray-800">Daftar Member</h1>
   <!-- Notification -->
	<?php

	$error = $this->session->flashdata('message');
	if(isset($error) && $error != '')
	  echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';?>
	<!-- /Notification -->

	<!-- DataTales Example -->
	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<button type="button" class="btn btn-primary" id="add-staff">Tambah Member</button>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered" id="table" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Jenis Kelamin</th>
							<th>Email</th>
							<th>Nomor Handphone</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>

</div>
<!-- /.container-fluid -->

<script type="text/javascript">

	var save_method; //for save method string
	 var tabel = null;

	$(document).ready(function() {
		//datatables
		table = $('#table').DataTable({ 
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"ordering": true, // Set true agar bisa di sorting
			"order": [[ 0, 'asc' ]], //Initial no order.
			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": '<?php echo site_url('home/view'); ?>',
				"type": "POST"
			},
			"deferRender": true,
			"aLengthMenu": [[5, 10, 50],[ 5, 10, 50]], // Combobox Limit
			//Set column definition initialisation properties.
			"columns": [
				{"data": "id", "visible": false},
				{"data": "name",width:170},
				{ "render": function ( data, type, row ) {  // Tampilkan jenis kelamin                        
					var html = "";
					if(row.jenis_kelamin == 'L'){ 
						html = 'Laki-laki' // Set laki-laki              
					}
					else
					{ 
						// Jika bukan L
						html = 'Perempuan' // Set perempuan                        
					}                        
					return html; // Tampilkan jenis kelaminnya
					}                
				},
				{"data": "email",width:100},
				{"data": "nomor_hp",width:100},
				{ "render": function ( data, type, row ) {
					//var html  = '<button id="#update-staff" title="Edit Member" class="update-staff-details ml-1 btn-ext-small btn btn-sm btn-primary"  data-staffid="' + row.id + '"><i class="fas fa-edit"></i></button>';
					var html  = '<button id="update-staff" title="Edit Member" class="update-staff-details ml-1 btn-ext-small btn btn-sm btn-primary" data-staffid="' + row.id + '"><i class="fas fa-edit"></i></button>';
					html += '<button id="delete-staff" title="Delete Member" class="delete-staff-details ml-1 btn-ext-small btn btn-sm btn-danger" data-staffid="' + row.id + '"><i class="fas fa-times"></i></button>';
					return html
					}                
				},
			],

		});
		
	});
	
	$(document).on('click', '#add-staff', function(){
		location.href = root+'home/form_member/';
	});
	
	$(document).on('click', '#update-staff', function(){
		var staff_id = $(this)[0].dataset.staffid;
		location.href = root+'home/form_member/'+staff_id;
	});
	
	$(document).on('click', '#delete-staff', function(){
		var user_id   = '<?php echo $this->session->userdata('user_id');?>';
		var staff_id = $(this)[0].dataset.staffid;
		if(user_id == staff_id) alert('Tidak dapat hapus data diri sendiri!');
		$.ajax({
			type:'POST',
			url:'<?php echo site_url('home/delete_member'); ?>',
			data:{staff_id: staff_id},
			dataType:'html',  
			success: function (html) {
				$('span#success-msg').html('');
				$('span#success-msg').html('<div class="alert alert-warning">Deleted member berhasil.</div>');  
				$('#table').DataTable().ajax.reload();		
				$('#delete-staff').modal('hide');			
			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}        
		});
	});
	
	
</script>