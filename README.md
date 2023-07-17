# Social-Network

This repository contains the code for a prototype of a social network web application. The backend of the application is built using the Express.js framework, while the frontend is built using React. The database used is PostgreSQL. Both the backend and the database are deployed in Docker containers.

## Features

The application provides the following features:

- User authentication and registration (including Google and Facebook authentication)
- Posting, liking, and commenting on posts
- Adding other users as friends
- Searching for users in the system
- Customizing own profile

## Technologies Used

- PHP
- Yii2
- JavaScript
- React
- PostgreSQL

## Prerequisites

Before running the application, make sure you have the following installed:

- Node.js
- PHP
- Composer
- PostgreSQL

## Installation

1. Clone this repository: `git clone https://github.com/marharita08/Social-Network`.

2. To install the required libraries, simply execute the following files:
   - `install_api.cmd`
   - `install_front.cmd`

3. Before running the application, you need to add a `.env` file to the `front` folder with the following properties:

    | Key                              | Value                                                |    
    |----------------------------------|------------------------------------------------------|
    | REACT_APP_API_URL                | API url                                              |

4. Create database for the application. Configure the database connection in the `api/config/db.php` file. Provide the appropriate credentials for created database.
5. Create the required tables using `init.sql`.
6. Start the backend by running `run_api.cmd`.
7. Start the frontend by running `run_front.cmd`.
8. Open your web browser and visit `http://localhost:3000` to access the Social Network application.