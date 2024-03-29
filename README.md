# niks-mailerlite

Author: Nik Stankovic <niks.work.goog@gmail.com>

You can also test this on https://niks-lab.com/mailerlite/

- [Setup](#setup)
- [Usage](#usage)
- [Code Structure](#code-structure)
- [Answers to questions in the task](#answers-to-questions-in-the-task)
- [Notes on design](#notes-on-design)


## Setup


1. Clone repository `git clone https://github.com/nikslab/niks-mailerlite.git`
2. `cd niks-mailerlite/` and `cp .env-sample .env`


Two options from here.


**A: If using *Docker***


3. Make sure nothing is running on your local machine on port 80 or 3306 (HTTP and MySQL)
4. Spin up a container with `docker-compose up -d --build`
5. Open http://localhost in browser


**B: If using own local environment (requires MySQL server, PHP, and nginx or Apache)**


3. Create database `niks_mailerlite` on local MySQL
4. Create a user/password with access to `niks_mailerlite` database
5. Import database structure in from `db/database.sql` with something like `mysql -umyuser -p niks_mailerlite < db/database.sql` (adjust as needed)
6. Edit `.env` as needed for user/pass. Root password can be ignored in this case, it is there for Docker only. Make sure to change the `MYSQL_HOSTNAME` to **localhost** (or otherwise as appropriate) as the default for this is also optimized for Docker.
7. Point your web server directory (Apache or nginx) to `src` directory


## Usage


1. Open http://localhost/
2. Click on *Populate database with random e-mails in bulk*. This will allow you to quickly add a bunch of e-mail addresses to the database, you can use the random once provided and/or add yours. One e-mail address per line. It also serves as a test. Note: some e-mail addresses are pre-configured as duplicates on purpose, it servers as a test. You can click on Submit many times; it should fail with "Already exists" after the first time with the same input.
3. Click on *Search Subscriber* from the index page. You can just Search to browse with pagination.
4. Otherwise test as needed. Adding one subscriber is done on the "Add subscriber" page. Navigation is at the top of the page.


## Code structure

Code structure follows a typical *Docker* structure with `db` folder for database setup file, `nginx` folder for nginx config file and all the source code in the `src`.

"Public" html code in our case is directly in the `src` folder, sometimes it is put in `src/public`.

HTML (Vue.js) files, including the index.html are in the `src` folder. API files are in the `api` folder.

There is only one `config.php` file for the APIs, which includes some configuration and functions, it is placed in the same directory as the APIs, it could also be placed elsewhere outside of public directory.



## Answers to questions in the task


0. **Link to fairly recent code** https://gist.github.com/nikslab/ef787bab825342c6459d7c051a55ffbb

This is a WordPress plugin (not all files are included) that syncs inventory in WooCommerce for FragranceNet drop shipping clients.

  The first file fragrance-net-woo-syncer.php is the main plugin file. Much of it functions that have to exist to define the plugin to Wordpress. It is essentially the manifest and the setup page. Some of it was auto-generated or is templated.

  The second file functions.php are some of the helpers function I wrote (and often use in other projects) for logging and database interactions, so code there is 100% mine.

  The plugin is used by a client of a client.


1. **How to scale the WRITE endpoint?**

There is not much we can do to scale a WRITE endpoint because new subscribers have to be written to the database. It's just a scaling of the infrastructure, so more load balanced servers or something like AWS Lambda. I am not checking if the subscribers are in the database already (see *Notes on design*), so there is nothing else that can be done. Using something like Redis is possible if there really are a lot of repeat requests to WRITE. In that case a Redis/Memcache solution might be helpful simply to check if the user exists prior to the WRITE. I'd have to know the ratio of repeat requests to judge if it would improve things.

 Scaling a READ endpoint is a different question. Two ways to do this. For simple lookups by e-mail address TEXT FILES can be created with the JSON response. This is very effective because the load is entirely switched to the web server. I have actually done this on a project. There are limits to this mainly in the number of files that can be had on a system (inodes). So that would depend on how many records there are. I created a test file with this project, so you can see for example https://niks-lab.com/mailerlite/src/api/email/niks.work.goog@gmail.com it's just a JSON API response as text file.  

 More realistically I would use Redis or Memcache. You ask for a configuration, but I am not sure what you mean here. The basic idea is that all incoming API requests would be stored in Redis for example, so for any new READ requests, Redis would be checked first before querying the database.


2. **How to scale it all 10 times?**

There are different architectures for scaling different things. For these two endpoints specifically, if that is the question, I would rewrite them in Python and put them on AWS Lambda (serverless) and use the AWS API Gateway to manage things like authentication, etc. These endpoints are trivial.

 The reason for this is that it allows for growth and scaling both up and down.

 "But we prefer PHP". PHP will work on AWS Lambda with *bref*, though I wouldn't go there. Your question is specifically on these two endpoints. If you want to serve 10 million requests per minute ("10 times 1 million per minute") then converting them to Python and using AWS is the BEST and most painless, quickest and cheapest way that solves all kinds of growth problems.
 Besides, even with a preference for PHP, other parts of the application can be written in PHP, while these two, and perhaps a few other endpoints, can be written in Python. These are trivial endpoints and can be easily converted. AWS API Gateway provides many other features for APIs including security, throttling, authentications, etc




## Notes on design


1. All field validation is done on the front end. This is standard practice but also the API will be called a million times per minute. We don’t want the API checking whether the e-mail is properly formed a million times per minute. The only thing we do on write is make sure the e-mail address is converted to all lowercase. Failing to do that (having records like “BOB@gmail.com” and “bob@gmail.com”) would really mess things up on search depending on how the collation of the database is done (or changed) or would be have to be done MySQL on every query. That's wasteful.


2. On insert, there is no check to see if the subscriber exists. We simply try to do it anyway. Table is indexed unique e-mail field so if the e-mail exists there will be a very specific MySQL error 1062 which will indicate that the subscriber already exists. This is somewhat risky if error codes change in the database, but is more efficient because it saves database queries.


3. Your task description indicates that the GET subscriber endpoint will be called a lot, but you do not indicate what to optimize on. For example, I would assume that the search would always and only be on EXACT e-mail address seeking other information (name, status), but then you mention paging, which means multiple results. So we are going to have a bunch of "LIKE" in the SQL statements, so I gave up optimizing on speed here.


4. Search API does not return the total number of records. It would be trivial to add with a COUNT()but would require an extra SQL query which we may want to avoid on an endpoint that gets a lot of calls. It would be better to create another endpoint that returns the number of results. In other words, I am optimizing as much as possible on speed, except where your task description indicates that may not be the main goal.


5. I chose Vue.js front end because it requires the least resources and for a simple demo it can be included with CDN--does not require Node.js and npm, etc. In a real application, different choices might be made.


6. Did not bother with https (using http) in Docker so as not to have to deal with SSL certificates, that's not what this test is about. Have obviously done SSL certificates it would just be a distraction here.





