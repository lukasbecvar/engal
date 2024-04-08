# Engal
Self-hosted encrypted media gallery.

## Dev Plan (Version 4.0)
### Phase 1
- [ ] Setup React & Symfony + configure
- [ ] Setup (PHPStan & PHPunit github actions)
### Phase 2
- [ ] Error manager
- [ ] Log manager
- [ ] Security middleware (XSS escape, SSL check)
- [ ] Database online check middleware
- [ ] Maintenance mode middleware
### Phase 3
- [ ] User system (JWT auth)
- [ ] Register function (default role USER)
- [ ] Auth actions log
- [ ] Command for granting admin permissions to a user
### Phase 4
- [ ] Frontend base (React Router, login check)
- [ ] Login & register components in frontend
- [ ] Store login token to local storage
- [ ] Check if stored login token is valid
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
- [ ] Backup command (extract all media to tar.gz archive).

## License
The framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
