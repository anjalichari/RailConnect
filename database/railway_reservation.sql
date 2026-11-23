-- ===================================================
-- RailConnect: Railway Reservation Management System
-- Database Schema for existing database: railway_reservation
-- Note: Do not include CREATE DATABASE statement.
-- ===================================================

-- Drop tables in reverse order of foreign key dependencies
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS Station;
DROP TABLE IF EXISTS Train;
DROP TABLE IF EXISTS Passenger;

-- Table 1: Passenger
CREATE TABLE Passenger (
    passenger_id INT PRIMARY KEY AUTO_INCREMENT,
    passenger_name VARCHAR(100) NOT NULL,
    gender VARCHAR(10),
    age INT,
    phone VARCHAR(15)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 2: Train
CREATE TABLE Train (
    train_no INT PRIMARY KEY,
    train_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 3: Station
CREATE TABLE Station (
    station_no INT PRIMARY KEY,
    station_name VARCHAR(100) NOT NULL,
    location VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 4: Ticket
CREATE TABLE Ticket (
    ticket_id INT PRIMARY KEY AUTO_INCREMENT,
    passenger_id INT NOT NULL,
    train_no INT NOT NULL,
    source_station INT NOT NULL,
    destination_station INT NOT NULL,
    journey_date DATE NOT NULL,
    fare DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (passenger_id) REFERENCES Passenger(passenger_id) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (train_no) REFERENCES Train(train_no) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (source_station) REFERENCES Station(station_no) ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (destination_station) REFERENCES Station(station_no) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
