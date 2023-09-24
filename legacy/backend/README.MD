# Installation
- Install php8+ with mysql or mariadb
- Web root set to public/ directory
- Import sql structure to your database
- Edit values in config.php (Main: url = access API url)

# Error codes
- 1: Vendor, autoload not found - Try reinstall composer components.
- 2: Request for wrong url - Use url defined in config.php.
- 3: Protocol error - Use https for request.
- 4: Maintenance mode is enabled - set maintenance in config.php to false.
- 5: Database error (connection refused) - check if database running.
- 6: Insert query function server query could not be completed - check if db running or table exist.
- 7: Invalid api token is null - user url?token=1234.
- 8: Empty action paramater - use like url?token=1234&action=status.
- 9: Unknow action - check url and validate request.
- 10: Undefined galleryName - just add &galleryName=gallery_name_in_db to url.
- 11: Ids not found - Database is empty or galleryName not found in database.
- 12: Undefined ID - add &id=1 to url like query string for GET.
- 13: POST - Request required - send post request on server.
- 14: GET undefined value limit GET - set limit like:&limit=10
- 15: GET undefined value limit GET - set limit like:&limit=10
- 16: undefined name GET - set get query string like:&name=name

# Requests examples 
- status: url/?token=1234&action=status
- counter: url/?token=1234&action=counter
- all ids get: url/?token=1234&action=ids
- all ids randomize get:  url/?token=1234&action=idsByName&galleryName=randomImagess2WH92Aww
- all ids by gallery name: url/?token=1234&action=idsByName&galleryName=galleryName
- get all gallery names: url/?token=1234&action=allGaleryNames
- get image content by id: url/?token=1234&action=imageContent&id=1
- get all images data by gallery name url/?token=1234&action=getAllImagesDataByGallery&galleryName=name&limit=50&startBy=0
- upload: url/?token=1234&action=upload = POST with data [name, gallery, content]
- delete by id: url/?token=1234&action=delete&id=1
- edit by id: url/?token=1234&action=edit&id=1&galleryName=gallerynew&name=newname

# Config 
- url: url with subdomain to access API
- version: define API version
- encoding: set mysql connection encode
- https: (True/False) -> https only set
- dev_mode: (True/False) -> for debuging
- maintenance: (True/False) -> for disable api function
- token: API access token
- mysql-config: mysql server connection values
