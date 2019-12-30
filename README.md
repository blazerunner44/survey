# PHP Simple Survey
This survey system is written in PHP. It is small in size and footprint but it can handle alot. 

<h2>Screenshots</h2>
This is what everyone wants to see first right?

<h3>Interface</h3>
The user interface is very clean and modern. Eliminate distractions for users so they can focus on providing you better answers

![alt tag](https://blazerunner44.me/github/screenshots/survey/main_page.png "Survey interface")

<h3>Administration Panel</h3>
The backend panel allows you to view the results from a overall perspective. An email will be sent to you with every survey submission, but the results page allows you to view the overall results as your survey collects responses.

![alt tag](https://blazerunner44.me/github/screenshots/survey/results.png "Results")

The administration panel also allows you to add and edit questions on your survey. There are currently 6 different question types:
 - Yes or No
 - Text Box
 - Paragraph
 - Multiple Choice
 - Expanded Multiple Choice
 - Checkboxes
 - Number Slider

![alt tag](https://blazerunner44.me/github/screenshots/survey/add_question.png "Add a question")

<h3>User Support</h3>
While multiple user support isn't a feature yet, it is on the list of things to do! Currently, one administrator account is created at instalation. The goal is to have 3 different kinds of accounts. One admin (created at install), full access, and read only. 

![alt tag](https://blazerunner44.me/github/screenshots/survey/user_support.png "Add a question")



# Installation
1. First, modify the `getConnection()` function inside `class/Model.php` to contain your database credentials.
 ex. 
 ```
 public static function getConnection(){
		 return mysqli_connect(
			 'localhost', //Database server
			 'db_user', //Database user
			 'db_pass', //Database password
			 'db_name' //Database name
		 );
	}
 ```
2. Run `install.php` to complete the setup.

## Help Wanted
This is by no means a finished product. If you would like to help develop this survey there are most likely some issues or featured logged on Github. Feel free to suggest/implement your own features to fit your needs. 
