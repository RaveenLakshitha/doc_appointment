<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\InventoryItem;

class LowStockAlert extends Notification
{
    use Queueable;

    protected InventoryItem $item;
    protected string $alertType; // 'low_stock' | 'out_of_stock'

    public function __construct(InventoryItem $item, string $alertType = 'low_stock')
    {
        $this->item = $item;
        $this->alertType = $alertType;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isOutOfStock = $this->alertType === 'out_of_stock';

        return [
            'title_key'      => $isOutOfStock ? 'file.notification_title_out_of_stock' : 'file.notification_title_low_stock',
            'message_key'    => $isOutOfStock ? 'file.notification_out_of_stock' : 'file.notification_low_stock',
            'message_params' => [
                'item'    => $this->item->name,
                'stock'   => $this->item->current_stock,
                'minimum' => $this->item->minimum_stock_level,
            ],
            'item_id'        => $this->item->id,
            'item_name'      => $this->item->name,
            'current_stock'  => $this->item->current_stock,
            'minimum_stock'  => $this->item->minimum_stock_level,
            'alert_type'     => $this->alertType,
            'icon'           => $isOutOfStock ? 'x-circle' : 'exclamation-triangle',
            'color'          => $isOutOfStock ? 'red' : 'yellow',
            'link'           => route('inventory.show', $this->item->id),
        ];
    }
}
