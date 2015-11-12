<span class="name"><?php echo $booking->getDisplayName()?></span>|
<span class="date"><?php echo $booking->getStartsAt()->format('d/m/Y') ?></span>|
<span class="time"><?php echo $booking->getStartsAt()->format('H:i') ?></span>
