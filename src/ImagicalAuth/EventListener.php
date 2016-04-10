<?php
namespace imagicalmine;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use imagicalmine\tasks\AuthenticationPopupTask;
class EventListener extends PluginBase implements Listener{
  /** @var Main */
  private $plugin;
  /**
   * @param Main $plugin
   */
    public function __construct(Main $plugin){
      $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event){
      $player = $sender->getName();
      $join = $this->plugin->getMessage("Join");
      $player->sendMessage("This server uses IA (Imagical Auth) - the universal authentication plugin for ImagicalMine");
      $player->sendMessage("{$join["main.prefix"]}\n{$join["message.register"]}\n{$join["message.login"]}");
      $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new AuthenticationPopupTask($this->plugin, $player), 20);
    }
}
