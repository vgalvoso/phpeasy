# Welcome to PHPEasy
PHPEasy is a monolithic API-centric php framework.
It's goal is to enable php developers to code freely, write less and do more.

## Features
1. Monolithic API-centric architecture (non-MVC framework)
2. File-based routing
3. Supports HTMX
4. Simple Database Abstraction Layer
5. Helper Functions such as input validator, code generator, upload file, etc.
6. Procedural and OOP hybrid coding
7. Supports PHP 8 and above , MySQL, MSSQL and SQlite
8. Includes basic css and js helpers.

## Intro
For this document let's assume that the server is hosted in your local machine.
The base url is http://localhost/phpeasy

## 1. Views
Create your views inside View folder.

View routes will be automatically created based on View folder structure.
The index.php inside View folder is the entry point,
so route will be http://localhost/phpeasy.

Now create View/admin.php and paste the code below
```html
<h1> Hello World! </h1>
```
the route will be http://localhost/phpeasy/admin


## 1.1 Organize Views
Views can be organized by creating subdirectories,
let's create dashboard and settings views for admin.

Create View/admin/dashboard.php and paste the code below
```html
<h1> Welcome to Dashboard! </h1>
```
the route will be http://localhost/phpeasy/admin/dashboard

Create View/admin/settings.php
```html
<h1> Welcome to Settings Page! </h1>
``` 
the route will be http://localhost/phpeasy/admin/settings
