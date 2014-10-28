<?php 
/* KDB CLASS
 * 作者：王坤
 * 版本：2.0
 * 功能：不同服务器多数据库切换便利 数据库操作类
 * 测试状态：可以对多不同服务器的两个表进行操作
 * 说明：1，NEWLINK为0（默认）时，对同一个服务器上的链接认定为一个链接标识，即对同一个服务器上的多个数据库或者表的操作应使用 复制对象，然后做对应的属性修改
 *  */
class KDB {
	public static $DEBUG=1;
	public static $TYPE=MYSQL_BOTH;//查询数组返回类型MYSQL_ASSOC，MYSQL_NUM 和 MYSQL_BOTH
	public static $NEWLINK=0;//说明1
	private $con;
	public $table;
	protected $db_host='localhost';
	protected $db_user='root';
	protected $db_pass='';
	protected $db_base='wx';
	
	function __construct($table='',$db='',$host='',$user='',$pass='') {
		//设置默认连接
		if ( !empty($host) && !empty($user) ) {
			$this->conn($host, $user, $pass);
		}else {
			$this->conn($this->db_host, $this->db_user, $this->db_pass);
		}
		//设置默认操作数据表
		if ( !empty($table) ) {
			$this->switchTable($table);
		};
		//设置默认数据库
		if (!empty($db)) {
			$this->switchDb($db);
		}else {
			$this->switchDb($this->db_base);
		}
	}
	function __destruct() {
		//echo '析构函数被调用了';
		//$this->close();
	}
	//连接关闭函数
	function conn($server,$user,$pass) {
		if (self::$NEWLINK) {
			$this->con = @mysql_connect($server,$user,$pass,1);
		}else {
			$this->con = @mysql_connect($server,$user,$pass);
		}
		if($this->con){
			return $this->con;
		}else{
			die("数据库连接失败");
		}
	}
	function close() {
		mysql_close($this->con);
	}
	//辅助函数
	function fetch($result) {
		return mysql_fetch_array($result,self::$TYPE);
	}
	function query($sql) {
		$error = '';
		$result = mysql_query($sql,$this->con);
		if ($result) {
			return $result;
		}else {
			if (self::$DEBUG) {
				$error = mysql_error($this->con);
			}
			throw new Exception("sql命令执行失败".$error);
		}
	}
	function selectAllQuery($items='*',$condition=1,$sort=1) {
		$sql = "select $items from $this->table where $condition order by $sort";
		return $this->query($sql);
	}
	function switchTable($table) {
		$this->table = $table;
	}
	function switchDb($db) {
		if( !mysql_select_db($db,$this->con) ) {
			throw new Exception("选择数据库".$db."失败".mysql_error());
		}else {
			$this->db_base = $db;
		}
	}
	//操作函数 
		//插入函数 无返回值
	function insert($record) {
		$keys = '';
		$vals = '';
		if( !is_array($record[0]) ){
			foreach( $record as $key=>$val){
				$keys .= ",".$key;
				$vals .= ",'$val'";
			}
			$keys = trim($keys,',');
			$vals = trim($vals,',');
			$this->query("insert into $this->table($keys) values($vals)");
		}else {
			for ($i=0;$i<count($record);$i++){
				$keys = '';
				$vals = '';
				foreach( $record[$i] as $key=>$val){
					$keys .= ",".$key;
					$vals .= ",'$val'";
				}
				$keys = trim($keys,',');
				$vals = trim($vals,',');
				$this->query("insert into $this->table($keys) values($vals)");
			}
		}
	}
		//更新函数 返回真假
	function update($array,$where) {
		$sets = '';
		if (!is_array($array[0])) {
			foreach( $array as $key=>$val){
				$sets .= ",$key='$val'";
			}
			$sets = ltrim($sets,',');
			if ($this->query("update $this->table set $sets where $where")) {
				return true;
			}else {
				return false;
			}
		}else {
			return false;
		}
		
	}
		//查找一个记录
	function getOne($condition) {
		$sql = "select * from $this->table where $condition";
		return $this->fetch($this->query($sql));
	}
		//根据id得到一个记录
	function getOneById($id) {
		$sql = "select * from $this->table where id=$id";
		return $this->fetch( $this->query($sql) );
	}
		//逐条读取记录  不能使用
	function Read($source) {
		return $this->fetch($source);
	}
}
?>