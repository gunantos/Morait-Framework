<?php
namespace morait {
	class user_core {
		private static $_error = '';
		private static $_status = false;
		private static $_login = true;
		private static $_username ='';
		private static $_is_user ='';
		private static $_email ='';
		private static $_nama = '';
		private static $_id_type = '';
		private static $_page = [];
		private static $_data ='';
		private static $_active = '';
		private static $_block = '';
		private static $__menu = '';
		private static $_id_user=0;
		private static $_kode_desa='';
		private static $_kode_kecamatan ='';
		private static $_nama_desa='';
		private static $_nama_kecamatan='';

		function __construct($id='')
		{
			if(!empty($id))
			{
				user_core::initialize($id);
			}

		}
		public function login($username, $password, $type='html')
		{
			if(empty($username) || empty($password))
			{
				user_core::$_status = false;
				user_core::$_error = 'Username dan password tidak boleh kosong';
				return user_core::$_status;
				exit;
			}
			db_core::where(array('username'=>$username, 'password'=>md5($password)));
			$get = db_core::get('tbl_user');
			if(! $get)
			{
				user_core::$_status = false;
				user_core::$_error = 'Username dan password salah';
				return user_core::$_status;
				exit;
			}
			$row = db_core::row();

			user_core::$_status = true;
			user_core::$_login = true;
			user_core::$_username = $row->username_user;
			user_core::$_id_user = $row->id_user;
			user_core::$_email = $row->email_user;
			user_core::$_nama = $row->nama_user;
			user_core::$_id_type = $row->id_user_access;
			return user_core::$_status;
		}

		public static function status()
		{
			return user_core::$_status;
		}

		public static function error()
		{
			return user_core::$_error;
		}

		public static function id_user($id_user='')
		{
			if(! empty($id_user)) user_core::$_id_user = $id_user;
			return user_core::$_id_user;
		}

		public static function kode_desa($kode_desa='')
		{
			if(! empty($kode_desa)) user_core::$_kode_desa = $kode_desa;
			return user_core::$_kode_desa;
		}

		public static function kode_kecamatan($kode_kecamatan='')
		{
			if(! empty($kode_kecamatan)) user_core::$_kode_kecamatan = $kode_kecamatan;
			return user_core::$_kode_kecamatan;
		}

		public static function nama_desa($nama_desa='')
		{
			if(! empty($nama_desa)) user_core::$_nama_desa = $nama_desa;
			return user_core::$_nama_desa;
		}

		public static function nama_kecamatan($nama_kecamatan='')
		{
			if(! empty($nama_kecamatan)) user_core::$_nama_kecamatan = $nama_kecamatan;
			return user_core::$_nama_kecamatan;
		}

		public static function username($username='')
		{
			if(! empty($username)) user_core::$_username = $username;
			return user_core::$_username;
		}

		public static function email($_email='')
		{
			if(! empty($_email)) user_core::$_email = $_email;
			return user_core::$_email;
		}

		public static function active($active='')
		{
			if(! empty($active)) user_core::$_active;
			if(strtolower(str_replace(" ", "", user_core::$_active)) == 'y')
			{
				return true;
			}else{
				return false;
			}
		}

		public static function block($block='')
		{
			if(! empty($block)) user_core::$_block;
			if(strtolower(str_replace(" ", "", user_core::$_block)) == 'y')
			{
				return true;
			}else{
				return false;
			}
		}

		public static function nama($_nama='')
		{
			if(! empty($_nama)) user_core::$_nama = $_nama;
			return user_core::$_nama;
		}

		public static function id_type($_id_type='')
		{
			if(! empty($_id_type)) user_core::$_id_type = $_id_type;
			return user_core::$_id_type;
		}

		private static function _menu($id_menu=[], $admin=false)
		{
			$get = db_core::get('tbl_page');
			$_menu = '';
			if($get)
			{
				if($get->num_rows > 0)
				{
					$_old_page = '';

					foreach (db_core::result() as $key) {
						if($admin == true)
						{
							$return = true;
						}else{
							if(in_array($key->id_page, $id_menu))
							{
								$return = true;
							}else{
								$return = false;
							}
						}

						if($return)
						{
							if(strpos($key->text_page, '_', 0))
							{
								$_ex = explode('_', $key->text_page);
								if(strtolower(str_replace(" ", "", $_ex[0])) == $_old_page)
								{
									$_old_page= $_old_page= strtolower(str_replace(" ", "", $_ex[0]));
									$_menu .= ' <li><a class="menu-item menu_user" href="javascript:;" data_url="'. $key->url_page .'"><span class="menu-title" data-i18n="">'. $_ex[1] .'</span></a></li>';
								}else{
									if(! empty($_old_page))
									{
										$_menu .= '</ul><hr>';
									}
									$_ex = explode('_', $key->text_page);
									$_menu .= '<li class="nav-item"><a href="#"><i class="ft-film"></i><span class="menu-title" data-i18n="nav.dash.main">'. $_ex[0] .'</span></a><ul class="menu-content">';
								}
								$_old_page = strtolower(str_replace(" ", "",  $_ex[0]));
							}else{
								if(! empty($_old_page))
								{
									if(!empty($_menu)) $_menu .= '</ul><hr>';
								}
								if(empty($key->url_page))
								{
									$_menu .= '<li class="nav-item"><a href="#"><i class="ft-film"></i><span class="menu-title" data-i18n="nav.dash.main">'. $key->text_page .'</span></a><ul class="menu-content">';
									$_old_page = strtolower(str_replace(" ", "", $key->text_page));
								}else{
									$_menu .= '<li class=" nav-item"><a href="javascript:;" class="menu_user" data_url="'. $key->url_page .'"><i class="ft-bar-chart"></i><span class="menu-title" data-i18n="">'. $key->text_page .'</span></a></li><hr>';
										$_old_page = '';
								}
									
							}
						}
					}
				}
			}
			user_core::$__menu = $_menu;
			return user_core::$__menu;
		}

		public static function menu_user()
		{
			db_core::where('id_user_access', user_core::$_id_type);
			$get = db_core::get('tbl_user_access');
			if($get)
			{
				if($get->num_rows > 0)
				{
					$row = db_core::row();
					$page_user_access = $row->page_user_acces;
					if($page_user_access == '*')
					{
						return user_core::_menu([], true);
					}else{
						$explode = explode(",", $page_user_access);
						return user_core::_menu($explode);
					}
				}
			}
		}

		public static function initialize($id_user='')
		{
			if(empty($id_user))
			{
				user_core::$_status = false;
				user_core::$_error = 'Tentukan user';
				return user_core::$_status;
				exit;
			}
			db_core::select('tbl_user.*, tbl_desa.desa as nama_desa, tbl_kecamatan.Kecamatan as nama_kecamatan');
			db_core::where('id_user', $id_user);
			db_core::join('LEFT', 'tbl_desa', 'tbl_desa.kode_desa', 'tbl_user.kode_desa');
			db_core::join('LEFT', 'tbl_kecamatan', 'tbl_kecamatan.kode_kec', 'tbl_user.kode_kecamatan');
			$get = db_core::get('tbl_user');
			if(! $get)
			{
				user_core::$_status = false;
				user_core::$_error = 'User tidak dikenali';
				return user_core::$_status;
				exit;
			}
			$row = db_core::row();

			user_core::$_status = true;
			user_core::$_login = true;
			user_core::$_active = $row->active;
			user_core::$_block = $row->block;
			user_core::$_username = $row->username_user;
			user_core::$_id_user = $row->id_user;
			user_core::$_email = $row->email_user;
			user_core::$_nama = $row->name_user;
			user_core::$_id_type = $row->id_user_access;
			user_core::$_kode_desa = $row->kode_desa;
			user_core::$_kode_kecamatan= $row->kode_kecamatan;
			user_core::$_nama_desa = $row->nama_desa;
			user_core::$_nama_kecamatan = $row->nama_kecamatan;
			return user_core::$_status;
		}
	}
}