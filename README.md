CMS-Fire [aka CodeIgniter on Fire] is a content management system that allows the indexing of relevant links using a hot-swapping algorithm.

To view a working demo check out: http://cmsfire.timnuwin.com


TO SET UP:


1].  Create a new Database in MySQL called "cmsfire"

2].  Import the table scructure from "\application\migrations\tableStructure.sql"

3].  Navigate to "application\config\database.php" then add your proper MySQL credentials for "username", "password" and "database".

4].  Set write permissions on in "application\controllers\", this is because once the script runs it will delete "initial.php"

5].  Change the default salt value in "application\models\core\login_model.php" and "application\models\core\user_model.php", it will be named <i>$salt</i>.  **These values should be equal to each other

6].  Next load in the browser: "YOUR_SERVER_NAME" and create your user.


You are now set up to use CMS Fire!  Create a category to start bookmarking and discussing topics.
