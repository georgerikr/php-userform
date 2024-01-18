# PHP userform

This is a user form application made with PHP. It validates input data after 'Save' button has been pressed, stores that data to the database, refills the form using that stored data and allows the user to edit their own data during the session.

## Installation

To download and run the application on your local machine, follow these steps:

1. Open a terminal in your local development environment folder (usually named `htdocs` if using XAMPP or MAMP, etc.).
2. Clone the repository by running the following command in the terminal: 
`git clone [projects_repository] php-userform`
Replace `[projects_repository]` with the HTTPS URL or SSH key of this repository. The "php-userform" after the repository URL creates a folder named "php-userform" and clones the repository into that folder.
3. After the clone is complete, create a MySQL database and run the SQL query from php-useform.sql in that database to create the 'users' and 'sectors' tables and fill the 'sectors' table with data.
4. If database and its tables are ready, add your database credentials (username, password and database name) to `$dbCredentials` variable in index.php.

The application is now ready to use. Go to your localhost URL `http://localhost/php-userform` to access the application.

### Live application

Live application is located at https://kexdev.ee/php-userform/. 