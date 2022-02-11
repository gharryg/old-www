CREATE TABLE settings (
    id int NOT NULL AUTO_INCREMENT,
    value int,
    headerMessage varchar(255),
    bodyMessage varchar(255),
    PRIMARY KEY (id)
);
INSERT INTO settings (value) VALUES (1);
CREATE TABLE users (
    id int NOT NULL AUTO_INCREMENT,
    firstName varchar(255),
    lastName varchar(255),
    email varchar(255),
    password varchar(255),
    verificationCode varchar(255),
    banned int DEFAULT 0,
    verified int DEFAULT 0,
    admin int DEFAULT 0,
    resetCode varchar(255),
    resetExpireTime int,
    PRIMARY KEY (id)
);
INSERT INTO users (firstName, lastName, email, password, verificationCode, verified, admin) VALUES ('Default', 'Admin', 'admin@gharryg.com', '010660e2ae17b89b7b72a4a8c3119181', 'abcdefghij', 1, 1);
INSERT INTO users (firstName, lastName, email, password, verificationCode, verified, admin) VALUES ('Test', 'User', 'user@gharryg.com', '010660e2ae17b89b7b72a4a8c3119181', 'abcdefghij', 1, 0);
CREATE TABLE samegame (
    id int NOT NULL AUTO_INCREMENT,
    userID int,
    score int,
    blocksLeft int,
    seed varchar(255),
    timestamp int,
    PRIMARY KEY (id)
);
CREATE TABLE pente (
    userID int,
    elo int DEFAULT 500,
    totalWins int DEFAULT 0,
    totalLosses int DEFAULT 0,
    winLossRatio float(4, 3) DEFAULT 0.000,
    currentWinRun int DEFAULT 0,
    longestWinRun int DEFAULT 0,
    PRIMARY KEY (userID)
);
