# Engal
Self-hosted encrypted media cloud storage.

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
- [ ] Frontend base
    - [ ] Set Api url
    - [ ] Check backend availability
    - [ ] Check backend status
    - [ ] Check app version
    - [ ] Check login status
- [ ] Register component
- [ ] Login component
- [ ] User dashboard
- [ ] Logout function
### Phase 5
- [ ] User dashboard (component)
    - [ ] Nav bar (user panel with logout)
### Phase 6
- [ ] Encryption utils
- [ ] Media entity (name, gallery_name, type(format), owner_id, path, upload_time, edit_time)
- [ ] Media upload (images & videos), (name, gallery_name[required], input_files[multiple uploads])
    - [ ] Insert data into database
    - [ ] Upload data to storage dir (filesystem: storage/username/{files})
### Phase 7
- [ ] Dashboard sections (photos, videos)
- [ ] Display galleries in the dashboard
- [ ] Gallery browser
- [ ] All images/videos browser
- [ ] Sorting (by: name, upload_time, edit_time, random)
### Phase 8
- [ ] Image browser
- [ ] Video player
### Phase 9
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
