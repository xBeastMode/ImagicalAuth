<?php
namespace ImagicalAuth;
use imagicalmine\tasks\AuthenticationTask;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
class Main extends PluginBase{
  const PREFIX = TF::RED . "[" . TF::LIGHT_PURPLE . "ImagicalMine" . TF::RED . "] ";
  /** @var Main */
  private static $instance = null;
  /** @var Player[] */
  private $unAuthed = [];
  /** @var $sql */
  private $sql;
  /** @var Config */
  private $cfg;
  /** @var Config */
  private $msg;
  public function onLoad(){
    $this->getLogger()->info(ImagicalAuth::PREFIX . "Loaded!");
    self::$instance = $this;
  }
  public function onEnable(){
    @mkdir($this->dataPath());
    $this->cfg = new Config($this->dataPath() . "config.yml", Config::YAML, ["stay_logged_in" => true]);
    $this->msg = new Config($this->dataPath() . "message.yml", Config::YAML, ["Join" =>, "msg" => "To play either:", "register" => "- Register with /register", "login" => "- Login with /login", "popup" => "You are not authenticated!"]);
    $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    $this->getLogger()->info(ImagicalAuth::PREFIX . "Enabled!");
  }
  public function onDisable(){
    $this->getLogger()->info(ImagicalAuth::PREFIX . "Disabled!");
  }
  /**
   * Gets message from configuration
   *
   * @param $message
   * @return bool|mixed
   */
  public function getMessage($message){
    return $this->msg->get($message);
  }
  /**
   * Data path of ImagicalAuth
   *
   * @return string
   */
  public function dataPath(){
    return $this->getDataFolder();
  }
  /**
   * Returns configuration file
   *
   * @return null|Config
   */
  public function getConfiguration(){
    if($this->cfg instanceof Config){
      return $this->cfg;
    }
    return null;
  }
  /**
   * Returns instance of ImagicalMine
   *
   * @return ImagicalAuth
   */
  public static function getInstance(){
    return self::$instance;
  }
  /**
   * Initiate MySQL Database
   *
   * @param $host
   * @param $username
   * @param $password
   * @param $db_name
   * @param $port
   */
  public function initDatabase($host, $username, $password, $db_name, $port){
    $this->sql = new \mysqli($host, $username, $password, $db_name, $port);
  }
  /**
   * Return MySQL database
   *
   * @return \mysqli|null
   */
  public function getDatabase(){
    if ($this->sql instanceof \mysqli) {
      return $this->sql;
    }
    return null;
  }
  /**
   * Returns hashed format of password
   *
   * @param $password
   * @return string
   */
  public function hashPassword($password){
    return bin2hex(md5(hash("sha1", $password) ^ hash("sha256", $password)));
  }
  /**
   * Verifies every single character of password
   *
   * @param $hash
   * @param $password
   * @return bool
   */
  public function verifyPasswordHash($hash, $password){
    $password = $this->hashPassword($password);
    $p1 = explode("", $hash);
    $p2 = explode("", $password);
    $matches = [];
    foreach($p1 as $a){
      foreach($p2 as $b){
        if($a === $b and $b === $a){
          $matches[] = 1;
        }else{
          $matches[] = 0;
        }
      }
    }
    if(in_array(0, $matches, true)){
      return false;
    }
    return true;
  }
  /**
   * Un-authenticate player to attempt login/registration
   *
   * @param Player $p
   */
  public function unAuthenticate(Player $p){
    $this->unAuthed[spl_object_hash($p)] = $p;
    if($this->cfg->get("authentication_delay_kick")) {
      $handler = $this->getServer()->getScheduler()->scheduleDelayedTask(
          $t = new AuthenticationTask($this, $p), 20 * intval($this->cfg->get("authentication_delay_kick_seconds")));
      $t->setHandler($handler);
    }
  }
  /**
   * Removes player if they took
   * too much to authenticate or if they quit
   *
   * @param Player $p
   */
  public function deAuthenticate(Player $p){
    if(isset($this->unAuthed[spl_object_hash($p)])) {
      unset($this->unAuthed[spl_object_hash($p)]);
    }
  }
}
