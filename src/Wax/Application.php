<?php
namespace Wax;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class Application extends \ArrayObject implements HttpKernelInterface {


  public function __construct($config) {
    if($config) $this->setConfig($config);
  }

    
  public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
    $this->configure();
    $this->route($request);
    \Autoloader::bootstrap();
    ob_start();
    $wax = new \WaxApplication(true);
    return new Response(ob_get_clean());
  }

  public function setConfig($source) {
    if(is_array($source)) $this["config"] = $source;
    else {
      if(is_readable($source)) $this["config"] = include($source);
    }
    \Config::set($this["config"]);
  }
  
  
  protected function route($request) {
    $_GET["route"] = ltrim($request->getBaseUrl().$request->getPathInfo(), "/");
  }
  
  protected function configure() {
    $this["config"] = $this->compat($this["config"]);
    \WaxModel::load_adapter($this->config["db"]);
    
  }

  /*** This is is bit horrible but a load of old Wax apps depend on non-namespaced values, so this method
  *    serves as a thin mapping layer to make the values available in old and new style.
  */
  protected function compat($config) {
    $config["db"]["dbtype"]      ?: $config["db.driver"];
    $config["db"]["host"]        ?: $config["db.host"];
    $config["db"]["database"]    ?: $config["db.dbname"];
    $config["db"]["username"]    ?: $config["db.user"];
    $config["db"]["password"]    ?: $config["db.password"];
    $config["db"]["socket"]      ?: $config["db.socket"];
    return $config;
  }

}
