<?php

namespace Toolmao\Nearphp;

class Router{
	private static $maps = array();
	private static $patterns = array(
		':any' => '[^/]+',
		':num' => '[0-9]+'
	);
	private static $error_callback;
	
	public static function add($rul,$cb){
		self::$maps[$rul] = $cb; //覆盖相同的路由规则
	}
	
	public static function set404($callback) {
		self::$error_callback = $callback;
	}
	
	public static function send(){
		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); //获取浏览器端的URI
		$uri = preg_replace(['#\/+#','#\/$#'], ['/',''], $uri); //删除多余的斜线、尾部的斜线
		if($uri==='') $uri='/';
		$uriruls = array_keys(self::$maps);  //服务器端设置的URI规则
		$uriruls = str_replace(array_keys(self::$patterns),array_values(self::$patterns),$uriruls); //把路由规则中类似 :any 替换成 [^/]+
		$callbacks = array_values(self::$maps);
		
		$found_route = false;
		
		foreach($uriruls as $pos=>$regex){
			if (preg_match('#^' . $regex . '$#', $uri, $matched)) {
				$found_route = true;
				array_shift($matched);
				if (!is_object($callbacks[$pos])) {
					$segments = explode('@',$callbacks[$pos]);
					$controller = new $segments[0]();
					if (!method_exists($controller, $segments[1])) {
						echo "controller and action not found!";
					} else {
						call_user_func_array(array($controller, $segments[1]), $matched);
					}
				} else {
					call_user_func_array($callbacks[$pos], $matched);
				}
			}
		}
		
		if ($found_route === false){
			header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
			if (!self::$error_callback) {
				echo '404 By None.ren';
			} else {
				call_user_func(self::$error_callback);
			}
		}
	}
}