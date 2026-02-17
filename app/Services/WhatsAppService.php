<?php

namespace App\Services;

use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Netflie\WhatsAppCloudApi\Message\Template\Template;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\ButtonAction;
use Netflie\WhatsAppCloudApi\Message\ButtonReply\Button;
use Netflie\WhatsAppCloudApi\Message\InteractiveList\Row;
use Netflie\WhatsAppCloudApi\Message\InteractiveList\Section;
use Netflie\WhatsAppCloudApi\Message\InteractiveList\ActionList;
use Netflie\WhatsAppCloudApi\Message\InteractiveList\ListMessage;

class WhatsAppService
{
    protected $whatsapp;

    public function __construct()
    {
        $this->whatsapp = new WhatsAppCloudApi([
            'from_phone_number_id' => config('whatsapp.from_phone_number_id'),
            'access_token'         => config('whatsapp.access_token'),
            'api_version'          => config('whatsapp.api_version'),
        ]);
    }

    public function sendText(string $to, string $text)
    {
        return $this->whatsapp->sendText($to, $text);
    }

    public function sendButtons(string $to, string $text, array $buttons, string $header = null, string $footer = null)
    {
        $buttonObjects = collect($buttons)->map(function ($btn) {
            return new Button($btn['id'], $btn['title']);
        })->toArray();

        $action = new ButtonAction($buttonObjects);

        return $this->whatsapp->sendButton($to, $text, $action, $header, $footer);
    }

    // Example: Send list (for doctors/specializations – up to 10 rows)
    public function sendList(string $to, string $title, string $text, array $sectionsData)
    {
        $sections = [];
        foreach ($sectionsData as $sectionTitle => $items) {
            $rows = collect($items)->map(fn($item) => new Row($item['id'], $item['title'], $item['description'] ?? ''))->toArray();
            $sections[] = new Section($sectionTitle, $rows);
        }

        $action = new ActionList($title, $sections);

        $list = new ListMessage($text, $action);

        return $this->whatsapp->sendInteractive($to, $list);
    }

    // Add methods for templates, documents, etc. as needed
}