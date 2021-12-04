# Web Project

This file describes how to initialize and use the website created for the web project course at AMI2B.  

## Clone Git in public_html

TODO

## Initialize database

First one must connect to the database. To do so, go to the root of the project and set *username* and *password*. Use command:
```
vim website/.projetWEB.ini
```
Replace `[login_court]` with your short login. For example, *Michel Arsouin* should replace it with `marsouin`.  
Save the editing with `Esc` followed by command `:x`.  
To then initialize the database, use command:
```
./reset_database.sh
```
