<?php
/**
 * 小型的 OR Mapping 工具类
 *
 * 使用此类需要在外部预定义MySQL配置信息
 *
 * define('DB_HOST', '127.0.0.1');
 * define('DB_NAME', 'data');
 * define('DB_USER', 'root');
 * define('DB_PASSWORD', '');
 *
 * Model类直接继承这个Class，所有Model中定义打Public属性都自动映射到数据库中的同名字段。
 */

class Mysql {
	public $id = null;

	//model类对应的表名
	protected static $table_name = null;

	/**
	 * @return mysqli
	 */
	protected static $client = null;
	protected function client() {
		if (!self::$client) {
			$conn = mysqli_init();
			$i = 2;     //如果连接超时重试两次
			do {
				$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 1);
				@$conn->real_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, null, null, MYSQL_CLIENT_COMPRESS);
			} while ($i -- > 0 && $conn->connect_error);
			if ($conn->connect_error) {
				throw new Exception("Mysql connect fail on error: ({$conn->connect_errno}){$conn->connect_error}");
			}
			$conn->set_charset('utf8');
			self::$client = $conn;
		} else {
			$conn = self::$client;
		}
		// mysql::ping() is no longer working for using mysqlnd. see: https://bugs.php.net/bug.php?id=52561
		if ( isset($conn->_last) && (time() - $conn->_last) > 10 ) {
			//reconnect when conn lost, avoid 'MySQL server has gone away' error
			self::$client = null;
			$conn = $this->client();
		}
		$conn->_last = time();
		return $conn;
	}

	public function __construct($id = null) {
		if ($id) {
			$this->load($id);
		}
		if (!static::$table_name) {
			throw new Exception("No \$table_name defined for " . get_class($this) . ' !' );
		}
	}

	protected function reset() {
		foreach ($this as $col => $val) {
			if ($col == 'id') {
				$this->$col = null; //保证id字段一直存在
			} else {
				unset($this->$col);
			}
		}
	}

	public function load($id) {
		$this->reset();

		$id = self::client()->real_escape_string($id);
		$result = $this->client()->query("SELECT * FROM `" . static::$table_name . "` WHERE `id` = '$id' LIMIT 1");

		if ($result->num_rows) {
			$data = $result->fetch_assoc();
			foreach ($data as $key => $value) {
				$this->{$key} = $value;
			}
		}
		return $this;
	}

	public static function loads($ids) {
		if (empty($ids)) {
			return array();
		}
		$ids_str = self::client()->real_escape_string(join(',', $ids));
		$result = self::client()->query("SELECT * FROM `" . static::$table_name . "` WHERE `id` IN ({$ids_str})");
		$ret = array();
		if ($result->num_rows) {
			while ($data = $result->fetch_object(get_called_class())) {
				$ret[$data->id] = $data;
			}
		}
		return $ret;
	}

	private function getQuery() {
		$where = array('1=1');

		foreach ($this->columns() as $key) {
			if (isset($this->{$key})) {
				$where[] = "`{$key}` = " . $this->encode($key);
			}
		}

		$where_str = join(' AND ', $where);
		return $where_str;
	}

	public function count($option = array()) {
		$where_str = $this->getQuery();

		$sql = "SELECT COUNT(1) FROM `" . static::$table_name . "` WHERE {$where_str}";

		$result = self::client()->query($sql);
		return current($result->fetch_row());
	}

	public function delete($option = array()) {
		$id_str = $this->id;
		if (isset($option['id'])) {
			$id_str = $option['id'];
		}

		$sql = "DELETE FROM `" . static::$table_name . "` WHERE `id` = '{$id_str}' LIMIT 1";;

		$result = self::client()->query($sql);
		return $result;
	}

	public function find($option = array()) {
		$where_str = $this->getQuery();

		$option_str = '';

		if (isset($option['order'])) {
			$option_str .= 'ORDER BY ' . $option['order'];
		}
		if (!isset($option['limit'])) {
			$option['limit'] = 40;
		}
		$option_str .= ' LIMIT ' . $option['limit'];

		$sql = "SELECT * FROM `" . static::$table_name . "` WHERE {$where_str} {$option_str}";
		$result = self::client()->query($sql);
		$ret = array();
		if ($result->num_rows) {
			while ($data = $result->fetch_object(get_class($this))) {
				$ret[$data->id] = $data;
			}
		}
		return $ret;
	}

	public function update() {
		if (!$this->id) throw new Exception("id is unknown when calling update()");
		$col = $this->columns();
		$set_strs = array();
		foreach ($col as $each_col) {
			$set_strs [] = "`{$each_col}` = " . $this->encode($each_col);
		}
		$set_str = join(', ', $set_strs);
		$sql = "UPDATE `" . static::$table_name . "` SET $set_str WHERE `id` = {$this->id}";
		$this->client()->query($sql);
		return $this;
	}

	public function insert() {
		$col = $this->columns();
		$col_str = join(',', array_map(function ($v) {
			return "`$v`";
		}, $col));
		$values_str = join(',', array_map(
			array($this, 'encode'), $col
		));
		$sql = "INSERT INTO `" . static::$table_name . "` ({$col_str}) VALUES ({$values_str})";
		$this->client()->query($sql);
		if ($this->client()->errno) {
			throw new Exception("Insert Errpr: " . $this->client()->error);
		} else {
			return $this->id = $this->client()->insert_id;
		}
	}

	protected function columns() {
		$vars = get_class_vars(get_class($this));
		unset($vars['client'], $vars['table_name']);
		return array_keys($vars);
	}

	protected function encode($v) {
		return "'" . $this->client()->real_escape_string($this->{$v}) . "'";
	}

	public static function loader() {
		return new static();
	}
}