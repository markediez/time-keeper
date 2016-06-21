<?php
  class DBLite extends SQLite3 {
    function __construct() {
      $this->open(__DIR__ . '/development.sqlite3'); // __DIR__ gets the cwd of this file
    }

    function runquery($query) {
      $statement = $this->prepare($query);
      return $statement->execute();
    }

    function initialStart() {
      // Store each query
      $statement = array();
      $index = 0;

      // Roles Table
      $statement[$index++] = $this->prepare('CREATE TABLE Roles (
        id INTEGER PRIMARY KEY,
        role TEXT NOT NULL UNIQUE,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
      );');

      // User Table
      $statement[$index++] = $this->prepare('CREATE TABLE Users (
        id INTEGER PRIMARY KEY,
        role_id INT NOT NULL,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
      );');

      // Job Table
      $statement[$index++] = $this->prepare('CREATE TABLE Jobs (
        id INTEGER PRIMARY KEY,
        user_id INT NOT NULL,
        title TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
      );');

      // Time Log Table
      $statement[$index++] = $this->prepare('CREATE TABLE WorkLog (
        id INTEGER PRIMARY KEY,
        user_id INT NOT NULL,
        job_id INT NOT NULL,
        title TEXT NOT NULL,
        start_time DATETIME NOT NULL,
        end_time DATETIME,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
      );');

      // Run Queries
      for($i = 0; $i < $index; $i++) {
        $statement[$i]->execute();
      }
    }
  }

  function connectDB() {
    return new SQLite3('db/development/development.db');
  }
?>
