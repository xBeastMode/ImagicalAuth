<?php
namespace ImagicalAuth;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
class Main extends PluginBase
{
    const PREFIX = TF::RED . "[" . TF::LIGHT_PURPLE . "ImagicalAuth" . TF::RED . "] ";

    /** @var Main */
    private static $instance = null;
    /**
     * @var array
     */
    private $unAuthenticated = [];
    /**
     * @return string
     */
    public function dataPath()
    {
        return $this->getDataFolder();
    }
    /**
     * @return \pocketmine\Server
     */
    public function server()
    {
        return $this->getServer();
    }
    /**
     * @return \pocketmine\plugin\PluginManager
     */
    public function pluginManager()
    {
        return $this->server()->getPluginManager();
    }
    public function logger()
    {
        return $this->server()->getLogger();
    }

    public function onLoad()
    {
        $this->getLogger()->info(Main::PREFIX . "Loaded!");
        Main::$instance = $this;
    }

    public function onEnable()
    {
        @mkdir($this->dataPath());
        $this->cfg = new Config($this->dataPath() . "config.yml". Config::YAML, array());
        $this->server()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getLogger()->info(Main::PREFIX . "Enabled!");
    }

    public function onDisable()
    {
        $this->getLogger()->info(Main::PREFIX . "Disabled!");
    }

    /**
     * @return Main
     */
    public static function getInstance(){
        return self::$instance;
    }

    /**
     * @param $password
     * @return bool|string
     */
    public function hashPassword($password)
    {
        $hashedPassword = md5(sha1(md5(sha1($password))));
        if($hashedPassword)
        {
            return $hashedPassword;
        } else {
            return false;
        }
    }

    /**
     * @param $hash
     * @param $password
     * @return bool
     */
    public function verifyPasswordHash($hash, $password)
    {
        $hashedPassword = md5(sha1(md5(sha1($password))));
        if($hashedPassword === $hash)
        {
            return true;
        }else{
            return false;
        }
    }
    public function isRegistered($username)
    {
        // MySQL Stuff here...
    }
}
