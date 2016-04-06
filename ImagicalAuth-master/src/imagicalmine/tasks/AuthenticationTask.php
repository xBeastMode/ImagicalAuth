<?php
namespace imagicalmine\tasks;
use imagicalmine\ImagicalAuth;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
class AuthenticationTask extends PluginTask{
    /** @var ImagicalAuth */
    private $plugin;
    /** @var Player */
    private $player;
    /**
     * @param ImagicalAuth $plugin
     * @param Player $p
     */
    public function __construct(ImagicalAuth $plugin, Player $p){
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
        $this->player->kick($this->plugin->getMessage("authentication_delay_kick_reason"));
    }
}