# Engal
Self-hosted encrypted media cloud storage.

## Preview
![Engal](.github/assets/preview.gif)

## Backend API
Engal backend is a REST API built with the Symfony framework. When running in the dev environment, you can use NelmioApiDoc for documentation and testing the API at the route `/api/doc`. The API is used for getting and uploading media files, creating galleries and accessing them. User login is based on JWT tokens through LexikJWT and Symfony Security Bundle.

## Frontend
The frontend is a React app that communicates with the REST API configured during the initialization of the application.

## Installation
The entire installation in the dev environment can be run through the `install.sh` script and launched using Docker Compose.

### Object storage (MinIO)
- Docker Compose now ships a MinIO service (S3 compatible) plus a helper that provisions buckets `engal-media` and `engal-media-test`.
- Backend storage is configured through `backend/.env.*` (`S3_ENDPOINT`, `S3_BUCKET`, `S3_ACCESS_KEY`, `S3_SECRET_KEY`, `S3_USE_PATH_STYLE`). Defaults target the local MinIO service.
- MinIO Encryption key is configurable with `MINIO_KMS_SECRET_KEY` in docker-compose.yml.
- MinIO web console is available on http://localhost:9001 (default creds: `engal` / `engalsecret`).

## License
The project is open-sourced software licensed under the [MIT license](https://github.com/lukasbecvar/engal/blob/main/LICENSE).
