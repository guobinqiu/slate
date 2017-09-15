---
title: 91wenwen API Reference

language_tabs: # must be one of https://git.io/vQNgJ
  - shell

search: true
---

# Introduction

Welcome to the 91wenwen API! You can use our API to access 91wenwen API endpoints, which can get information on various cats, kittens, and breeds in our database.

We have language bindings in Shell! You can view code examples in the dark area to the right, and you can switch the programming language of the examples with the tabs in the top right.
     

# Authentication

> To authorize, use this code:

```shell
# With shell, you can just pass the correct header with each request  
curl "api_endpoint_here" \
    -H "X_Access_Token: MTk0MzA0NjE5NjU5NzZiMjdiNjE5OWM6NWJkZWVjMjZlM2FkNzliMzgzNGRiOTU0OGUxY2QyMDMxMmI4Njk3MTEyZGU4NjIwODI3OGMzMTYxOGM2Mjg5NA" \
    -H "X_Timestamp: 1503992897" \
    -H "X_Nonce: 82e9080ce6a23ce0710551c05b578a17"
```

> Make sure to replace `MTk0MzA0NjE5NjU5NzZiMjdiNjE5OWM6NWJkZWVjMjZlM2FkNzliMzgzNGRiOTU0OGUxY2QyMDMxMmI4Njk3MTEyZGU4NjIwODI3OGMzMTYxOGM2Mjg5NA` with your API key.

91wenwen uses API keys to allow access to the API.

91wenwen expects for the API key to be included in all API requests to the server in a header that looks like the following:

`X-Access-Token: MTk0MzA0NjE5NjU5NzZiMjdiNjE5OWM6NWJkZWVjMjZlM2FkNzliMzgzNGRiOTU0OGUxY2QyMDMxMmI4Njk3MTEyZGU4NjIwODI3OGMzMTYxOGM2Mjg5NA`

<aside class="notice">
You must replace <code>MTk0MzA0NjE5NjU5NzZiMjdiNjE5OWM6NWJkZWVjMjZlM2FkNzliMzgzNGRiOTU0OGUxY2QyMDMxMmI4Njk3MTEyZGU4NjIwODI3OGMzMTYxOGM2Mjg5NA</code> with your personal API key.
</aside>

# X_Access_Token

x_access_token = base64(appId + ':' + sha256(uppercase(method + uri + payload + timestamp + nonce), appSecret))

+ appId: ask administrator for it
+ appSecret: ask administrator for it
+ method: get, post, put, delete
+ uri: path + query string
+ payload: json data in a post request
+ timestamp: current system time (POSIX time)
+ nonce: unique random string (UUID)

# Provinces

## Get All Provinces

```shell
curl "https://www.91wenwen.net/v1/provinces" \
    -X GET \
    -H "X_Access_Token: MTk0MzA0NjE5NjU5NzZiMjdiNjE5OWM6NWJkZWVjMjZlM2FkNzliMzgzNGRiOTU0OGUxY2QyMDMxMmI4Njk3MTEyZGU4NjIwODI3OGMzMTYxOGM2Mjg5NA" \
    -H "X_Timestamp: 1503992897" \
    -H "X_Nonce: 82e9080ce6a23ce0710551c05b578a17"
```

> The above command returns JSON structured like this:

```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "province_name": "直辖市"
    },
    {
      "id": 2,
      "province_name": "河北省"
    },
    {
      "id": 3,
      "province_name": "山西省"
    },
    {
      "id": 4,
      "province_name": "内蒙古自治区"
    },
    {
      "id": 5,
      "province_name": "辽宁省"
    },
    {
      "id": 6,
      "province_name": "吉林省"
    },
    {
      "id": 7,
      "province_name": "黑龙江省"
    },
    {
      "id": 8,
      "province_name": "江苏省"
    },
    {
      "id": 9,
      "province_name": "浙江省"
    },
    {
      "id": 10,
      "province_name": "安徽省"
    },
    {
      "id": 11,
      "province_name": "福建省"
    },
    {
      "id": 12,
      "province_name": "江西省"
    },
    {
      "id": 13,
      "province_name": "山东省"
    },
    {
      "id": 14,
      "province_name": "河南省"
    },
    {
      "id": 15,
      "province_name": "湖北省"
    },
    {
      "id": 16,
      "province_name": "湖南省"
    },
    {
      "id": 17,
      "province_name": "广东省"
    },
    {
      "id": 18,
      "province_name": "广西壮族自治区"
    },
    {
      "id": 19,
      "province_name": "海南省"
    },
    {
      "id": 20,
      "province_name": "四川省"
    },
    {
      "id": 21,
      "province_name": "贵州省"
    },
    {
      "id": 22,
      "province_name": "云南省"
    },
    {
      "id": 23,
      "province_name": "西藏自治区"
    },
    {
      "id": 24,
      "province_name": "陕西省"
    },
    {
      "id": 25,
      "province_name": "甘肃省"
    },
    {
      "id": 26,
      "province_name": "青海省"
    },
    {
      "id": 27,
      "province_name": "宁夏回族自治区"
    },
    {
      "id": 28,
      "province_name": "新疆维吾尔自治区"
    },
    {
      "id": 29,
      "province_name": "香港特别行政区"
    },
    {
      "id": 30,
      "province_name": "澳门特别行政区"
    },
    {
      "id": 31,
      "province_name": "台湾省"
    },
    {
      "id": 32,
      "province_name": "其他"
    }
  ]
}
```

This endpoint retrieves all provinces.

### HTTP Request

`GET https://www.91wenwen.net/v1/provinces`

# Cities

## Get All Cities

```shell
curl "https://www.91wenwen.net/v1/provinces/1/cities" \
    -X GET \
    -H "X_Access_Token: MTk0MzA0NjE5NjU5NzZiMjdiNjE5OWM6NmUzNmY5YzBmOGY2NmEyMDA3ZTM4ZDk2MTdhZGFjYjgyMzU0MTY1ZTJiZDU2MDA2NTlmYzUwNDlhZjViZjg0OA" \
    -H "X_Timestamp: 1503994574" \
    -H "X_Nonce: abb3736020d220e1e871c7fa1891d14f" 
```

> The above command returns JSON structured like this:

```json
{
  "status": "success",
  "data": [
    {
      "cityName": "上海市",
      "cityId": 2
    },
    {
      "cityName": "北京市",
      "cityId": 1
    },
    {
      "cityName": "天津市",
      "cityId": 3
    },
    {
      "cityName": "重庆市",
      "cityId": 4
    }
  ]
}
```

This endpoint retrieves all cities of a province.

### HTTP Request

`GET https://www.91wenwen.net/v1/provinces/1/cities`

### URL Parameters

Parameter | Required | Description
--------- | -------- | -----------
province_id | true | The ID of the province


# Users

## Login

```shell
curl \
    -X POST \ 
    -H "X_Access_Token: MTk0MzA0NjE5NjU5NzZiMjdiNjE5OWM6ZDgzYjE5OGIxNGRlYWRlYjgzZDlmMDEwYzRhZjc0Yzk2YTdkMzdmOTMwYjVkNjM3OTQzMjA3Mjk4MGZkYzhiZA" \
    -H "X_Timestamp: 1504001050" -H "X_Nonce: 15a204ce6352f8aaaae28ad2919808bc" \
    -H "X_Login_Token: 6448377ef8ac21a868ad618f6e17b886" \
    -H "Content-Type: application/json" \
    -d '{"login":{"username":"13916122915","password":"111111"}}' \
    "https://www.91wenwen.net/v1/users/login"
```

> The above command returns JSON structured like this:

```json
{
  "status": "success",
  "data": {
    "user": {
      "is_email_confirmed": 0,
      "reward_multiple": 1,
      "points": 0,
      "password_choice": 1,
      "prize_tickets": [],
      "user_sign_in_details": [],
      "points_cost": 0,
      "points_expense": 0,
      "complete_n": 0,
      "screenout_n": 0,
      "quotafull_n": 0
    },
    "loginToken": "81748cfa004e535321837863e8600b8b"
  }
}
```

This endpoint checks username and password and returns the corresponding user information along with an unfixed login token. App side should store this login token.

### HTTP Request

`POST https://www.91wenwen.net/v1/users/login`

### URL Parameters

Parameter | Required | Description
--------- | -------- | -----------
openid | true | qq user openid
access_token | true | qq login access token

## Logout

## QQ Login

```shell
curl \
    -X POST \
    "https://www.91wenwen.net/v1/qq/login?openid=123&access_token=abc"
```

> The above command returns JSON structured like this:

```json
{
  "status": "success",
  "data": {
    "openid": "123",
    "access_token": "abc"
  }
}
```

This endpoint retrieves all kittens.

### HTTP Request

`GET https://www.91wenwen.net/v1/provinces/1/cities`

### URL Parameters

Parameter | Required | Description
--------- | -------- | -----------
openid | true | qq user openid
access_token | true | qq login access token

## Weixin Login

## Weibo Login

# Surveys

## Get All Surveys

```shell
curl \
    -X GET \
    -H "X_Access_Token: MTk0MzA0NjE5NjU5NzZiMjdiNjE5OWM6MGVkNGMzNGM4Mjg4NTRjZjg3M2RhNWRjNjU0MWNlMzdlMWI3NDM5YTk2MDUwOWVkNmU3NjE1NGUwODQ3NmM5Zg" \
    -H "X_Timestamp: 1504000042" \
    -H "X_Nonce: cad9301fe29a52a7673b5b1da57c6a98" \
    -H "X_Login_Token: cf12d67b28a29ddffa2790c4e759106a" \
    "https://www.91wenwen.net/v1/surveys"
```

> The above command returns JSON structured like this:

```json
{
  "status": "success",
  "data": [
    "survey1",
    "survey2",
    "survey3",
    "survey4",
    "survey5"
  ]
}
```

This endpoint retrieves all kittens.

### HTTP Request

`GET https://www.91wenwen.net/v1/surveys`



