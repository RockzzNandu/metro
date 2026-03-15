USE metro_db;

-- 1. Drop constraints on bookings
ALTER TABLE bookings DROP FOREIGN KEY bookings_ibfk_1;
ALTER TABLE bookings ADD CONSTRAINT bookings_user_id_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- 2. Drop constraints on login_activity
ALTER TABLE login_activity DROP FOREIGN KEY login_activity_ibfk_1;
ALTER TABLE login_activity ADD CONSTRAINT login_activity_user_id_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- 3. Drop constraints on tickets (referencing bookings)
ALTER TABLE tickets DROP FOREIGN KEY tickets_ibfk_1;
ALTER TABLE tickets ADD CONSTRAINT tickets_booking_id_fk FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE;

-- 4. Drop constraints on payments (referencing bookings)
ALTER TABLE payments DROP FOREIGN KEY payments_ibfk_1;
ALTER TABLE payments ADD CONSTRAINT payments_booking_id_fk FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE;

-- 5. Drop constraints on refunds (referencing bookings)
ALTER TABLE refunds DROP FOREIGN KEY refunds_ibfk_1;
ALTER TABLE refunds ADD CONSTRAINT refunds_booking_id_fk FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE;
