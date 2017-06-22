<?php

namespace Matthww\connectedip;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class connectedip extends PluginBase implements Listener {

    protected $PlayerData;
    protected $ConnectedIP;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->getLogger()->notice("is enabled");
    }

    public function onDisable() {
        $this->getLogger()->notice("is disabled!");
    }

    public function onPacketReceived(DataPacketReceiveEvent $receiveEvent) {
        if ($receiveEvent->getPacket() instanceof LoginPacket) {
            $pk = $receiveEvent->getPacket();
            $this->PlayerData[$pk->username] = $pk->clientData;
        }
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if (strtolower($command->getName()) == "connectedip" or strtolower($command->getName()) == "cip") {
            if ($sender->hasPermission("connectedip.use")) {
                if (isset($args[0])) {
                    if ($this->getServer()->getPlayer($args[0])) {
                        $target = $this->getServer()->getPlayer($args[0]);
                        $cdata = $this->PlayerData[$target->getName()];
                        $sender->sendMessage("§a§l===§r§aPlayer Info§a§l===");
                        $sender->sendMessage("§bName: §c" . $target->getDisplayName());
                        $sender->sendMessage("§bPlayer IP: §c" . $target->getAddress());
                        $sender->sendMessage("§bConnected IP: §c" . $cdata["ServerAddress"]);
                        return true;
                    } else {
                        $sender->sendMessage("§c[Error] Player not found");
                    }
                } else {
                    if ($sender instanceof Player) {
                        $cdata = $this->PlayerData[$sender->getName()];
                        $sender->sendMessage("§a§l===§r§aPlayer Info§a§l===");
                        $sender->sendMessage("§bName: §c" . $sender->getDisplayName());
                        $sender->sendMessage("§bPlayer IP: §c" . $sender->getAddress());
                        $sender->sendMessage("§bConnected IP: §c" . $cdata["ServerAddress"]);
                        return true;
                    } else {
                        $sender->sendMessage("§c[Error] Please specify a player");
                    }
                }
            }
        }
        return true;
    }
}
