<?php
namespace imagicalmine;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
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
      $msg = $msg->get("on_join_msg");
      $player->sendMessage("This server uses IA (Imagical Auth) - the universal authentication plugin for ImagicalMine");
      $player->sendMessage($msg);
    }
}
