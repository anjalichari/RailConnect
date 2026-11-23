# RailConnect – Railway Reservation Management System

> **Tagline:** Your Journey, Our Management  
> **Course:** Database Management Systems (DBMS) Mini Project  
> **Class:** Third Year Computer Engineering  

---

## 1. Project Introduction

**RailConnect** is a clean, simple, and computerized Railway Reservation Management System built for the third-year Computer Engineering DBMS curriculum. It provides a web interface for railway administrators to manage core railway entities: **Passengers**, **Trains**, **Stations**, and **Ticket Reservations**.

The system replaces manual paper-based rail reservation systems, ensuring data accuracy, eliminating duplicate records, maintaining referential integrity across entities, and enabling instant execution of analytical SQL queries.

---

## 2. Project Objectives

- Implement an end-to-end relational database following the Relational Model and Boyce-Codd / 3rd Normal Form (3NF).
- Maintain data consistency and referential integrity using MySQL InnoDB foreign keys (`ON UPDATE CASCADE`, `ON DELETE RESTRICT`).
- Provide an intuitive web dashboard for complete Create, Read, Update, and Delete (CRUD) operations on all tables.
- Execute and display the five compulsory academic SQL queries (JOIN, Subquery with AVG, LIKE pattern matching, GROUP BY with COUNT, and HAVING clause).
- Provide a secure, student-friendly codebase using Core Procedural PHP and prepared statements (`mysqli`).

---

## 3. Technologies Used

- **Frontend:** HTML5, CSS3 (Custom styling with a Navy Blue, White, and Light Grey theme).
- **Backend:** Core Procedural PHP with `mysqli` extension and prepared statements.
- **Database:** MySQL (InnoDB engine) via phpMyAdmin.
- **Environment:** XAMPP (Apache HTTP Server & MySQL Database Server).

*Note: No heavy or unnecessary frameworks (e.g., Bootstrap, React, Laravel) are used. The project is 100% genuine student-level procedural code.*

---

## 4. Project Directory Structure

```text
RailConnect/
├── index.php                 # Home page with introduction & real-time counts
├── passengers.php            # CRUD operations for passengers
├── trains.php                # CRUD operations for trains
├── stations.php              # CRUD operations for stations
├── tickets.php               # CRUD operations for ticket bookings
├── reports.php               # Five compulsory SQL query demonstration reports
├── includes/
│   ├── db.php                # MySQL database connection settings
│   ├── header.php            # Navigation bar & standard page header
│   └── footer.php            # Consistent page footer
├── css/
│   └── style.css             # Navy-blue themed student stylesheet
├── database/
│   ├── railway_reservation.sql # Table schema creation script (InnoDB)
│   ├── sample_data.sql         # Realistic initial sample data
│   ├── required_queries.sql    # Standalone script containing all 5 queries
│   └── having_demo.sql         # Test data script (>100 passengers) for Query E
├── README.md                 # Complete project setup and execution manual
└── database_design.md        # ER diagram, relational schema & relationship mapping
```

---

## 5. How to Set Up and Run in XAMPP

### Step 1: Copy Project to XAMPP htdocs
Copy the entire `RailConnect` project folder into your XAMPP web root directory:
```text
C:\xampp\htdocs\RailConnect
```

### Step 2: Start XAMPP Services
1. Open the **XAMPP Control Panel**.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.
4. Both modules should now show a green status.

### Step 3: Create Database in phpMyAdmin
1. Open your web browser (Chrome, Edge, Firefox).
2. Go to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3. In the left sidebar, click on **New**.
4. Enter the database name: `railway_reservation`
5. Set collation to `utf8mb4_general_ci` (or default).
6. Click **Create**.

### Step 4: Import Database Tables & Sample Data
1. Select the newly created `railway_reservation` database from the left sidebar in phpMyAdmin.
2. Click on the **Import** tab in the top navigation bar.
3. Click **Choose File** and select:
   ```text
   C:\xampp\htdocs\RailConnect\database\railway_reservation.sql
   ```
4. Scroll to the bottom and click **Import** (or **Go**).
5. Once successful, repeat the import step for the sample data file:
   ```text
   C:\xampp\htdocs\RailConnect\database\sample_data.sql
   ```

### Step 5: Open the Application in Your Browser
Open your browser and navigate to:
```text
http://localhost/RailConnect/
```

---

## 6. How to Use Website Pages

### 1. Home Page (`index.php`)
- Displays the project title, tagline, and project overview.
- Shows dynamic summary cards with live counts of total Passengers, Trains, Stations, and Booked Tickets.
- Provides quick action buttons to access each section.

### 2. Passengers Page (`passengers.php`)
- **Add Passenger:** Fill in Full Name, Gender, Age, and Phone Number, then click **Add Passenger**.
- **Update Passenger:** Click the **Edit** button beside any passenger row. The form loads the existing details. Modify the values and click **Update Passenger**.
- **Delete Passenger:** Click the **Delete** button. A confirmation popup appears.  
  *Referential Integrity Feature:* If the passenger has booked tickets, the system prevents deletion and displays an error message informing the user to delete the associated tickets first.

### 3. Trains Page (`trains.php`)
- **Add Train:** Enter a unique Train Number (e.g., `12951`) and Train Name (e.g., `Rajdhani Express`), then click **Add Train**.
- **Update Train:** Click **Edit** to modify the train name. (The Train Number is the Primary Key and remains protected).
- **Delete Train:** Click **Delete**. If tickets exist for this train, deletion is blocked to maintain referential integrity.

### 4. Stations Page (`stations.php`)
- **Add Station:** Enter Station Number (e.g., `101`), Station Name (e.g., `Chhatrapati Shivaji Terminus`), and Location (e.g., `Mumbai`).
- **Update Station:** Click **Edit** to update station name or city/location.
- **Delete Station:** Click **Delete**. If the station is used as a source or destination in any ticket, deletion is blocked.

### 5. Tickets Page (`tickets.php`)
- **Book Ticket:**
  - Select Passenger from the dropdown.
  - Select Train from the dropdown.
  - Select Source Station and Destination Station (system validates that Source and Destination cannot be identical).
  - Select Journey Date.
  - Enter Ticket Fare in INR (₹).
  - Click **Book Ticket**.
- **View All Booked Tickets:** Displays a complete table joining Passenger, Train, Source, and Destination names instead of raw IDs.
- **Update Ticket:** Click **Edit** to modify dates, stations, or fare.
- **Cancel Ticket:** Click **Delete** to cancel and remove the ticket.

### 6. Reports Page (`reports.php`)
Contains dedicated, styled report sections for each of the five compulsory academic SQL queries.

---

## 7. Compulsory SQL Queries Explained

The five queries can be viewed in `reports.php` or executed manually in the phpMyAdmin SQL tab from `database/required_queries.sql`.

### Query A: JOIN Query
*Display passenger names along with train names using JOIN.*
```sql
SELECT Passenger.passenger_name, Train.train_name
FROM Ticket
JOIN Passenger ON Ticket.passenger_id=Passenger.passenger_id
JOIN Train ON Ticket.train_no=Train.train_no;
```
**Explanation:** Combines data across three tables (`Ticket`, `Passenger`, and `Train`) through foreign key matching to show which passenger is traveling on which train.

---

### Query B: Subquery with Aggregate Function
*Display passengers who booked tickets having fare greater than average ticket fare.*
```sql
SELECT DISTINCT Passenger.passenger_name, Ticket.fare
FROM Passenger
JOIN Ticket ON Passenger.passenger_id=Ticket.passenger_id
WHERE Ticket.fare>(SELECT AVG(fare) FROM Ticket);
```
**Explanation:** The nested subquery `(SELECT AVG(fare) FROM Ticket)` calculates the mathematical mean fare of all tickets. The outer query retrieves distinct passengers whose ticket fare exceeds this average value.

---

### Query C: Pattern Matching with LIKE
*Display trains whose names LIKE '%Express%'.*
```sql
SELECT *
FROM Train
WHERE train_name LIKE '%Express%';
```
**Explanation:** Uses the wildcard `%` to filter all trains whose name contains the substring "Express" anywhere within the string (e.g., Shatabdi Express, Rajdhani Express, Goa Express).

---

### Query D: GROUP BY and COUNT()
*Display number of passengers travelling in each train using GROUP BY and COUNT().*
```sql
SELECT Train.train_name, COUNT(DISTINCT Ticket.passenger_id) AS passenger_count
FROM Train
LEFT JOIN Ticket ON Train.train_no=Ticket.train_no
GROUP BY Train.train_no, Train.train_name;
```
**Explanation:** Groups reservations by train and calculates the distinct count of passengers per train. A `LEFT JOIN` is used so that trains with 0 passengers still appear in the list with a count of 0.

---

### Query E: HAVING Clause
*Display trains having more than 100 passengers using HAVING.*
```sql
SELECT Train.train_name, COUNT(DISTINCT Ticket.passenger_id) AS passenger_count
FROM Train
JOIN Ticket ON Train.train_no=Ticket.train_no
GROUP BY Train.train_no, Train.train_name
HAVING COUNT(DISTINCT Ticket.passenger_id)>100;
```
**Explanation:** While `WHERE` filters individual rows before grouping, `HAVING` filters aggregated groups after `GROUP BY`. This query only returns trains where the aggregated passenger count is strictly greater than 100.

---

## 8. How to Demonstrate Query E (HAVING Query)

In standard sample data, we maintain a small, clean dataset (under 10 passengers) for normal presentation so the tables remain easy to read.

To demonstrate the HAVING query returning positive results:

1. Open **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Select `railway_reservation` from the left sidebar.
3. Click on the **Import** tab.
4. Click **Choose File** and select:
   ```text
   database/having_demo.sql
   ```
5. Click **Import** (or **Go**).
6. Return to the browser and refresh the **Reports** page:
   ```text
   http://localhost/RailConnect/reports.php
   ```
7. Scroll down to **Query E**. You will now see **Rajdhani Express** displaying **105 passengers**, successfully demonstrating the `HAVING` clause!

---

## 9. Viva Questions & Project Highlights

1. **Why MySQLi procedural over OOP?**  
   Procedural PHP with standard MySQLi functions (`mysqli_connect`, `mysqli_query`, `mysqli_prepare`) is straightforward, making it easy for an undergraduate student to understand and explain line-by-line during viva.

2. **How is SQL Injection prevented?**  
   All insert, update, and delete actions use **prepared statements** (`mysqli_prepare`, `mysqli_stmt_bind_param`, `mysqli_stmt_execute`) which parameterize user inputs.

3. **How is XSS (Cross-Site Scripting) prevented?**  
   All output rendered to HTML tables and inputs uses `htmlspecialchars()` to escape special characters.

4. **How is Referential Integrity enforced?**  
   The database tables use MySQL **InnoDB** with strict foreign keys (`ON DELETE RESTRICT`). Furthermore, PHP checks existing references before deletion and alerts the user with a friendly error message.
