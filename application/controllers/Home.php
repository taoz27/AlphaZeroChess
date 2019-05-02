<?php
class Home extends CI_Controller{
	
public function __construct(){
parent::__construct();
$this->load->helper('form');
$this->load->helper('cookie');
$this->load->database();
$this->load->library('session');
$this->load->model('chess_model');
}

public function index(){
$this->load->view('templates/header');
$this->load->view('chess/home');
$this->load->view('templates/footer');
}

/*获取某一局得信息
info{
	w:name;
	b:name;
	end:w/b/u;
	turn:w/b;
	curpos:start/fen;
}
*/
//todo 输出错误信息  输入校验
public function getinfo(){
$addr=$this->input->post('addr');
$sql="select pgn.idw as idw,pgn.idb as idb,wt.name as whitename,bt.name as blackname,
pgn.res as end,pgn.turn as turn,pgn.cur as curpos,pgn.id as pgnid  
from pwd as wt,pwd as bt,pgn 
where pgn.addr='".$addr."' and wt.id=pgn.idw and bt.id=pgn.idb;";
$query=$this->db->query($sql);
if($query!=null&&$query->result()!=null){
	echo json_encode($query->result()[0]);
}
}

public function updateinfo(){
/*
$this->db->set('field', 'field+1');
$this->db->where('id', 2);
$this->db->update('mytable');
*/
$this->db->set('res',$this->input->post('res'));
$this->db->set('cur',$this->input->post('cur'));
$this->db->set('turn',$this->input->post('turn'));
$this->db->where('addr',$this->input->post('addr'));
$this->db->update('pgn');

$this->db->set('pgn',$this->input->post('pgn'));
$this->db->where('id',$this->input->post('id'));
$this->db->update('pgninfo');
}

public function game($num){
$query=$this->db->get_where('pgn',array('addr'=>$num));
if($query==null||$query->result()==null){
	echo "Game ".$num." not exist";
	return;
}
if($query->result()[0]->res!="u"){
	echo "Game ".$num." ended";
	return;
}
$this->load->view('templates/header');
if($query->result()[0]->idw==-1||$query->result()[0]->idb==-1){
	$this->load->view('chess/playai');
}else{
	$this->load->view('chess/play');
}
$this->load->view('templates/footer');
}

//create a game with type "friend" or "ai"
public function create(){
$user=$this->session->id==null?1:$this->session->id;
$type=$this->input->post('type');
$other=$type=='ai'?-1:1;
$select=$this->input->post('select');
//if($select=='r')
$idw=$select=='w'?$user:$other;
$idb=$select=='w'?$other:$user;

$fen=$this->input->post('fen');
$start="rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR";
if($fen==="start")$fen=$start;

$game=$this->chess_model->create_game($idw,$idb,$fen,$type);

$res=array(
	'id'	=> $game['id'],
	'type'	=> 'success',
	'addr'	=> $game['addr'],
	'msg'	=> 'no error'
);
echo json_encode($res);
}

public function query(){
	$name=$this->input->post('name');
	$id=$this->input->post('id');
	$sel=$this->input->post('select');
	$query=$this->db->get_where('wait',array('name'=>$name));
	if($query!=null&&$query->result()!=null){
		$addr=$query->result()[0]->addr;
		if($addr!=null&&$addr!=""){
			$res=array(
				'type'	=>'success',
				'addr'	=>$query->result()[0]->addr
			);
		}else{
			$res=array(
				'type'	=> 'fail'
			);
		}
		echo json_encode($res);
		return;//让后来者创建比赛
	}else{	
		$dbdata=array(
			'name'	=>	$name,
			'id'	=>	$id,
			'sel'	=>	$sel,
			'status'=>	'wait',
			'addr'	=>  ''
		);
		$this->db->insert('wait',$dbdata);
	}
	$sql="select * from wait where status='wait' and name<>'".$name."'";
	if($sel!='r'){
		$sql=$sql." and sel<>'".$sel."'";
	}
	$search=$this->db->query($sql);
	if($search!=null&&$search->result()!=null){
		$chars = preg_split('//','0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
		$q = array();
		for ($i = 0; $i < 6; $i++) $q[$i] = $chars[rand(0 ,61)];
		$raddr=implode("",$q);
		
		$cdbdata=array(
			'first' => 'w',
			'start' => 'start',
			'cur'	=> 'start',
			'addr'	=> $raddr,
			'type'	=> 'game'
		);
		if($sel=='r')$sel=rand(0,1)==0?'w':'b';
		if($sel=='w'){
			$cdbdata['idw']=$id;
			$cdbdata['idb']=$search->result()[0]->id;
		}else{
			$cdbdata['idb']=$id;
			$cdbdata['idw']=$search->result()[0]->id;
		}
		$this->db->insert('pgn', $cdbdata);
		
		$this->db->set('status','ready');
		$this->db->set('addr',$raddr);
		$this->db->where('name',$name);
		$this->db->update('wait');
		$this->db->set('status','ready');
		$this->db->set('addr',$raddr);
		$this->db->where('name',$search->result()[0]->name);
		$this->db->update('wait');
		
		$res=array(
			'id'	=> $this->db->insert_id(),
			'type'	=> 'success',
			'addr'	=> $raddr,
			'msg'	=> 'no error'
		);
		echo json_encode($res);
		return;
	}
	$res=array(
		'type'	=> 'fail'
	);
	echo json_encode($res);
}

public function login(){
if($this->session->login){
	header("location:/");
	$this->load->view('templates/header');
	$this->load->view('chess/home');
	$this->load->view('templates/footer');
}else{
	$getname=$this->input->post('inputName');
	if($getname==null){
		$this->load->view('templates/header');
		$this->load->view('chess/login');
		$this->load->view('templates/footer');
	}else{
		//id大于1表示非匿名、非AI
		$sql="select * from pwd where 
		id>1 and name='".$this->input->post('inputName')."' and 
		password='".$this->input->post('inputPassword')."'";

		$query=$this->db->query($sql);

		if($query!=null&&$query->result()!=null){
			//$remember=$this->input->post('remember');
			$logindata = array(
			'name'	=> $this->input->post('inputName'),
			'login'	=> true,
			'id'	=> $query->result()[0]->id
			);

			$this->session->set_userdata($logindata);

			header("location:/");
			$this->load->view('templates/header');
			$this->load->view('chess/home');
			$this->load->view('templates/footer');
		}else{
			  $this->load->view('templates/header');
			  $this->load->view('chess/login');
			  $this->load->view('templates/footer');
		}
	}
}
}
 
public function register(){
	$data['login']=false;
	$data['msg']="right";
	$data['getemail']=$this->input->post('email');
	if($data['getemail']==null){
		$data['msg']="email null";
		$this->load->view('templates/header',$data);
		$this->load->view('chess/register');
		$this->load->view('templates/footer');
	}else{
		$query=$this->db->get_where('info',array('email'=>$data['getemail']));
		if($query!=null&&$query->result()!=null){
			$data['msg']="email repeat";
			$this->load->view('templates/header',$data);
			$this->load->view('chess/register');
			$this->load->view('templates/footer');
		}else{
			$dbpwd=array(
				'name'=>$this->input->post('username'),
				'password'=>$this->input->post('password'),
			);
			$this->db->insert('pwd',$dbpwd);
			$dbinfo=array(
				'id'=>$this->db->insert_id(),
				'email'=>$this->input->post('email'),
				'phone'=>$this->input->post('phone')
			);
			$this->db->insert('info',$dbinfo);
			unset($_SESSION['login']);
			header("location:/");
			$this->load->view('templates/header');
			$this->load->view('/chess/login');
			$this->load->view('templates/footer');
		}
	}
}

public function logout(){
	unset($_SESSION['login']);
	header("location:/");
	$this->load->view('templates/header');
	$this->load->view('chess/home');
	$this->load->view('templates/footer');
}

}


