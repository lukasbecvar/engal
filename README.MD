# Encrypted media gallery
Self-hosted encrypted gallery

# TODOS for version 3.0
## Backend
- [X] setup backend api base (symfony)
- [X] log system
- [X] maintenance check middleware
- [X] database check middleware
- [X] security check middleware (check if api running on ssl (only in prod env))
- [X] / main controller (for check if api connection is valid)
- [X] auth api functions (register, login, logout) (USE POST REQUSTS)
- [X] register disable config
- [X] upload images function (POST REQUST)
- [X] get gallery list by logged user (POST REQUEST)
- [ ] get gallery thumbnails base64 in gallery list (for use in gallery selection)
- [ ] get images ids list by gallery name (POST REQUEST)
- [ ] get image where gallery & id (POST REQUEST)
- [ ] protect content for users (only users with token in image data can show image & gallery content)
- [ ] images
- [ ] edit images (move to gallery, rename)
- [ ] document api errors & endpoints
- [ ] content encryption

## Frontend
- [X] setup frontend app base (react)
- [X] api link setter (for connect to backend server)
- [X] api running check
- [X] maintenance mode check
- [X] home component (register & login forms navigation)
- [X] register enabled check
- [X] register component
- [X] login component
- [X] logout component
- [X] user home (main component with user info)
- [X] upload component
- [ ] gallery list component
- [ ] gallery browser component
- [ ] gallery & images counter
- [ ] image delete function
- [ ] image edit function (move to gallery, rename)
- [ ] catch error show only in dev mode

## Version 4.0
- [ ] sort by upload date, name, size + reverse
