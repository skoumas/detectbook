#detectbook

## Latest Relesae
- Added Edit Entry
- Added Delete Entry
- Comments can happen now
- Signup function
- Validator fixed
- Relationship hasMany retrieves data

##TODO
- Fix the to many DB connections error when retrieving child of hasMany
- Return user data for each comment
- Edit comment
- Delete comment

##To run please do:
1) Have docker and docker-compose at your computer
2) Run `docker-compose up -d`
3) Login to the database [detectbook] at port [9906] with username [detectbook] and password [detectB00k!@#] at your [localhost] and import the sql file from the /Seed folder
4) Check that localhost is running with http://0.0.0.0:8100/
5) Go to the react folder `cd detectbook`
6) `npm install`
7) `npm run`
8) A window should open with the application at your browser.

Any questions contact me at skoumas.net@gmail.com
