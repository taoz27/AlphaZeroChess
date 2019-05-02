<?php
class Chess_model extends CI_Model{
public $chess;
public $pipes;
public $cwd;
public $cmd;
public $desc;

public function __construct(){
	$this->cmd=escapeshellcmd("./application/models/release/lc0");
	$this->desc=array(
		0=>array("pipe","r"),
		1=>array("pipe","w"),
		2=>array("file","lc0log.txt","w")
	);
	//$this->cwd="./release/";
	$this->chess=proc_open($this->cmd,$this->desc,$this->pipes,null,null);
}

public function get_bestmove($fen=NULL,$move=NULL){
if(!is_resource($this->chess))return "not resource";
//if($move!=NULL)
//	fwrite($this->pipes[0],"position fen ".$fen." moves ".$move."\n");
//else if($fen!=NULL)
	fwrite($this->pipes[0],"position fen ".$fen."\n");
fwrite($this->pipes[0],"go depth 1\n");

$res=array();
while(1){
	$rec=fread($this->pipes[1],1024);

	if(strpos($rec, "error") === 0){
		$res['error']=$rec;
		break;
	}
	if(strpos($rec, "bestmove") === 0){
		$res['move']=(explode(" ",$rec))[1];
		break;
	}
}
fwrite($this->pipes[0],"getvalues\n");
$rec=fread($this->pipes[1],1024);
$res['values']=$rec;
//fwrite($this->pipes[0],"quit\n");

return $res;
}

function uuid($length=8){
	$chars = preg_split('//','0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
	$q = array();
	for ($i = 0; $i < $length; $i++) $q[$i] = $chars[rand(0 ,61)];
	return implode("",$q);
}

public function create_game($idw,$idb,$fen="start",$type){
//insert into table pgn
$addr=$this->uuid();
$dbdata=array(
	'idw'	=> $idw,
	'idb'	=> $idb,
	'first' => 'w',
	'start' => $fen,
	'cur'	=> $fen,
	'addr'	=> $addr,
	'type'	=> $type
);
$this->db->insert('pgn', $dbdata);

//insert into table pgninfo
$id=$this->db->insert_id();
$pgninfo=array('id'=>$id);
$this->db->insert('pgninfo', $pgninfo);

$res=array(
	'id'	=>$id,
	'addr'	=>$addr
);

return $res;
}
}


?>
