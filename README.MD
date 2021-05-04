# How to Install the software
To install the software on local and production machine, follow the following steps:  
1. Download a zipped copy of this repository or clone via Github bash(or any configured git CLI) `git clone github.com/tasiukwaplong/csc411.git`
2. Create a MySQL database and import the SQL file located in: `src/Config/insurance.sql`
3. Create an account on [paystack.com](https://paystack.com) and copy the paystack secret key which can be found in your User dashboard and navigating to `settings/API and Webhooks/`
4. Host the files either using a development server such as XAMPP, WAMP, LAMP or on a live server
5. Go to `src/Config/config.php` and set the necessary config variables:  
```php
[
  'host' => 'LOCALHOST',// replace with database host
  'database' => 'DATABASE',// replace with database name
  'username' => 'USERNAME',// replace with database username
  'password' => 'PASSWORD', // replace with database password
  'paystack_private_key' => 'SK_KEY', // replace with paystack secret key
  'frontend_url' => 'https://frontend.com', // replace with frontend URL
  'backend_url' => 'https://backend.com' // replace with backend url
]
```
Also, remeber to set the environment you are using. it can be any of `DEV` or `PROD`. DEV implies you are in development mode, PROD implies production.

## API Documentation
The documentation on how the API works can be found here: [https://docs.google.com/document/d/178jgwj2XJtn6GLXqtpqjR0-KiTa8GnPLIVbwCxAlBeI/edit](https://docs.google.com/document/d/178jgwj2XJtn6GLXqtpqjR0-KiTa8GnPLIVbwCxAlBeI/edit)

### System requirements/configurations
To run this on a local development machine, you need:
1. PHP >= 7
2. MySQL 
3. PostMan (Recommended) or any other HTTP API testing software  
These are mostly already set by default in most live servers