# Review service

This is pet project service used for processing movies reviews.

For the test cases, service generates single movie and episode on startup

# Installation

Run
```shell
docker-compose up --build
```
Docker will install all needed dependencies and PostgreSQL database, execute tests and launch development server, which could be accessed by 8000 port

# Testing

Publish new review:

```shell
curl -X POST --location 'http://localhost:8000/review' \
--header 'Content-Type: application/json' \
--data '{
    "score": 1,
    "reviewText": "This is the worst thing I have ever seen",
    "episodeId": 1
}'
```

Get episode summary:

```shell
curl --location 'http://localhost:8000/summary/1'
```
