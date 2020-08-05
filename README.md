# SAGA Project Documentation

### Database

#### Accompanying questions

![acc_questions_Table](https://user-images.githubusercontent.com/10381338/85729329-01b56b00-b6f9-11ea-9301-a5b8aa54d225.jpg)

This table contains information about accompanying questions for a quiz.
It has infromation about the type, position and the quiz that is linked
to.

#### Accompanying questions answers

![acc questions_answers table](https://user-images.githubusercontent.com/10381338/85729843-6f619700-b6f9-11ea-9563-17989f338271.jpg)

This table contains the possible answers for an accompanying question.
It contains the index, text and the id of the accompanying question that
is linked to.

#### Accompanying questions feedback
![acc_questions_Feedback_Table](https://user-images.githubusercontent.com/10381338/85729983-915b1980-b6f9-11ea-870a-873b9bc3aa78.jpg)

This table contains information on an accompanying question about on
which question to be displayed in the initial phase.

#### Accompanying questions positions

![acc_questions_positions_Table](https://user-images.githubusercontent.com/10381338/85730115-ad5ebb00-b6f9-11ea-9200-576df168cd22.png)

This table contains information on an accompanying question about on
which question to be displayed in the revision phase.

#### Classes

![classes](https://user-images.githubusercontent.com/10381338/85730274-d2ebc480-b6f9-11ea-844c-2c744631e098.jpg)


This table contains the information about a class. Here we can see the
name, invitation code for participants, when it has been created, who is
the author (represented as an user in in [users](#Databaseusers) table)
etc. Currently, the `class_active` column is not used.

#### Class invites

![class_invites](https://user-images.githubusercontent.com/10381338/85730364-e434d100-b6f9-11ea-8919-7ced6ca738ad.jpg)

This table keeps track of the sent class invites. Each invitation is a
token that is verified upon registration. The invitation is tied to a
class id from the [classes](#Databaseclasses) table

#### Class Quizzes

![class_quizzes](https://user-images.githubusercontent.com/10381338/85730462-f6167400-b6f9-11ea-8524-eb492f74777b.jpg)

This table contains the links between [classes](#Databaseclasses) and
[quizzes](#Databasequizzes). We store the time when the quiz has been
added to the class.

#### Class users

![class_users](https://user-images.githubusercontent.com/10381338/85730552-06c6ea00-b6fa-11ea-9ee1-03dda1d92eb4.jpg)

This table contains the links between [classes](#Databaseclasses) and
[users](#Databaseusers). By users we contain teachers (including the
author of the class) and the participants. We have a timestamp when an
user has been added.

#### Participants progress

![participants_progress](https://user-images.githubusercontent.com/10381338/85730614-15ad9c80-b6fa-11ea-950c-496cb9144183.jpg)

This table has information about an user's participation in a quiz. Here
we track the progress of an participant through a quiz. We store the
`user_id`, `sessions` - an id from the
[quiz\_session](#Databasequiz-session) table. For each question answered
we store in this table the phase, question index, whether this
participation has been completed and timestamps.

#### Participants scores

![participant_scores](https://user-images.githubusercontent.com/10381338/85730681-252ce580-b6fa-11ea-8c27-64f94907d2ca.jpg)

This table contains information about scores for each participation. We
have an participant id from the [participants
progress](#Databaseparticipants-progress) table , score value and
reason. Please note that currently the page that shows scores
information is disabled.

#### Password resets

![password_resets](https://user-images.githubusercontent.com/10381338/85730749-337b0180-b6fa-11ea-8a88-c1c336e5938f.jpg)

This table is created by default by Laravel to keep track of password
resets.

#### Questions

![questions](https://user-images.githubusercontent.com/10381338/85730819-41c91d80-b6fa-11ea-9f90-52c9f0a81a71.jpg)

This table has information about questions in quiz. Each question has a
test, an optional image link, the index of the correct answer, etc.
Please note that the `question_active` and `question_required` are not
used right now as all questions are required by default and the
activeness of a question is not taken into account.

#### Question answers

![question_answers](https://user-images.githubusercontent.com/10381338/85730890-50173980-b6fa-11ea-8447-470e41c8a559.jpg)

This table contains information about answers for a question. We store
answer information, the question that is tied to and the index. The
`qa_active` column is not used.

#### Quizzes

![quizzes](https://user-images.githubusercontent.com/10381338/85730948-5f968280-b6fa-11ea-9131-04b194aa3e74.jpg)

This table holds the information data for a quiz. We store all the main
characteristics of a quiz. The column `quiz_status` might not hold
accurate data as the status of a quiz is decided by the fact that it has
a session opened or not.

#### Quiz additional messages

![quiz_Add_messages](https://user-images.githubusercontent.com/10381338/85731002-6d4c0800-b6fa-11ea-97a1-ad58adef95ff.jpg)

Here we store the starting messages for a quiz. The message is stored as
a delta data structure from QuillJS (the library used for displaying and
storing these messages). For information about QuillJS and delta data
structure visit [QuillJS Documentation](https://quilljs.com/docs/delta/)

#### Quiz questions

![quiz_questions](https://user-images.githubusercontent.com/10381338/85731067-7b9a2400-b6fa-11ea-875a-b4b291326949.jpg)

In this table we store information about a quiz's questions. We store
the links between a quiz id and question id, index of the question in
the quiz and timestamps.

#### Quiz responses

![quiz_responses](https://user-images.githubusercontent.com/10381338/85731148-8ce33080-b6fa-11ea-965d-919666d6b984.jpg)

This table stores all the responses of a participant in a quiz. For each
session of a quiz we store the answer index, correctness, duration (how
long it took to answer) in seconds and timestamp. This table is used for
the Quiz export functionality.

#### Quiz responses accompanying questions

![q_res_acc_q](https://user-images.githubusercontent.com/10381338/85731235-a1272d80-b6fa-11ea-9302-2ece90057dca.jpg)


This table is similar to [Quiz responses](#Databasequiz-responses) table
but it holds the responses from the accompanying questions. The
responses are in different tables from the normal questions as we can
have different type of answer and the location of the accompanying
question.

#### Quiz scheduling

![quiz_Sch](https://user-images.githubusercontent.com/10381338/85731301-b13f0d00-b6fa-11ea-922d-57130f44b970.jpg)

This table holds information about a quiz's scheduling. We can have set
for a quiz to have a session that starts at a certain time and goes to
all phases without the assistance of the the teacher. The table has
datetime columns for start/end times for each phase of a quiz.

#### Quiz session

![quiz_session](https://user-images.githubusercontent.com/10381338/85731402-c9169100-b6fa-11ea-88f5-6a084e77df60.jpg)

This table holds the information about a quiz's sessions. We have
columns to track every update such as change of phase, quiz start/stop
and contains a snapshot of all the quiz's information at that current
time in a JSON structure in the `qs_quiz_data` column. This column
contains all the information related to the quiz
(title,questions,accompanying questions, messages) at the time of
starting the specific session.

#### Roles

![roles](https://user-images.githubusercontent.com/10381338/85731461-d5025300-b6fa-11ea-819b-ed9710704c5a.jpg)

This table holds all the possible roles we can have for the users.
Currently there are 3 types of roles: Administrator, Teacher and
Participant.

#### Users

![users](https://user-images.githubusercontent.com/10381338/85731538-e3e90580-b6fa-11ea-81ad-d089c1e1a671.jpg)


This table contains all the information related to an user. We store
email, first last name , role id, etc. Please note that the `name`
column is not used as we use the `u_first_name` and `u_last_name` column
to compose the name. The `name` column has not been removed as it's
still required by the Laravel framework.

#### User Logins

![logins](https://user-images.githubusercontent.com/10381338/85731592-ef3c3100-b6fa-11ea-9d36-cf9ce389d40e.jpg)


This table holds a history of users logins. For each user id we have
timestamp of the login.

#### Users Admins

![admins](https://user-images.githubusercontent.com/10381338/85731670-ff541080-b6fa-11ea-947e-d5b6d619d31f.jpg)

This table holds information about current admins. We have also the
concept of the Super Admin, which cannot be removed or demoted.

For development, IntelliJ'S Phpstorm is recommended. Local MySQL
database can be run with WAMP.

In this page we present an in-depth view of the existing codebase, what
is the workflow and examples of possible future fixes.

We will first categorize the file in two main sides : frontend and
backend. Frontend will be considered the views (.blade.html files), CSS
and JS files. Backend are all the existing PHP and config files.

### Backend

In order to understand the overview of the backend, in the following
diagram we can see the created files on top of the existing Laravel's
framework files

![backend](https://user-images.githubusercontent.com/10381338/85731748-0f6bf000-b6fb-11ea-96fa-55484508fd1b.jpg)


#### Repositories(repositories)

Repository files are the classes dealing with database operations. In
order to keep an organized flow of the code, if we need to
retrieve,update or delete anything from the database, those functions
need to be in a Repository file. The sole purpose of functions relying
in a Repository file are only the database operations.

Here, functions receive certain parameters, do the specific operations
and should **always** return a status variable (an associative array)
containing a boolean valued variable called *status*, a string variable
called *message*and if the case requires, a variable with the *result*.
This variable is useful to catch any exception and return the
appropriate feedback to the user in the interface. The status is
propagated until the controller to decide what to show in the end on the
webpage.

Below there is a brief presentation of some of the Repository files in
order to understand what they contain.

##### [AdminRepository](https://github.com/pmpapad/saga-project/blob/f78af046881103fc23b9aa50f0ed6633065dc62a/app/Repositories/Admin/AdminRepository.php)

This repository contains database operations that are needed in the
Admin Pages. Below we can see some examples:

[AddAdmin(\$user\_id)](https://github.com/pmpapad/saga-project/blob/f78af046881103fc23b9aa50f0ed6633065dc62a/app/Repositories/Admin/AdminRepository.php#L134)
function will add an entry in the [users\_admins](#database_todo)
database table and return a status of the operation.

[UpdateUser(\$id, \$email, \$first\_name, \$last\_name, \$role\_id,
\$department,
\$position)](https://github.com/pmpapad/saga-project/blob/f78af046881103fc23b9aa50f0ed6633065dc62a/app/Repositories/Admin/AdminRepository.php#L162)
function updates an existing user by either updating personal
information or current access rights.

[GetUserViewInfo(\$user\_id)](https://github.com/pmpapad/saga-project/blob/f78af046881103fc23b9aa50f0ed6633065dc62a/app/Repositories/Admin/AdminRepository.php#L196)
function retrieves all the needed information from the database in order
to construct the User view page for Administrators. It contains multiple
queries, as we need more complex information.

#### Services

For Developers
==============

Accompanying questions of type 'other questions' that have open structure and are outside of the quiz. 
------------------------------------------------------------------------------------------------------

Are not fully implemented. The backend exists to support them but
implementation to show them in Quiz Monitor page is missing and also
there is an issue with displaying them in the running quiz even though a
quiz is set to display them.

Accompanying questions positions and feedack. 
---------------------------------------------

To decide if an accompanying question needs to be displayed in a quiz
there are these two properties. To decide display in the initial phase,
the `feedback` array property is checked. To check the display in the
revision phase the `positions` array property is used.

Quiz exports
------------

To modify the structure of the exports you need to check the files
inside `app/Exports`. Those are classes of the library
`Maatwebsite\Excel` that is used to generate excel files.

Usage
=====

### Setup

For development, we used the following technologies:

-   Ubuntu `v18.04`
-   MySQL `v8.0.18`
-   PHP `v7.4`
-   Apache `v2.4.41`
-   NodeJS `v8.10` The application is built with Laravel framework
    (version `6.x`) and NPM (version `6.4.1`) for JS and SCSS files.

#### Fresh installation

A complete detailed process can be seen in the [saga_installation](https://github.com/pmpapad/saga-project/blob/master/saga_installation.md)
In order to install the application in a new environment, we need to
pull the code:

`git pull https://github.com/pmpapad/saga-project.git`


Please note that as it is a private repository you need to have the
credentials set.

Now that we have the latest code we want to make sure we meet the
prerequisites. The environment needs to have installed PHP, (at least
PHP`7.2`) and composer. Also, we need to have
[NodeJS](https://nodejs.org/en/) and npm (it comes by default with
NodeJS)for js and scss files compilation. Make sure that the environment
meets all the Laravel's
[requirements](https://laravel.com/docs/6.x#server-requirements).

Run `composer update` to install all the used libraries for Laravel.

Run `npm install` to install all the referenced node modules.

By now all the files should be set.

### Initial configuration

As a reference, [Laravel's official documentation about
configuration](https://laravel.com/docs/6.x#configuration) should be
followed.

Be sure to assign the right permissions for the folders and do **NOT**
assign 0777 permissions for any files.

Credentials and connection details can be completed in the `.env` file
generated by the `php artisan key:generate` command.

**DO NOT** include the *.env* file in the git versioning. It can contain
important information about database connection, mail service
credentials etc.

### ENV file configuration

Note that there are mandatory fields that need to exists in the env
file.

#### Database

We add the connection details in the `.env` file. Example:

``` {.hljs}
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saga-project
DB_USERNAME=<db_username>
DB_PASSWORD=<db_password>
```

#### Branding

``` {.hljs}
APP_NAME='SAGA Project'
APP_ENV=local
#APP_KEY is generated by running  the php artisan key:generate commannd
APP_KEY=<generated>
#set the APP_DEBUG to true to see full error when the application crashes Note that the true value for APP_DEBUG
#should be used only on development environments. This variable is used to decide if captcha is verified and what key
#to use for analytics
APP_DEBUG=true
#the URL is important as it is used when sending emails like Password Reset that use a link. That link is built using
#this variable's value and the necessary route.
APP_URL=<url_of_the_application>
ENABLE_HTTPS=false
#ENABLE_HTTPS this should be false for development and true for live server
```

#### Mail

Settings to configure the mail server. This is mandatory as the SAGA
applications sends emails.

``` {.hljs}
MAIL_DRIVER=smtp
MAIL_HOST=
MAIL_PORT=
MAIL_ENCRYPTION=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_NAME="SAGA Team"
```

#### Captcha

Please note that on APP\_DEBUG= true the \_DEBUG keys are used.

``` {.hljs}
CAPTCHA_SITE_KEY=
CAPTCHA_SERET_KEY=
CAPTCHA_SITE_KEY_DEBUG=
CAPTCHA_SERET_KEY_DEBUG=
```

#### Webserver configuration

Make sure the Apache's vhost points to the `public` folder inside the
application folder `saga-project/public`

### Troubleshooting

By now, the application should be up and running. If it displays a 500
Error make sure to check `laravel.log` file in the `\storage\logs`
folder for a detailed description of the error. It can be a cause of
either file permissions issues or missing `.env` file.


Main page of the wiki

Landing pages are located in :

```
\resources\views\intro\landing_page.blade.php  <-- Home
\resources\views\intro\publications.blade.php  <-- Publications
\resources\views\intro\examples.blade.php      <-- Examples and good practices
```


