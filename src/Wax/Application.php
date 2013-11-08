<?php
namespace Wax;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class Application extends \ArrayObject implements HttpKernelInterface {


  public function __construct($config) {
    if($confg) $this->setConfig($config);
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
    \WaxModel::load_adapter([
      "dbtype"=>    $this["config"]["db.driver"],
      "host"=>      $this["config"]["db.host"],
      "database"=>  $this["config"]["db.dbname"],
      "username"=>  $this["config"]["db.user"],
      "password"=>  $this["config"]["db.password"]
    ]);
    
  }

}
