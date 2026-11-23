-- ===================================================
-- RailConnect: Railway Reservation Management System
-- Compulsory SQL Queries (Academic Project Requirements)
-- ===================================================

-- Query A: Display passenger names along with train names using JOIN
SELECT Passenger.passenger_name, Train.train_name
FROM Ticket
JOIN Passenger ON Ticket.passenger_id=Passenger.passenger_id
JOIN Train ON Ticket.train_no=Train.train_no;

-- Query B: Display passengers who booked tickets having fare greater than average ticket fare
SELECT DISTINCT Passenger.passenger_name, Ticket.fare
FROM Passenger
JOIN Ticket ON Passenger.passenger_id=Ticket.passenger_id
WHERE Ticket.fare>(SELECT AVG(fare) FROM Ticket);

-- Query C: Display trains whose names LIKE '%Express%'
SELECT *
FROM Train
WHERE train_name LIKE '%Express%';

-- Query D: Display number of passengers travelling in each train using GROUP BY and COUNT()
SELECT Train.train_name, COUNT(DISTINCT Ticket.passenger_id) AS passenger_count
FROM Train
LEFT JOIN Ticket ON Train.train_no=Ticket.train_no
GROUP BY Train.train_no, Train.train_name;

-- Query E: Display trains having more than 100 passengers using HAVING
SELECT Train.train_name, COUNT(DISTINCT Ticket.passenger_id) AS passenger_count
FROM Train
JOIN Ticket ON Train.train_no=Ticket.train_no
GROUP BY Train.train_no, Train.train_name
HAVING COUNT(DISTINCT Ticket.passenger_id)>100;
