<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InventoryItem;
use App\Models\NotificationSetting;
use App\Notifications\LowStockAlert;

class CheckLowStockInventory extends Command
{
    protected $signature   = 'inventory:check-low-stock';
    protected $description = 'Check inventory for low-stock or out-of-stock items and send dashboard notifications.';

    public function handle(): void
    {
        // ── 1. Resolve recipients for each event ────────────────────────────
        $lowStockRecipients    = NotificationSetting::getRecipients('inventory_low_stock');
        $outOfStockRecipients  = NotificationSetting::getRecipients('inventory_out_of_stock');

        if ($lowStockRecipients->isEmpty() && $outOfStockRecipients->isEmpty()) {
            $this->info('No notification recipients configured for inventory alerts. Skipping.');
            return;
        }

        // ── 2. Out-of-stock items (standard products only, stock = 0) ───────
        if ($outOfStockRecipients->isNotEmpty()) {
            $outOfStockItems = InventoryItem::outOfStock()->get();

            foreach ($outOfStockItems as $item) {
                // Only notify if we haven't sent an unread out-of-stock alert already
                foreach ($outOfStockRecipients as $user) {
                    $alreadyNotified = $user->unreadNotifications()
                        ->where('type', LowStockAlert::class)
                        ->where('data->item_id', $item->id)
                        ->where('data->alert_type', 'out_of_stock')
                        ->exists();

                    if (! $alreadyNotified) {
                        $user->notify(new LowStockAlert($item, 'out_of_stock'));
                    }
                }
            }

            $this->info("Processed {$outOfStockItems->count()} out-of-stock items.");
        }

        // ── 3. Low-stock items (standard products, stock <= minimum but > 0) ─
        if ($lowStockRecipients->isNotEmpty()) {
            $lowStockItems = InventoryItem::lowStock()->get();

            foreach ($lowStockItems as $item) {
                foreach ($lowStockRecipients as $user) {
                    $alreadyNotified = $user->unreadNotifications()
                        ->where('type', LowStockAlert::class)
                        ->where('data->item_id', $item->id)
                        ->where('data->alert_type', 'low_stock')
                        ->exists();

                    if (! $alreadyNotified) {
                        $user->notify(new LowStockAlert($item, 'low_stock'));
                    }
                }
            }

            $this->info("Processed {$lowStockItems->count()} low-stock items.");
        }

        $this->info('Inventory low-stock check complete.');
    }
}
