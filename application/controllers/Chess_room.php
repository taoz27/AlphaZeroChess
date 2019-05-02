<?php
class Chess_room extends CI_Controller{
	
	public function __construct(){
		parent::__construct();
		
	}
	
	public function index(){
		$data['login']=true;
		
		$this->load->view('templates/header',$data);
		$this->load->view('chess/people',$data);
		$this->load->view('templates/footer');
	}
	
}
