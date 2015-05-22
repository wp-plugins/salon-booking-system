<?php echo $booking->getDisplayName()?>
 <?php echo $booking->getStartsAt()->format('d/m/Y h:i') ?>
 (<?php echo implode(', ',$booking->getServices()) ?>)

