<?php
namespace RecordLog;

use think\facade\Env;
use Psr\Log\LoggerInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\WebProcessor;
use Monolog\Logger;
 
class RecordLog implements LoggerInterface
{
    protected $loggers;
 
    /**
     * 是否允许日志写入
     * @var bool
     */
    protected $allowWrite = true;
 	
 	/**
     * 日志设置
     */
    protected $config = [];
 
    // 实例化并传入参数
    public function __construct($config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
        if (!empty($config['close'])) {
            $this->allowWrite = false;
        }

        //获取runtime目录
        $runtimePath = Env::get('runtime_path');
 
        if (empty($this->config['path'])) {
            $this->config['path'] = $runtimePath . 'recordlogs' . DIRECTORY_SEPARATOR;
        } elseif (substr($this->config['path'], -1) != DIRECTORY_SEPARATOR) {
            $this->config['path'] .= DIRECTORY_SEPARATOR;
        }
    }
 
    /**
     * 创建日志
     * @return mixed
     */
    private function createLogger($name)
    {
        if (empty($this->loggers[$name])) {
            // 根据业务域名与方法名进行日志名称的确定
            $channel	= $this->config['channel'];
            // 日志文件目录
            $path		= $this->config['path'];
            // 日志保存时间
            $maxFiles 	= $this->config['max_files'];
            // 日志等级
            $level 		= Logger::toMonologLevel($name);
            // 权限
            $filePermission =  $this->config['file_permission'];
            // 创建日志
            $logger    = new Logger($channel);
            // 日志文件相关操作
            $handler   = new RotatingFileHandler("{$path}{$name}.log", $maxFiles, $level, true, $filePermission);
            // 日志格式
            $formatter = new LineFormatter("%datetime%###%channel%###%level_name%###%message%###content:%context%###extra:%extra%\n", "Y-m-d H:i:s", false, true);
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);
            // extra 信息
            $logger->pushProcessor(new UidProcessor);       //extra Uid
            $logger->pushProcessor(new ProcessIdProcessor); //extra Pid
            $logger->pushProcessor(new WebProcessor);       //extra Web
            $logger->pushProcessor(function ($record) { 
                $record['extra']['dummy'] = 'Hello world!'; 
             
                return $record; 
            });
 
            $this->loggers[$name] = $logger;
        }
        return $this->loggers[$name];
    }
 
    /**
     * 记录日志信息
     * @access public
     * @param  mixed  $message 		日志信息
     * @param  string $level      	日志级别
     * @param  array  $context 		替换内容
     * @return $this
     */
    public function record($message, $level = 'info', array $context = [])
    {
        if (!$this->allowWrite) {
            return;
        }
        $logger = $this->createLogger($level);
        $level = Logger::toMonologLevel($level);
        if (!is_int($level)) $level = Logger::INFO;
        
        return $logger->addRecord($level, $message, $context);
    }
 
    /**
     * 记录日志信息
     * @access public
     * @param  string $level     日志级别
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        if ($level == 'sql')
            $level = 'debug';
        $this->record($message, $level, $context);
    }
 
    /**
     * 记录emergency信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
 
    /**
     * 记录警报信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
 
    /**
     * 记录紧急情况
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
 
    /**
     * 记录错误信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
 
    /**
     * 记录warning信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
 
    /**
     * 记录notice信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
 
    /**
     * 记录一般信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
 
    /**
     * 记录调试信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
 
    /**
     * 记录sql信息
     * @access public
     * @param  mixed  $message   日志信息
     * @param  array  $context   替换内容
     * @return void
     */
    public function sql($message, array $context = [])
    {
        $this->log(__FUNCTION__, $message, $context);
    }
 
}
