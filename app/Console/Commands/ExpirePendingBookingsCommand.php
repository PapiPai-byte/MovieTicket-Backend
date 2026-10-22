<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSeat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePendingBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire pending bookings older than 10 minutes and release reserved seats';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredBookings = Booking::where('status', BookingStatus::PENDING->value)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No pending bookings to expire.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking) {
                // Free the reserved seats
                BookingSeat::where('booking_id', $booking->id)->delete();

                // Update status to cancelled
                $booking->status = BookingStatus::CANCELLED;
                $booking->save();
            });

            $count++;
        }

        $this->info("Successfully expired {$count} pending booking(s) and released seats.");

        return Command::SUCCESS;
    }
}

