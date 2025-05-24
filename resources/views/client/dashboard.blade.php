<!-- Add this button somewhere appropriate in your dashboard view -->
<div class="mt-4">
    <a href="{{ route('parking.clearReservations') }}" class="btn btn-outline btn-sm inline-flex items-center">
        <i class="fas fa-broom mr-2"></i> Clear All My Reservations
    </a>
    <p class="text-xs text-muted-foreground mt-1">Use this if you're unable to make new reservations due to phantom reservations.</p>
</div>