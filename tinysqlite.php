<?php
/**
 * TinySQLite
 * A tiny PHP class for work with SQLite3 datatables using PDO.
 * (c) 2019 Alfonso Saavedra "Son Link"
 * Under the GNU/GPL 3 or newer license
 * http://son-link.github.io
 */
class TinySQLite{

	private $db;
	public $num_rows;
	public $errorInfo;
	public $lastInsertId;
	public $result;

	public function __construct($file){
		/**
		* @param $file string La ruta al fichero que contiene la BS a usar.
		*/
		$this->db = new PDO('sqlite:'.$file) or die('No se pudo acceder a la base de datos');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	}

	public function query($sql, $values=array()){
		/**
		 * Execute a SQL and return if the execute is correct
		 * @param $sql string A string qith the SQL sentence. Required
		 * @param $values array A array with values to pass to SQL;
		 */
		$vals = array();
		foreach ($values as $v) {
			array_push($vals, $v);
		}

		$query = $this->db->prepare($sql);
		$query->execute($vals);
		$this->errorInfo = $query->errorInfo();
		$this->num_rows = $query->rowCount();
		$this->lastInsertId();
		if($query && $this->num_rows > 0) $this->result = $query->fetchAll();
		return $query;
	}

	public function select($params = array()){
		/**
		 * Execute a select and return a array with the result or false if don't have any result
		 * @param $params array A array with the options. Only required table.
		 * $table string The table to use. Required;
		 * $fields array A array whit the fiels to selectd. Default is * (all). Optional
		 * $conditions array or string A assocative array witht WHERE conditions and AND. tambien puede ser una cadena.
		 * $orderby string A string for order output
		 * $limit int set how many items to get.
		 */

		extract($params);

		if (!isset($table)) return false;
		if (!isset($fields)) $fields = ['*'];
		if (!isset($conditions)) $conditions = array();
		if (!isset($orderby)) $orderby = '';

		$sql = 'SELECT '.join(', ', $fields).' FROM '.$table;
		$vals = array();
		if (!empty($conditions)){
			$sql .= ' WHERE ';
			if(is_array($conditions)){
				foreach ($conditions as $k => $v) {
					$sql .= " $k=? AND";
					array_push($vals, $v);
				}
				$sql = substr($sql,0,-4);
			}elseif (is_string($conditions)) {
				$sql .= " $conditions";
			}
		}
		if ($orderby){
			$sql .= ' ORDER BY '.$orderby;
		}

		if (!empty($limit)) $sql .= " LIMIT $limit";
		$query = $this->query($sql, $vals);

		if($query){
			return $query->fetchAll();
		}else{
			return false;
		}
	}

	function insert($table, $values){
		/**
		 * Insert new row on table.
		 * @param $table string La tabla donde se hara la actualización
		 * @param $values array A associative array as field => value.
		 */
		$sql = "INSERT INTO $table ";
		$vals = '';
		$fields = array();
		$valores = array();

		foreach ($values as $k => $v) {
			array_push($fields, $k);
			array_push($valores, $v);
			$vals .= "?,";
		}
		$vals = substr($vals,0,-1);
		$sql .= '('.join(', ', $fields).') VALUES ('.$vals.')';
		$query = $this->query($sql, $valores);

		if($query){
			return $this->lastInsertId();
		}else{
			return false;
		}
	}

	function update($table, $values, $conditions=''){
		/**
		 * Update values on table.
		 * @param $table string La tabla donde se hara la actualización
		 * @param $values array A associative array as field => value.
		 * @param $conditions array A assocative array witht WHERE conditions and AND. Optional
		 */

		$sql = "UPDATE $table SET ";
		$vals = array();
		foreach ($values as $k => $v) {
			$sql .= "$k=?,";
			array_push($vals, $v);
		}
		$sql = substr($sql,0,-1);
		if (!empty($conditions)){
			$sql .= ' WHERE ';
			foreach ($conditions as $k => $v) {
				$sql .= " $k=? AND";
				array_push($vals, $v);
			}
			$sql = substr($sql,0,-4);
		}
		$query = $this->query($sql, $vals);
		if ($query) return true;
		else return false;
	}

	function delete($table, $conditions, $limit=''){
		/**
		 * Update values on table.
		 * @param $table string La tabla donde se hara la actualización
		 * @param $fields array A associative array as field => value.
		 * @param $limit set number of cols to delete
		 */

		$sql = "DELETE FROM $table WHERE";
		$vals = array();
		foreach ($conditions as $k => $v) {
			$sql .= " $k=? AND";
			array_push($vals, $v);
		}
		$sql = substr($sql,0,-4);
		if (!empty($limit) && is_int($limit)){
			$sql .= " LIMIT ?";
			array_push($vals, $limit);
		}

		$query = $this->query($sql, $vals);
		if ($query) return true;
		else return false;
	}

	public function lastInsertId() {
		/**
		* return last insert id of the last insertion
		*/
    	$result = $this->db->query('SELECT last_insert_rowid() as last_insert_rowid')->fetch();
    	return $result->last_insert_rowid;
	}
}
