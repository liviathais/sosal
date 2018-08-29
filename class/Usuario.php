<?php

//require_once("Sql.php");

class Usuario{

	private $idusuario;
	private $deslogin;
	private $dessenha;
	private $descadastro;


	public function getIdusuario(){
		return $this->idusuario;
	}

	public function setIdusuario($value){
		$this -> idusuario = $value;
	}

	public function getDeslogin(){
		return $this -> deslogin;
	}

	public function setDeslogin($value){
		$this -> deslogin = $value;
	}

	public function getDessenha(){
		return $this -> dessenha;
	}

	public function setDessenha($value){
		$this -> dessenha = $value;
	}

	public function getDescadastro(){
		return $this -> descadastro;
	}

	public function setDescadastro($value){
		$this -> descadastro = $value;
	}

	public function loadByid($id){
		$sql = new Sql();
		$results = $sql -> select ("SELECT * FROM tbl_usuario WHERE idusuario = :ID", array(":ID"=>$id)); 

		if(count($results) > 0){

			$this-> setData($results[0]);
		}
	}

	// static 
	public static function getList(){
		$sql = new Sql();

		return $sql-> select("SELECT * FROM tbl_usuario ORDER BY deslogin");
	}

	public static function search($login){
		$sql = new Sql();

		return $sql->select("SELECT * FROM tbl_usuario WHERE deslogin LIKE :SEARCH ORDER BY deslogin", array(
			':SEARCH'=>"%".$login."%"));
	}

	public function login($login, $senha){
		$sql = new Sql();
		$results = $sql -> select ("SELECT * FROM tbl_usuario WHERE deslogin = :LOGIN AND dessenha = :SENHA", array(
			":LOGIN"=>$login,
			":SENHA"=>$senha
		)); 

		if(count($results) > 0){

			$this-> setData($results[0]);
		}
		else{
			throw new Exception("Login e/ou senha inválido", 1);
			
		}
	}

	public function setData($data){

		$this -> setIdusuario($data['idusuario']);
		$this -> setDeslogin($data['deslogin']);
		$this -> setDessenha($data['dessenha']);
		$this -> setDescadastro(new DateTime ($data['descadastro']));	
	}

	public function insert(){
		$sql = new Sql();

		$results = $sql-> select("CALL sp_usuario_insert(:LOGIN, :SENHA)", array(
			':LOGIN'=> $this-> getDeslogin(),
			':SENHA'=> $this-> getDessenha()
		));

		if(count($results) > 0){
			$this->setData($results[0]);
		}
	}

	public function update($login, $senha){

		$this-> setDeslogin($login);
		$this-> setDessenha($senha);
		$sql = new Sql();

		$sql -> query("UPDATE tbl_usuario SET deslogin = :LOGIN, dessenha = :SENHA WHERE idusuario = :ID", array(
			':LOGIN' => $this -> getDeslogin(),
			':SENHA' => $this -> getDessenha(),
			':ID' => $this-> getIdusuario()
		));
	}

	public function delete(){
		$sql = new Sql();

		$sql->query("DELETE FROM tbl_usuario WHERE idusuario = :ID",array(':ID'=> $this -> getIdusuario()
			));

		$this -> setIdusuario(0);
		$this -> setDeslogin("");
		$this -> setDessenha("");
		$this -> setDescadastro(new DateTime());
	}

	// = "" não deixa que os paramentros sejam obrigatórios.

	public function __construct($login = "", $senha = ""){
		$this-> setDeslogin($login);
		$this-> setDessenha($senha);
	}

	public function __toString(){
		return json_encode(array(
			"idusuario" => $this -> getIdusuario(),
			"deslogin" => $this -> getDeslogin(),
			"dessenha" => $this -> getDessenha(),
			"descadastro" => $this -> getDescadastro() -> format("d/m/Y H:i:s")
		));
	}
}

?>
