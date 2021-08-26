# Implementation of object storage with Pollen Solutions Filesystem component and MinIO

MinIO is an S3 compatible object storage system.
This simple application allows you to simply launch MinIO and pair it with Pollen Solutions component to perform the 
basic actions of adding, reading and deleting files. 

## Install

### Clone the repository from Github

```bash
git clone git@github.com:jordy-manner/lab-filesystem-minio.git
```

### Install dependencies

```bash
composer install
```

### Launch the MinIO service

```bash
docker-compose up
```

## Configure MinIO

### Connexion

Visit: [http://127.0.0.1:9001](Minio Console)

Enter the username (minio) and the password (minio123).

![Minio login](docs/minio-login.png)

### Create a bucket

Open bucket interface

![Minio bucket UI](docs/minio-bucket-ui.png)

Create a bucket named **kittens-bucket**

![Minio new bucket](docs/minio-bucket-new.png)

### Change permissions

Select the bucket and change Access Policy from **Private** to **Public** in Summary tab.

![Minio change policy from private to public](docs/minio-public-access.png)

## Launch application

```bash
php -S 127.0.0.1:8000
```

Visit: http://127.0.0.1:8000
