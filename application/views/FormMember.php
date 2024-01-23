 <!-- Begin Page Content -->
 
<div class="container-fluid">
<!-- Notification -->
	<?php

	$error = $this->session->flashdata('message');
	if(isset($error) && $error != '')
	  echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';?>
	<!-- /Notification -->
	
	<div class="row">
		<div class="col-xl-10">
			<div class="card mb-4">
				<div class="card-header">Detail Akun</div>
				<div class="card-body">
					 <form class="" method="post" action="<?php echo base_url(); ?>home/process_member" enctype="multipart/form-data">
						<div class="mb-3">
							<label class="small mb-1" for="name">Nama Lengkap</label>
							<input class="form-control" name="name" id="name" type="text" placeholder="Nama Lengkap" value="" required/>
						</div>
						<div class="mb-3">
							<label class="small mb-1" for="tanggal_lahir">Tanggal Lahir</label>
							<input class="form-control" name="tanggal_lahir" id="tanggal_lahir" type="date" placeholder="Tanggal Lahir" value="" required/>
						</div>
						<div class="mb-3">
							<label class="small mb-1" for="name">Jenis Kelamin</label>
							<label class="radio-inline">
								<input type="radio" id="jenis_kelamin" name="jenis_kelamin" value="L" checked>Laki - laki
							</label>
							<label class="radio-inline">
								<input type="radio" id="jenis_kelamin" name="jenis_kelamin" value="P">Perempuan
							</label>
						</div>
						<div class="mb-3">
							<label class="small mb-1" for="nomor_ktp">Nomor KTP</label>
							<input class="form-control" name="nomor_ktp" id="nomor_ktp" type="text" placeholder="Nomor KTP" value="" required/>
						</div>
						<div class="mb-3">
							<label class="small mb-1" for="nomor_hp">Nomor Handphone</label>
							<input class="form-control" name="nomor_hp" id="nomor_hp" type="text" placeholder="Nomor Handphone" value="" required/>
						</div>
						<div class="mb-3">
							<label class="small mb-1" for="email">Email</label>
							<input class="form-control" name="email" id="email" type="email" placeholder="Email" value="" required/>
						</div>
						<div class="row gx-3 mb-3">
							<div class="col-md-6">
								<label class="small mb-1" for="passwd">Password</label>
								<input class="form-control" name="passwd" id="passwd" type="password" placeholder="Password" value="" />
							</div>
							<div class="col-md-6">
								<label class="small mb-1" for="repasswd">Ulangi Password</label>
								<input class="form-control" name="repasswd" id="repasswd" type="password" placeholder="Ulangi Password" value=""/>
							</div>
						</div>
						<?php 
						if($this->session->userdata('admin') == 1)	
						{
						?>
						<div class="mb-3">
							<label class="small mb-1" for="status">Status</label>
							<select class="form-control" id="status" name="status"  class="span5" data-bind="" />
								<option value="1">Aktif</option>
								<option value="2">Tidak Aktif</option>
							</select>
						</div>
						<div class="mb-3">
							<label class="small mb-1" for="sys_admin">Admin</label>
							<select class="form-control" id="sys_admin" name="sys_admin"  class="span5" data-bind="" />
								<option value="1">Ya</option>
								<option value="2">Tidak</option>
							</select>
						</div>
						<?php
						}
						?>
						<div class="mb-3">
							<label for="image">Foto Diri</label>
							<?php
								if($id > 0 && strlen($user['foto']) > 0){
							?>
								<img class="img-preview img-fluid mb-3 col-sm-5" src="<?php echo base_url().'assets/img/'.$user['foto']; ?>">
							
							<?php
								}
								else{
							?>
								<img class="img-preview img-fluid mb-3 col-sm-5">
							<?php
								}
							?>
							<input type="file" class="form-control form-control-user" id='image' name="image" onchange="previewImage()" accept="image/png, image/jpeg">
						</div>
						<input type="hidden" id="id" name="id" value="">
						<input type="hidden" id="oldImage" name="oldImage" value="">
						<button class="btn btn-primary btn-user btn-block">Simpan</button>
					</form>
				</div>
			</div>
		</div>
	</div>

<script>
	
  function previewImage(){
    const image = document.querySelector('#image');
    const imgPreview = document.querySelector('.img-preview');
    imgPreview.style.display = 'block';
    const oFReader = new FileReader();
    oFReader.readAsDataURL(image.files[0]);

    oFReader.onload = function(oFREvent){
      imgPreview.src = oFREvent.target.result;
    }
  }
  
  const id = <?php echo $id > 0 ? $id :0; ?>;
	console.log(id,'id');
	if(id > 0){
		$('#id').val('<?php echo isset($user['id'])?$user['id']:0; ?>');
		$('#name').val('<?php echo isset($user['name'])?$user['name']:''; ?>');
		$('#tanggal_lahir').val('<?php echo isset($user['tanggal_lahir'])?$user['tanggal_lahir']:''; ?>');
		$('input:radio[name="jenis_kelamin"][value="<?php echo isset($user['jenis_kelamin'])?$user['jenis_kelamin']:''; ?>"]').prop('checked', true);
		$('#status').val('<?php echo isset($user['status'])?$user['status']:''; ?>');
		$('#sys_admin').val('<?php echo isset($user['sys_admin'])?$user['sys_admin']:''; ?>');
		$('#nomor_ktp').val('<?php echo isset($user['nomor_ktp'])?$user['nomor_ktp']:''; ?>');
		$('#nomor_hp').val('<?php echo isset($user['nomor_hp'])?$user['nomor_hp']:''; ?>');
		$('#email').val('<?php echo isset($user['email'])?$user['email']:''; ?>');
	}

</script>
	