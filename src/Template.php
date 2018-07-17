<?php

namespace Toolmao\Nearphp;

class Template{
	public static $templatePath;
    private $data;

    /**
     * 设置模板变量
     * @param $key string | array
     * @param $value
     */
    public function assign($key, $value=null) {
        if(is_array($key)) {
            $this->data = is_array($this->data)?array_merge($this->data, $key):$key;
        } elseif(is_string($key)) {
            $this->data[$key] = $value;
        }
    }

    /**
     * 渲染模板
     * @param $template
     * @return string
     */
    public function toHTML($template) {
        extract($this->data);
        ob_start();
        include (self::$templatePath . $template);
        $res = ob_get_contents();
        ob_end_clean();
        return $res;
    }
}