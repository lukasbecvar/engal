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
- [ ] Media upload (images & videos), (name, gallery_name[required], input_files[multiple uploads])
    - [X] Upload form component
    - [X] Accept only media formats
    - [X] Upload status
    - [ ] Upload limits policy config
    - [ ] Insert data to Media entity
    - [ ] Upload file filter (users, type)
    - [ ] Design upload form
    - [ ] Upload api doc
    - [ ] Upload unit test
- [ ] Encryption system
- [ ] Media fixtures
- [ ] Data wipe command (drop all users, logs, storage data)
### Phase 7
- [ ] Dashboard sections (stats - userbar, photos, videos)
- [ ] Display galleries in the dashboard
- [ ] Gallery browser
- [ ] All images/videos browser
- [ ] Sorting (by: name, upload_time, edit_time, random)
### Phase 8
- [ ] Image browser
- [ ] Video player
### Phase 9
- [ ] Account settings (change password, username, account delete)
- [ ] Admin system
    - [ ] Dashboard
    - [ ] Log viewer
    - [ ] User management
    - [ ] Ban system
### Phase 10
- [ ] Backup command (extract all media to tar.gz archive)
### Phase 11
- [ ] Port frontend to electron
- [ ] Port frontend to react-native
### Phase 12
- [ ] Deploy system
    - [ ] Build script (build final frontend & symfony for production use to deploy archives)
    - [ ] Backend install script (install backend project to new server)
    - [ ] Backend update script (autoupdate backend)
    - [ ] Check frontend app version (check if versions matching with backend version)
### Phase 13
- [ ] Refactor / code clean & check
- [ ] Complete doc

## License
The framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
