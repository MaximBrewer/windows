<?php

namespace App;

class TelegramBot
{
    private $sendTo = ['188766748', '218500660'];

    private $botKey = '649476059:AAEo-UsAJtTZsSS9t_WeTbUsBTbRPPeS2mk';

    public function sendMessage($message)
    {
        foreach($this->sendTo as $chatId) {
            //$message = urlencode($message);
            $url = "https://api.telegram.org/bot{$this->botKey}/sendMessage?chat_id={$chatId}&text={$message}";
            $ch = curl_init();
            $optArray = [
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
            ];
            curl_setopt_array($ch, $optArray);
            $result = curl_exec($ch);
            curl_close($ch);
        }
    }

    private function getUpdates() {
    $url = "https://api.telegram.org/bot{$this->botKey}/getUpdates";
    $ch = curl_init();
    $optArray = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
    ];
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
    }

    public function updateUsers()
    {
    $this->loadUsers();

    $updates = json_decode($this->getUpdates());

    $updates = $updates->result;

    foreach ($updates as $update)
    {
        if (!in_array($update->message->from->id, $this->sendTo))
        {
        $this->sendTo[] = $update->message->from->id;
        }
    }
    $this->saveUsers();
    }

    private function loadUsers()
    {
    if (file_exists('send_to.txt')) {
        $fileContents = file_get_contents('send_to.txt');
        $this->sendTo = json_decode($fileContents, true);
    }
    }

    private function saveUsers()
    {
    $encodedString = json_encode($this->sendTo);
    file_put_contents('send_to.txt', $encodedString);
    }
}
