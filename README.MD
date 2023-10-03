# Encrypted media gallery

Self-hosted encrypted gallery, builded with symfony 6 & bootstrap 5

# Installation (with apache & mysql)
- Set Mysql data in .env
- Set apache root dir to public/
- Run install script (scripts/install.sh)
- Disable apache autoindex module
- Run migrate (scripts/migrate.sh)

## Required
 - php-8.1 or <
 - mysql8 or <
 - php-pdo

# TODOS for version 3.0
- [ ] Dumper script (dump all stored galleries to archive)
- [ ] User settings (password change, profile pics)
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
