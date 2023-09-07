<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Laravel 10 Simple API

System requirements
<ul>
    <li><a href="https://www.php.net/">PHP 8.1+</a></li>
    <li><a href="https://www.postgresql.org/">Postgres 9.5+</a></li>
    <li><a href="https://getcomposer.org/">Composer</a></li>
</ul>

Instructions
<ol>
    <li>Execute "composer install" command</li> 
    <li>Rename .env_example file into .env</li>
    <li>Set params for database connection in .env</li>
    <li>Execute "php artisan <b><span style="color: lime;">migrate</span></b>" command</li>
    <li>Execute "php artisan <b><span style="color: lime">tinker</span></b>" command</li>
    <li>Inside <b><span style="color: lime">tinker</span></b> write the following code "User::factory(50)->create();" and hit enter. The code will generate 50 fifty records in users table for test purpose</li>
    <li>Execute "php artisan <span style="color: lime;"><b>serve</b></span>" command to start local web server on port 8000. Use --port parameter to change the default port. </li> 
</ol>

##  API Documentation
 - API web interface can be accessed on http://localhost:8000/api/documentation#/Users
 - API Documentation tool <a href="https://swagger.io/">Swagger</a>

##  Resources
 - https://laravel.com/docs/10.x
 - https://www.udemy.com/course/laravel-for-rest-apis
