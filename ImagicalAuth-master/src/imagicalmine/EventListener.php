<?php
namespace imagicalmine;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
class EventListener extends PluginBase implements Listener{
  /** @var ImagicalAuth */
  private $plugin;
  /**
   * @param ImagicalAuth $plugin
   */
    public function __construct(ImagicalAuth $plugin){
      $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event){

    }
}