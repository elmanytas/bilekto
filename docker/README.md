## Docker image

Example:
```
docker run --name bilekto -d -p 8080:8080 \
            -e GALLERY_NAME='My cool gallery' \
            -v /tmp/clasificando:/var/www/html/images
            elmanytas/bilekto:latest
```

In this moment configures these environment vars:
- *GALLERY_NAME*:
- *ALLOW_DOWNLOADS*:
- *ESTIMATE_ARCHIVE_SIZE*:
- *CACHING*:
- *THUMB_FORMAT*:

Take a look to config.ini.dist .
