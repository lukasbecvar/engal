# Encrypted media gallery
Self-hosted encrypted gallery

# TODOS for version 3.0
## Backend
- [X] setup backend api base (symfony)
- [ ] log system
- [ ] maintenance check middleware
- [ ] database check middleware
- [ ] security check middleware (check if api running on ssl (only in prod env))
- [ ] auth api functions (register, login, logout) (USE POST REQUSTS)
- [ ] register disable config
- [ ] / main controller (for check if api connection is valid
- [ ] middleware for check if user valid api token (use user token)
- [ ] create gallery (GET REQUST)
- [ ] get gallery list by logged user (GET REQUEST)
- [ ] upload images function (POST REQUST)
- [ ] get images ids list by gallery name (GET REQUEST)
- [ ] get image where gallery & id (GET REQUEST)
- [ ] protect content for users (only users with token in image data can show image & gallery content)
- [ ] delete gallery & images
- [ ] edit gallery & images
- [ ] document api errors & endpoints
- [ ] content encryption

## Frontend
- [X] setup frontend app base (react)
- [ ] api link setter (for connect to backend server)
- [ ] api running check
- [ ] maintenance running check
- [ ] home component (register & login forms navigation)
- [ ] about component (basic page with engal description)
- [ ] register component
- [ ] register enabled check
- [ ] login component
- [ ] gallery create component
- [ ] gallery list component
- [ ] gallery browser component
- [ ] auth functions
- [ ] gallery gets & create view
- [ ] gallery browser view
- [ ] gallery & image delete function
- [ ] gallery & image edit function (move to gallery, rename)
- [ ] image upload form
