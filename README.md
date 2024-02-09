# niks-mailerlite

## Notes on design:

1. All field validation is done on the front end. This is standard practice but also the API will be called a million times per minute. We don’t want the API checking whether the e-mail is properly formed a million times per minute. The only thing we do on the write is make sure the e-mail address is converted to all lowercase. Failing to do that (having records like “BOB@gmail.com” and “bob@gmail.com”) would really mess things up on search depending on how the collation of the database is done (or changed) or would be have to be done MySQL on every query. That's wasteful.

2. Search API does not return total number of records. It would be trivial to add but would require an extra SQL query which we may want to avoid on an endpoint that gets a lot of calls. It would be better to create another endpoint then that returns the number of results.

3. Chose Vue.js front end because it requires the least resources and for a simple demo it can be included with CDN--does not require Node.js and npm, etc. In a real application, different choices might be made.

