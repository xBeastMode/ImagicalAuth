<?php
namespace ImagicalAuth;
use imagicalmine\tasks\AuthenticationTask;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\event\Listener;
class Main extends PluginBase implements Listener {

     const PREFIX = TF::RED . "[" . TF::LIGHT_PURPLE . "ImagicalMine" . TF::RED . "] ";

     public $toLogin = array();
     public $toRegister = array();

    /** @var Main */
    private static $instance = null;
    /** @var Player[] */
    private $unAuthed = [];
    /** @var $sql */
    private $sql;
    /** @var Config */
    private $cfg;
    /** @var Config */
    private $messages;

    public function onLoad(){
      $this->getLogger()->info(ImagicalAuth::PREFIX . "Loaded!");
      self::$instance = $this;
    }
	
	public function onEnable()
	{
        $this->getServer()->getPluginManager()->registerEvents($this ,$this);
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder() . "/account");
		$this->messages = new Config($this->getDataFolder() . "/messages.yml", Config::YAML);
                if($this->messages->get("message.authdelaykickreason") == null)
                {
                        $this->messages->get("message.authdelaykickreason", "§eYou took too much time trying to log ");
                }
                if($this->messages->get("message.join") == null)
                {
                        $this->messages->set("message.join", "§eWelcome to ImagicalAuth Server ");
                }
		if($this->messages->get("main.prefix") == null)
		{
			$this->messages->set("main.prefix", "§7[§aImagicalAuth§7]§f ");
		}
		if($this->messages->get("message.login") == null)
		{
			$this->messages->set("message.login", "§eYou have to login. Use /login <password>.");
		}
		if($this->messages->get("message.loginSuccessfull") == null)
		{
			$this->messages->set("message.loginSuccessfull", "§aYou have successfully logged in!");
		}
		if($this->messages->get("message.loginFail") == null)
		{
			$this->messages->set("message.loginFail", "§cWrong password!");
		}
		if($this->messages->get("message.loginNotRegistered") == null)
		{
			$this->messages->set("message.loginNotRegistered", "§cThis account is not registered!");
		}
		if($this->messages->get("message.register") == null)
		{
			$this->messages->set("message.register", "§eYou have to register. Use /register <password>.");
		}
		if($this->messages->get("message.registerSuccessfull") == null)
		{
			$this->messages->set("message.registerSuccessfull", "§aYou have successfully registered your account!");
		}
		if($this->messages->get("message.registerFail") == null)
		{
			$this->messages->set("message.registerFail", "§cThis account is already registered!");
		}
		if($this->messages->get("message.registerBroadcast") == null)
		{
			$this->messages->set("message.registerBroadcast", "§ejoined for the first time!");
		}
		if($this->messages->get("message.authentificationFail") == null)
		{
			$this->messages->set("message.authentificationFail", "§cCould not authentificate account!");
		}
		$this->messages->save();
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
