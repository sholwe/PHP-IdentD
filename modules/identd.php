<?php
  class @@CLASSNAME@@ {
    public $depend = array("RawEvent");
    public $name = "identd";

    public function receiveRawEvent($name, $data) {
      $connection = $data[0];
      $data = $data[1];

      if (preg_match("/^\\d+, ?\\d+$/", trim($data))) {
        $connection->send(trim($data)." : USERID : UNIX : ".$this->ident);
      }
      $connection->disconnect();
    }

    public function isInstantiated() {
      $ident = StorageHandling::loadFile($this, "ident.conf");
      if ($ident != false && is_string($ident) && strlen($ident) > 0) {
        $this->ident = $ident;
      }
      else {
        $this->ident = "ident";
        StorageHandling::saveFile($this, "ident.conf", "ident");
      }

      EventHandling::registerForEvent("rawEvent", $this, "receiveRawEvent");
      return true;
    }
  }
?>
