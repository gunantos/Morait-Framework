<?php
namespace morait{

	class db_core{
		private static $db_host='';
		private static $db_name='';
		private static $db_user='';
		private static $db_pass='';
		public static $conn = '';
		private static $env = 'development';

		private static $select ='*';
		private static $where = '';
		private static $join = '';
		private static $like = '';
		private static $from = '';
		private static $order_by='';
		private static $group_by= '';

		private static $limit = '';
		
		private static $_set_insert_val = '';
		private static $_set_insert_col = '';
		private static $_set_update = '';
		private static $_set_insert = '';

		private static $query = '';
		private static $table = '';

		private static $get = '';
		private static $del = '';
		private static $update = '';
		private static $insert = '';

		private static $current_row=0;

		private static $_mysqli_result = '';

		function __construct($option=[])
		{
			db_core::initialize($option);
		}

		function __destruct()
		{
			db_core::__error();
		}

		private static function __error()
		{
			if(! db_core::$_mysqli_result)
			{
				if(db_core::$env === 'development')
				{
					echo '<div sytle="border:none; box-shadow:0 1px 15px rgba(62,57,107,.07); padding: 1.5rem!important">
							  '. mysqli_error(db_core::$conn) .'</div>'; exit();
				}else{
					db_core::w_log(mysqli_erro(db_core::$conn));
				}
			}
		}
		private static function wh_log($log_msg)
		{
		    $log_filename = BASEPATH."app/log/";
		    if (!file_exists($log_filename)) 
		    {
		        mkdir($log_filename, 0777, true);
		    }
		    $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
		    file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
		}

		public static function initialize($option=[])
		{
			db_core::$db_host = $option['host'];
			db_core::$db_user = $option['username'];
			db_core::$db_pass = $option['password'];
			db_core::$db_name = $option['database'];
			db_core::$env 	  = strtolower($option['enveropment']);

			db_core::$conn = mysqli_connect(db_core::$db_host, db_core::$db_user, db_core::$db_pass, db_core::$db_name);
 			if(db_core::$conn === false){
    			die("ERROR: Could not connect. " . mysqli_connect_error());
			}
		}

		public static function error()
		{
			return mysqli_error(db_core::$conn);
		}

		private static function clear($all=true)
		{
			db_core::$select ='*';
			db_core::$where = '';
			db_core::$join = '';
			db_core::$like = '';
			db_core::$from = '';
			db_core::$order_by='';
			
			db_core::$_set_insert_val = '';
			db_core::$_set_insert_col = '';
			db_core::$_set_update = '';
			db_core::$_set_insert = '';

			if($all== true)
			{
				db_core::$query = '';
				db_core::$table = '';

				db_core::$get = '';
				db_core::$del = '';
				db_core::$update = '';
				db_core::$insert = '';
			}
		}

		public static function limit($limit=10, $start=0)
		{
			if($start > 0)
			{
				return db_core::$limit = ' LIMIT '. $limit .' OFFSET '. $start;
			}else{
				return db_core::$limit = ' LIMIT '. $limit;
			}
		}

		public static function set($col, $val='', $escape=true)
		{
			if($val != '')
			{
				db_core::$_set_insert_col .= (db_core::$_set_insert_col != '' ? ',' : ''). $col;
				db_core::$_set_insert_val .= (db_core::$_set_insert_val != '' ? ',' : '').db_core::escape($val, $escape); 
				db_core::$_set_update .= (db_core::$_set_update != '' ? ',' : '').$col.'='. db_core::escape($val, $escape);
			}else{
				if(is_array($col))
				{
					foreach ($col as $key => $value) {
						db_core::$_set_insert_col .= (db_core::$_set_insert_col != '' ? ',' : ''). $key;
						db_core::$_set_insert_val .= (db_core::$_set_insert_val != '' ? ',' : '').db_core::escape($value, $escape); 
						db_core::$_set_update .= (db_core::$_set_update != '' ? ',' : '').$key.'='. db_core::escape($value, $escape);
					}
				}
			}
		}

		public static function query($query='')
		{
			if($query != '')
			{
				db_core::$query = $query;
			}
			return db_core::$query;
		}

		private static function _mysqli_query($fungsi, $query='')
		{
			if($query != '') 
			{
				db_core::query($query);
			}else{
				$_funsgi = strtolower($fungsi);
				switch ($_funsgi) {
					case 'insert':
						for($i=0; $i<sizeof(db_core::$_set_colom); $i++)
						{

						}
						$_query = 'INSERT INTO '. db_core::$table .' ('. db_core::$_set_insert_col .') VALUES ('. db_core::$_set_insert_val .')' ;
						break;
					case 'update':
						$_query = 'UPDATE '. db_core::$table .' SET '. db_core::$_set_update .' WHERE '. db_core::$where;
						break;
					case 'delete':
						$_query = 'DELETE '. db_core::$table .' WHERE '. db_core::$where;
						break;
					case 'select':
					default:
						$query = 'SELECT '. (empty(db_core::$select) ? '*' : db_core::$select) .' FROM '. db_core::$table .(db_core::$join != '' ? " ".db_core::$join : ''). (empty(db_core::$where) ? '' : ' WHERE '. db_core::$where) . (empty(db_core::$order_by) ? '' : ' ORDER BY '. db_core::$order_by)  . (empty(db_core::$group_by) ? '' : ' GROUP BY '. db_core::$group_by) . (empty(db_core::$limit) ? '' : db_core::$limit);
						break;
				}
				db_core::query($query); 
			}
			db_core::clear(false);
			db_core::$_mysqli_result = db_core::$conn->query(db_core::$query);
			db_core::__error();
			return db_core::$_mysqli_result;
		}

		private static function _row($object=true, $multi=true)
		{
			$group_arr = false;
			if(db_core::$_mysqli_result)
			{
				if($object==true && $multi=false)
				{
					$group_arr = mysqli_fetch_object(db_core::$_mysqli_result);
				}elseif($object==false && $multi==false){
					$group_arr = mysqli_fetch_array(db_core::$_mysqli_result,MYSQLI_ASSOC);
				}elseif($object==true && $multi == true)
				{
					while ($row = mysqli_fetch_object(db_core::$_mysqli_result)){
				        $group_arr[] =  $row;
				    	}
				}else{
					$group_arr = mysqli_fetch_all(db_core::$_mysqli_result, MYSQLI_ASSOC);
				}
			}
			db_core::$_mysqli_result->close();
			@mysqli_free_result(db_core::$_mysqli_result);
			db_core::clear();
			return $group_arr;
		}

		public static function row()
		{
			if(db_core::$_mysqli_result)
			{
				return mysqli_fetch_object(db_core::$_mysqli_result);
			}
			return false;
		}

		public static function row_array($type=MYSQLI_ASSOC)
		{
			if(db_core::$_mysqli_result)
			{
				return (array) mysqli_fetch_array(db_core::$_mysqli_result, $type);
			}
			return false;
		}

		public static function result()
		{
			if(db_core::$_mysqli_result)
			{
				while ($row = mysqli_fetch_object(db_core::$_mysqli_result)){
					        $group_arr[] =  $row;
				}
				return (object) $group_arr;
			}
			return false;
		}

		public static function result_array($type=MYSQLI_ASSOC)
		{
			if(db_core::$_mysqli_result)
			{
				return mysqli_fetch_all(db_core::$_mysqli_result, $type);
			}
			return false;
		}

		public static function num_rows()
		{
			if(db_core::$_mysqli_result)
			{
				return db_core::$_mysqli_result;
			}
			return 0;
		}
		public static function get($table='', $where='')
		{
			db_core::from($table);
			db_core::where($where);
			return db_core::_mysqli_query('select');
		}

		public static function update($table='', $set='', $where='', $escape=true)
		{
			db_core::from($table);
			db_core::where($where);
			db_core::set($set, '', $escape);
			$hasil = db_core::_mysqli_query('update');
			@mysqli_free_result(db_core::$_mysqli_result);
			db_core::clear();
			return $hasil;
		}

		public static function delete($table='', $where='')
		{
			db_core::from($table);
			db_core::where($where);
			$hasil = db_core::_mysqli_query('delete');
			@mysqli_free_result(db_core::$_mysqli_result);
			db_core::clear();
			return $hasil;
		}

		public static function insert($table='', $set='', $escape=true)
		{
			db_core::from($table);
			db_core::set($set, '', $escape);
			$hasil = db_core::_mysqli_query('insert');
			@mysqli_free_result(db_core::$_mysqli_result);
			db_core::clear();
			return $hasil;
		}

		public static function select($select='*')
		{
			if($select != '') db_core::$select = $select;
			return $select;
		} 

		private static function escape($data, $escape=true)
		{
			$_data = '""';
			if(! empty($data))
			{
				if($escape == true)
				{
					$_data = "'". mysqli_real_escape_string(db_core::$conn, $data) ."'";
				}else{
					$_data = $data;
				}
			}
			return $_data;
		}

		private static function _where($where='', $escape=true, $or=false)
		{
			$and = ' AND '; 
			if($or == true)
			{
				$and = ' OR ';
			}
			if($where != '')
			{
				foreach ($where as $key => $value) {
					if(db_core::$where != '')
					{	
						db_core::$where .= $and . $key .'='.db_core::escape($value, $escape);
					}else{
						db_core::$where .= $key .'='. db_core::escape($value, $escape);
					}
				}
			}
			return db_core::$where;
		}

		public static function where($where='', $val='', $escape=true)
		{
			if($where != '')
			{
				if(is_array($where))
				{
					$_where = (object) $where;
					db_core::_where($_where, $escape);
				}elseif(is_object($where))
				{	
					db_core::_where($_where, $escape);
				}else{
					db_core::_where((object) array($where=>$val), $escape);
				}
			}
			return db_core::$where;
		}

		public static function or_where($where='', $escape=false)
		{
			if($where != '')
			{
				if(is_array($where))
				{
					$_where = (object) $where;
					db_core::_where($_where, $escape, true);
				}elseif(is_object($where))
				{	
					db_core::_where($_where, $escape, true);
				}else{
					db_core::$where .= $where;
				}
			}
			
			return db_core::$where;
		}

		public static function from($table='')
		{
			if($table !=''){
				db_core::$table = $table;
			}
			return db_core::$table;
		}

		public static function group_by($group_by='')
		{
			if($group_by !=''){
				db_core::$group_by = $group_by;
			}
			return db_core::$group_by;
		}

		private static function _like($id='', $value='', $like='both', $or=false)
		{
			if($id != '')
			{
				switch (strtolower($like)) {
					case 'right':
						db_core::$like .= (db_core::$like != '' ? ($or == true ? ' OR ' : ' AND '): '').$id.' '. $value .'%';
						break;
					case 'left':
						db_core::$like .= (db_core::$like != '' ? ($or == true ? ' OR ' : ' AND '): '').$id.' %'. $value;
						break;

					case 'both':
					default:
						db_core::$like .= (db_core::$like != '' ? ($or == true ? ' OR ' : ' AND '): '').$id.' %'. $value .'%';
						break;
				}
			}
			return db_core::$like;
		}

		public static function like($id='', $value='', $like='')
		{
			return db_core::_like($id, $value, $like, false);
		}

		public static function or_like($id='', $value='', $like='')
		{
			return db_core::_like($id, $value, $like, true);
		}

		public static function order_by($order='', $by='')
		{
			if($order != '')
			{
				if($by != '')
				{
					db_core::$order_by = $order .' '. $by;
				}else{
					db_core::$order_by = $order;
				}
			}
			return db_core::$order_by;
		}

		public static function join($methode, $table='', $id='', $id2='')
		{
			if($methode != '')
			{
				if(is_array($methode))
				{
					if(array_key_exists('methode', $methode))
					{
						db_core::$join .= (db_core::$join != '' ? ' ': '').$methode['methode'].' JOIN '. $methode['table'] . ' ON '. $methode['where'];
					}else{
						if(sizeof($methode) >= 4)
						{
							db_core::$join .= (db_core::$join != '' ? ' ': '').$methode[0].' JOIN '. $methode[1] . ' ON '. $methode[2] .'='. $methode[3];
						}elseif(sizeof($methode) == 3){
							db_core::$join .= (db_core::$join != '' ? ' ': '').$methode[0].' JOIN '. $methode[1] . ' ON '. $methode[2];
						}
					}
				}else{
					if($table != '')
					{
						db_core::$join .= (db_core::$join != '' ? ' ' : ''). $methode.' JOIN '. $table .' ON '. ($id2 != '' ? $id.'='.$id2 : $id);
					}else{
						db_core::$join .= (db_core::$join != '' ? ' ': ''). $methode;
					}
				}
			}
		}

		public static function last_id()
		{
			if(db_core::$insert != '')
			{
				return mysqli_insert_id(db_core::$insert);
			}else{
				return false;
			}
		}
	}
}