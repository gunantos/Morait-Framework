<?php
namespace morait {
	class app_core {
		public static function page($page)
		{
			if(! empty($page))
			{
				$page = strval(trim(strtolower($page)));
				if(strpos($page, '.php'))
				{
					$_page = FCPATH.str_replace('/', '\\', $page);
				}elseif(substr($page, 0, 5) == 'aksi_'){
					$folder = str_replace('aksi_', 'mod_', $page);
					$_page = FCPATH.'modul/'. $folder .'/'. $page .'.php';
				}else{
					if(!IS_AJAX) {
						if(strtolower(ENVIRONMENT) != 'development')
						{
							set_status_header(503); exit();
						}
					}
					if(strpos(strtolower($page), 'mod_', 0))
					{
						$_page = FCPATH.'modul/'. $page .'/'. $page2 .'.php';
					}else{
						$_page = FCPATH.'modul/mod_'. $page .'/'. str_replace('mod_', '', $page) .'.php';
					}
				}

				if(file_exists($_page))
				{
					include $_page; exit;
				}else{
					app_core::show_404('Halaman yang anda cari tidak ditemukan');
				}
			}
		}

		public static function show_404($message='')
		{
			if(! empty($message))
			{
				echo '<h1>Page not Found</h1><br><hr>';
				echo $message;
			}
			set_status_header(404);
			exit;

		}
		public static  function get($text)
		{
			return (isset($_GET[$text]) ? $_GET[$text] : '');
		}	

		public static function post($text)
		{
			return (isset($_POST[$text]) ? $_POST[$text] : '');
		}	

		public static  function get_post($text)
		{
			if(isset($_GET[$text]))
			{
				return $_GET[$text];
			}elseif(isset($_POST[$text]))
			{
				return $_POST[$text];
			}else{
				return '';
			}
		}
	}
}