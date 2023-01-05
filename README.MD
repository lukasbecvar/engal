# Encrypted media gallery

Self-hosted end-to-end encryption gallery for desktop or phone

## Features
- Image content & names encryption with custom key
- Cloud remote storage
- Private self-hosted database
- API secure access with token

## TODOS
Comons
- [ ] User system (register, login, separated images for each user)
- [ ] Code refactor

Backend - API
- [X] Database table structure
- [X] Service status getter
- [X] Token validator (Check if request can access to database)
- [X] Stats getter (Media counters)
- [X] Get all ids in json
- [X] Get ids in json by gallery name
- [X] Get galleries list
- [X] Get image content by ID
- [X] Logging system
- [X] Media upload system + script for auto upload
- [X] Image uploader script get name from file
- [X] Gallery uploader script get name remove extension
- [X] Gallery dump name set id_+filename
- [X] Data resources in file system


Frontend - Client
- [X] Check API Running on start
- [X] Images gallery name select
- [X] All images selection in list
- [X] Random gallery in list
- [X] Image select with gallery lib
- [X] Image uploader
- [X] Stats in main page images, gallery counter
- [X] Image delete
- [X] Image page system
- [X] Delete image yes or no prompt
- [X] Image edit (name, gallery)
- [X] Gallery link in image description
- [X] Encryption functions
- [X] Encryption disable
- [X] Multiple images uploader
- [ ] Final UI redesign
- [ ] Port APP to electronJS
- [ ] Prot APP to Android

## Dependencies
- Backend requires PHP8+, Composer, MySQL or MariaDB.
- Client requires HTML/JS(React)/CSS compatibile runner.

* Whoops
   * Version: **2.14**
   * [Github](https://github.com/filp/whoops)
* ReactJS
   * Version: **18.2.0**
   * [Website](https://reactjs.org/)
* Light gallery
   * Version: **2.7.0**
   * [Website](https://www.lightgalleryjs.com/)
* Jquery
   * Version: **3.6.3**
   * [Website](https://jquery.com/)
* Crypto-JS
   * Version: **4.1.1**
   * [Github](https://github.com/brix/crypto-js)
