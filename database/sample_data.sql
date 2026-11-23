-- ===================================================
-- RailConnect: Railway Reservation Management System
-- Sample Data for Demonstration
-- ===================================================

-- Clear existing data
DELETE FROM Ticket;
DELETE FROM Station;
DELETE FROM Train;
DELETE FROM Passenger;

-- 1. Insert Trains
INSERT INTO Train (train_no, train_name) VALUES
(12001, 'Shatabdi Express'),
(12951, 'Rajdhani Express'),
(12628, 'Karnataka Express'),
(12123, 'Deccan Queen'),
(12780, 'Goa Express'),
(12051, 'Jan Shatabdi Express');

-- 2. Insert Stations
INSERT INTO Station (station_no, station_name, location) VALUES
(101, 'Chhatrapati Shivaji Terminus', 'Mumbai'),
(102, 'New Delhi Railway Station', 'New Delhi'),
(103, 'Pune Junction', 'Pune'),
(104, 'KSR Bengaluru Station', 'Bengaluru'),
(105, 'Madgaon Junction', 'Goa'),
(106, 'Hazrat Nizamuddin', 'Delhi');

-- 3. Insert Passengers
INSERT INTO Passenger (passenger_id, passenger_name, gender, age, phone) VALUES
(1, 'Rahul Sharma', 'Male', 28, '9876543210'),
(2, 'Priya Patel', 'Female', 24, '9823456781'),
(3, 'Amit Verma', 'Male', 35, '9811223344'),
(4, 'Sneha Kulkarni', 'Female', 22, '9765432109'),
(5, 'Rohan Deshmukh', 'Male', 42, '9988776655'),
(6, 'Ananya Iyer', 'Female', 30, '9123456780'),
(7, 'Vikram Singh', 'Male', 55, '9456123789'),
(8, 'Neha Gupta', 'Female', 27, '9345678901');

-- 4. Insert Tickets
-- Note: Average fare here is (2850 + 2850 + 1820 + 450 + 450 + 2150 + 1650 + 1820 + 450 + 2150) / 10 = 1664.00
INSERT INTO Ticket (ticket_id, passenger_id, train_no, source_station, destination_station, journey_date, fare) VALUES
(1, 1, 12951, 101, 102, '2026-12-01', 2850.00),
(2, 2, 12951, 101, 102, '2026-12-01', 2850.00),
(3, 3, 12001, 102, 101, '2026-12-05', 1820.00),
(4, 4, 12123, 101, 103, '2026-12-02', 450.00),
(5, 5, 12123, 103, 101, '2026-12-03', 450.00),
(6, 6, 12628, 104, 102, '2026-12-08', 2150.00),
(7, 7, 12780, 105, 106, '2026-12-10', 1650.00),
(8, 8, 12001, 102, 101, '2026-12-12', 1820.00),
(9, 1, 12123, 101, 103, '2026-12-15', 450.00),
(10, 3, 12628, 104, 102, '2026-12-20', 2150.00);
