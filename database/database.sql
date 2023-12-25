create database airlinedb;
USE airlinedb;

CREATE TABLE User (
    ID INT AUTO_INCREMENT,
    Name VARCHAR(255),
    Email VARCHAR(255),
    Password VARCHAR(255),
    Tel VARCHAR(20),
    Type ENUM('passenger', 'company'),
    PRIMARY KEY (ID)
);

CREATE TABLE Company (
    ID INT AUTO_INCREMENT,
    Bio TEXT,
    Address VARCHAR(255),
    Location VARCHAR(255),
    Username VARCHAR(255),
    Logo VARCHAR(50),
    Account DECIMAL(10,2),
    PRIMARY KEY (ID),
    UserID INT,
    FOREIGN KEY (UserID) REFERENCES User(ID)
);

CREATE TABLE Passenger (
    ID INT AUTO_INCREMENT,
    Photo VARCHAR(50),
    PassportImg VARCHAR(50),
    Account DECIMAL(10,2),
    UserID INT,
    PRIMARY KEY (ID),
    FOREIGN KEY (UserID) REFERENCES User(ID)
);

CREATE TABLE Flight (
    ID INT AUTO_INCREMENT,
    Name VARCHAR(255),
    Itinerary VARCHAR(255),
    RegisteredPassengers INT,
    PendingPassengers INT,
    Fees DECIMAL(10,2),
    StartDay DATETIME,
    EndDay DATETIME,
    Completed BOOLEAN,
    CompanyID INT,
    Source varchar(255),
    destination varchar(255) ,
    Canceled BOOLEAN,
    PRIMARY KEY (ID),
    FOREIGN KEY (CompanyID) REFERENCES Company(ID)
);

CREATE TABLE PassengerFlights (
    PassengerID INT,
    FlightID INT,
    Status ENUM('completed', 'pending'),
    FOREIGN KEY (PassengerID) REFERENCES Passenger(ID),
    FOREIGN KEY (FlightID) REFERENCES Flight(ID)
);