# Encrypted media gallery
Self-hosted encrypted gallery, builded with symfony 6 & bootstrap 5

## Light Gallery 1.4.1
Engal uses an outdated version of the Light Gallery 1.4.1 library because this version is modified for this project

## Installation (with apache & mysql)
- Set Mysql data in .env
- Set apache root dir to public/
- Run install script (scripts/install.sh)
- Disable apache autoindex module
- Run migrate (scripts/migrate.sh)
- Regenerate APP_SECRET in .env
- set APP_ENV to prod (if using in production server)

## Required
 - php-8.1 or <
 - mysql8 or <
 - php-pdo

## TODOS for version 3.0
- [ ] Dumper script (dump all stored galleries to archive)
- [ ] User settings (password change, profile pics)
- [ ] Better gallery list manager (create, delete, move)
- [ ] Better images managment (rename, move to another gallery)
- [ ] Upload progress bar
- [ ] Suport video formats
- [ ] admin system (ban system, visitors table, log reader)
- [ ] REST-API (json api for client app)
- [ ] Client Frontend (react native & electron app)
- [ ] Docker compose file
- [ ] Tests

## Dependencies
* Symfony
   * Version: **6.3**
   * [Website](https://symfony.com/)
* Jquery
   * Version: **3.7.1**
   * [Website](https://jquery.com/)
* Bootstrap
   * Version: **5.3.2**
   * [Website](https://getbootstrap.com/)
* lightGallery
   * Version: **1.4.1**
   * [Website](https://www.lightgalleryjs.com/)
