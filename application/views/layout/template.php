<?php
$this->load->view('layout/header');
if(isset($main_content)) { $this->load->view($main_content); }
$this->load->view('layout/footer');
?>