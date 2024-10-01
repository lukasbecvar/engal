# Engal
Self-hosted encrypted media cloud storage.

## Gallery list
![Gallery list](.github/images/gallery-list.png)

## Gallery browser
![Gallery browser](.github/images/browser.png)

## Backend API
Engal backend is a REST API built with the Symfony framework. When running in the dev environment, you can use NelmioApiDoc for documentation and testing the API at the route `/api/doc`. The API is used for getting and uploading media files, creating galleries and accessing them. User login is based on JWT tokens through LexikJWT and Symfony Security Bundle.

## Frontend
The frontend is a React app that communicates with the REST API configured during the initialization of the application.

## Installation
The entire installation in the dev environment can be run through the `install.sh` script and launched using Docker Compose.

## End of support
This project is no longer being maintained or developed, and no further updates or support will be provided.

## License
The project is open-sourced software licensed under the [MIT license](https://github.com/lukasbecvar/engal/blob/main/LICENSE).
