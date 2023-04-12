# User authentication system

User authentication is a security process that verifies the identity of a user attempting to access a system or network. It requires a human-to-machine transfer of credentials, such as username and password, or other forms of input. User authentication helps prevent unauthorized users from accessing or damaging systems, stealing information, or causing other problems.

The user authentication system is the foundation project for any type of web-based application because every application has some restrictions on who can and cannot use the system for which we need to create some authentication mechanism. 

Here is a typical user authentication system that can be used to login user and register new user; in case, if user has forgotten his password then he can retrieve it via email link where he will receive the account retrieval token link to change the password of his existing account. If all goes well, he will be able to log into the application, and the server will return an authorization token, which may be used as a bearer authorization token in the API request header.

## Getting started

We would be using [MySQL](https://www.mysql.com) server for database and [PHP](https://www.php.net) scripting language for backend development.

### Set up the local development environment

Start the mysql server 

    mysqld --console

Start the PHP server

    php -S localhost:8000

### Check if PHP server is working properly

1. Open the [localhost:8000](http://localhost:8000/) link in the browser, that must show your client side app. If it does then that means your PHP server is running successfully.

2. Now go to the [localhost:8000/api/](http://localhost:8000/api/) link in the browser that should show handshake greetings of the API; which means your app is accessible. To access the API, any client can be used like  curl, postman, thunderclient, etc. 

3. To check the database, go to the [localhost:8000/api/adminer.php](http://localhost:8000/api/adminer.php) link and enter the credentials to login. If you have not selected any database in the login form then you might need to select it now to get inside your database; otherwise you would be seeing the required database where you would be able to find all the associated tables for this project.
