<?php
namespace frame\runtime;
/**
 * 类加载器
 *
 * @author lonphy(dev@lonphy.com)
 * @version 1.0
 */
class CClassLoader
{
    /**
     * 注册本实例到autoloader
     *
     * @param Boolean $prepend Whether to prepend the autoloader or not
     */
    public static function register($prepend = false)
    {
        spl_autoload_register([self::class, 'autoload'], true, $prepend);
    }
    
    /**
     * 类的自动加载
     * @param  string $class 带命名空间的类名,例app\IndexController
     * @return void
     */
    public static function autoload($class)
    {
        $file = APP_PATH . '/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            include $file;
        } else if ($protoBufFile = self::findProtoBufFile($class)) {
            // protobuf 文件
            include $protoBufFile;
        }
    }

    /**
     * 加载protobuf文件
     * @param string $class
     * @return string|void
     * @author lixin
     */
    public static function findProtoBufFile($class)
    {
        $file = APP_PATH . '/app/protobuf/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            return $file;
        } else {
            return;
        }
    }
}