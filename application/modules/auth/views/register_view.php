<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Member - Register</title>

    <!-- Custom fonts for this template-->
    <link href="<?php echo base_url()?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?php echo base_url()?>assets/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">
	<!-- Notification -->
	<?php

	$error = $this->session->flashdata('message');
	if(isset($error) && $error != '')
	  echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';?>
	<!-- /Notification -->

    <div class="container">
	

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Buat Akun</h1>
                            </div>
                            <form class="user" method="post" action="<?php echo base_url(); ?>auth/process_register" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
										<label for="name">Nama Lengkap</label>
                                        <input type="text" class="form-control form-control-user" id="nama" name="name"
                                            placeholder="Nama Lengkap" required>
                                    </div>
                                </div>
								<div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
										<label for="tanggal_lahir">Tanggal Lahir</label>
                                      <input type="date" class="form-control form-control-user" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo date('dd/mm/yyyy');?>" required >
                                    </div>
                                </div>
								<div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
										<label>Jenis Kelamin</label>
										<label class="radio-inline">
											<input type="radio" id="jenis_kelamin" name="jenis_kelamin" value="L" checked>Laki - laki
										</label>
										<label class="radio-inline">
											<input type="radio" id="jenis_kelamin" name="jenis_kelamin" value="P">Perempuan
										</label>
                                    </div>
                                </div>
								<div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
										<label for="nomor_ktp">Nomor KTP</label>
                                        <input type="text" class="form-control form-control-user" id="nomor_ktp" name="nomor_ktp"
                                            placeholder="Nomor KTP" required>
                                    </div>
                                </div>
								<div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
										<label for="nomor_hp">Nomor Handphone</label>
                                        <input type="text" class="form-control form-control-user" id="nomor_hp" name="nomor_hp"
                                            placeholder="Nomor Handphone" required>
                                    </div>
                                </div>
                                <div class="form-group row">
									<div class="col-sm-6 mb-3 mb-sm-0">
										<label for="email">Email</label>
										<input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Email">
									</div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
										<label for="passwd">Password</label>
                                        <input type="password" class="form-control form-control-user" id="passwd" name="passwd" placeholder="Password" required>
                                    </div>
                                    <div class="col-sm-6">
										<label for="repasswd">Ulangi Password</label>
                                        <input type="password" class="form-control form-control-user" id="repasswd" name="repasswd" placeholder="Ulangi Password" required>
                                    </div>
                                </div>
								<div class="form-group row">
									<div class="col-sm-6 mb-3 mb-sm-0">
										<label for="image">Foto Diri</label>
										<img class="img-preview img-fluid mb-3 col-sm-5">
										<input type="file" class="form-control form-control-user" id='image' name="image" onchange="previewImage()" accept="image/png, image/jpeg" required>
									</div>
                                </div>
                                <button class="btn btn-primary btn-user btn-block">
									Daftar Akun
								</button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="<?php echo base_url()?>">Sudah punya akun ? Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?php echo base_url()?>assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url()?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?php echo base_url()?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?php echo base_url()?>assets/js/sb-admin-2.min.js"></script>
	
	<script>
  //$('#tanggal_lahir').val('<?php echo date('dd/mm/yyyy'); ?>');

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

</script>
</body>

</html>