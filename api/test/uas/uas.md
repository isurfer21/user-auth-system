# REST API

## Login to existing user account

### Query
```shell
curl -X POST \
  'http://localhost:8000/api/login' \
  --header 'Authorization: Bearer aWRlbnRpdHkgYW5kIGFjY2VzcyBtYW5hZ2VtZW50IHN5c3RlbQ==' \
  --header 'Content-Type: application/json' \
  --data-raw '{
    "username": "test1",
    "password": "p@sSc0d9"
  }'
```

### Response

##### Success
```
HTTP/1.1 200 OK
Host: localhost:8000
Date: Sun, 23 Apr 2023 18:04:32 GMT
Connection: close
X-Powered-By: PHP/8.2.1
Content-Length: 567
Content-type: text/html; charset=UTF-8

{
  "message": "User signed in",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpIiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2FwaSIsImlhdCI6MTY4MjI3MzA3MiwiZXhwIjoxNjgyMjc2NjcyLCJzdWIiOjF9.LmxCRoSazEVIc4New8fFzzIrTHudXCfh2tKtNihUigis5E8dGImZw3BNCe7HRGNKjEAn9esONzVua-bTsOECaWzCiEmQFm5ZUIFXguaR-QcczpyzSWfrFfNNIxh55Vovx3P6_lzVyZh7cltj-TCsmUh22Bz9JG6kbXUTB_BVjywWEL_84iZfJWAl90Ne0f10KucGg1Lc7uF0UQCiBSDRj5-uWG2AQwiFopQwkMu5MwUzsTqk2LxhFlmUnxR2J75XM_Npmatxq1lAwGOzyQXXyfgEbU6r-fXzC_kPtjaKRANUlv5OpqIYs0rzxGKy_rqAOWB8LEEiCXfP9UTd8fbTRw"
}
```

## Register new user account

### Query
```shell
curl -X POST \
  'http://localhost:8000/api/register' \
  --header 'Authorization: Bearer aWRlbnRpdHkgYW5kIGFjY2VzcyBtYW5hZ2VtZW50IHN5c3RlbQ==' \
  --header 'Content-Type: application/json' \
  --data-raw '{
    "username": "test1",
    "password": "p@sSc0d9",
    "email": "abc@test.com"
  }'
```

### Response

##### Success
```
HTTP/1.1 200 OK
Host: [::1]:8000
Date: Sun, 23 Apr 2023 18:09:34 GMT
Connection: close
X-Powered-By: PHP/8.2.1
Content-Length: 26
Content-type: text/html; charset=UTF-8

{
  "message": "User created"
}
```

##### Failure
```
HTTP/1.1 400 Bad Request
Host: localhost:8000
Date: Sun, 23 Apr 2023 18:04:59 GMT
Connection: close
X-Powered-By: PHP/8.2.1
Content-type: text/html; charset=UTF-8

{
  "message": "User already exists"
}
```

## Forgot password of existing user account

### Query
```shell
curl -X POST \
  'http://localhost:8000/api/forgot-password' \
  --header 'Authorization: Bearer aWRlbnRpdHkgYW5kIGFjY2VzcyBtYW5hZ2VtZW50IHN5c3RlbQ==' \
  --header 'Content-Type: application/json' \
  --data-raw '{
    "email": "isurfer21@gmail.com"
  }'
```

### Response

##### Failure
```
HTTP/1.1 500 Internal Server Error
Host: localhost:8000
Date: Sun, 23 Apr 2023 18:05:33 GMT
Connection: close
X-Powered-By: PHP/8.2.1
Content-type: text/html; charset=UTF-8

{
  "message": "Failed to send password reset email"
}
```