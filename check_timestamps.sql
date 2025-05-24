-- Run this query to see the actual reservation timestamps
SELECT id, reserved_at, NOW(), 
       TIMESTAMPDIFF(SECOND, reserved_at, NOW()) as seconds_elapsed
FROM reservations
WHERE parked_at IS NULL;