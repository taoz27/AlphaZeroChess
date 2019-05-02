<?php
class Chess extends CI_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->model('chess_model');
		$this->load->library('session');
		$this->load->helper('url');
	}
	
	public function index(){
		if(isset($_SESSION['login'])){
			$data['login']=$_SESSION['login'];
			$data['name']=$_SESSION['name'];
		}else
			$data['login']=false;
		
		$this->load->view('templates/header',$data);
		$this->load->view('chess/play');
		$this->load->view('templates/footer');
	}
	
	
	public function get_bestmove(){
		$fen=$this->input->post('fen');
		$move=$this->input->post('move');
		//$fp = fopen('./mylog.txt', 'a+b');
		//fwrite($fp, print_r($fen, true));
		//fclose($fp);
		$res=$this->chess_model->get_bestmove($fen);
		//$fp = fopen('./mylog.txt', 'a+b');
		//fwrite($fp, print_r("=====>".$res, true));
		//fclose($fp);
		//$arr=explode(" ",$res);
		echo json_encode($res);
	}
}

