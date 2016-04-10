<?php
namespace imagicalmine\tasks;
use ImagicalAuth\Main;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
class AuthenticationTask extends PluginTask{
    /** @var Main */
    private $plugin;
    /** @var Player */
    private $player;
    /**
     * @param Main $plugin
     * @param Player $p
     */
    public function __construct(Main $plugin, Player $p){
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->player = $p;
    }
    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun($currentTick){
        $this->player->kick($this->plugin->getMessage("message.authdelaykickreason"));
    }
}
