# Engal
Self-hosted encrypted media cloud storage.

[Stable version](https://github.com/lordbecvold/engal/tree/main)

## Recode (Version 4.0) - in progress
I am currently working on a complete recode of the application.
### Phase 1
- [X] Setup React (frontend)
- [X] Setup Symfony (backend)
- [X] Setup (tests github actions)
- [X] Setup docker env
### Phase 2
- [X] Index/status controller
- [X] Error manager
- [X] Log manager
- [X] Setup NelmioApiDocBundle 
- [X] Security middleware (XSS escape, SSL check)
- [X] Database online check middleware
- [X] Maintenance mode middleware
- [X] SSL Only mode middleware
### Phase 3
- [X] User system (JWT auth)
- [X] Auth actions log
- [X] Token invalidation
- [X] Blacklisted token check middleware
- [X] Last login & register time in user entity
- [X] Command for create user
- [X] Command for granting admin permissions to a user
- [X] User status api endpoint
- [X] Register api endpoint
### Phase 4
- [X] Frontend base
    - [X] Set Api url
    - [X] Check backend availability
    - [X] Check backend status
    - [X] Check app version
- [X] Login component
    - [X] Check login status
    - [X] Login refactor
- [X] Register component
- [X] Autologin after register
- [X] Logout function
- [X] User dashboard
    - [X] Nav bar (user panel with logout button)
- [X] API connecton error with url config reset
- [X] Error component reload
- [X] Frontend app devmode (console.log errors)
- [X] Preventive refactor
### Phase 5
- [X] Design base components
- [X] Sub navbar
- [X] Frontend refactor
### Phase 5,5
- [X] Fix expirated login token error
- [X] Backend cache token blacklist (filesystem cache)
### Phase 6
- [X] Media entity (name, gallery_name, type(format), owner_id, path, upload_time, edit_time)
- [X] Media upload (images & videos), (name, gallery_name[required], input_files[multiple uploads])
    - [X] Upload form component
    - [X] Accept only media formats
    - [X] Upload status
    - [X] Upload limits policy config
    - [X] Insert data to Media entity
    - [X] Upload file filter (users, type)
    - [X] Upload gallery name value
    - [X] Recode upload controller & create storage manager
    - [X] Upload & gallery list api doc
    - [X] Upload & gallery unit test
    - [X] Recode frontend upload component
    - [X] Design upload form
- [X] All response codes get from response const
- [X] Media fixtures
### Phase 6.5
- [X] Setup OPCache
- [X] Setup APCU
- [X] Setup PHP_codesniffer
### Phase 7
- [X] Dashboard stats
- [X] Dark scroll bar
- [X] Get media content endpoint
- [X] Get first gallery token by gallery id
- [X] Get minified thumbnail api endpoint
- [X] Move media counter to sub navbar
- [ ] Display galleries in the dashboard 
- [ ] Gallery browser (photo & video (image priority))
- [ ] Browse all galleries
- [ ] Sorting (by: default(by database id), name, upload_time, edit_time, random)
### Phase 8
- [ ] Image browser
- [ ] Video player
### Phase 9
- [ ] Data wipe command (drop all users, logs, storage data)
- [ ] Backup command (extract all media to tar.gz archive)
- [ ] Encryption system
### Phase 10
- [ ] Port frontend to electron
- [ ] Port frontend to react-native
### Phase 11
- [ ] Deploy system
- [ ] Basic documents in markdown

## TODOS: version 5
- [ ] Gallery info (show gallery data: count, owner, time data)
- [ ] Gallery share (configurate in gallery info), (use owner_id in databse [json/array])
- [ ] Media info (get media data, name, gallery_name, size, type, time data)
- [ ] Media delete function
- [ ] Media move (move to gallery), single or multiple
- [ ] Media edit (name, replace file with reupload)
- [ ] Account settings (change password, username, account delete)
- [ ] Admin system
    - [ ] Dashboard
    - [ ] Log viewer
    - [ ] User management
    - [ ] Ban system

## License
The framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
