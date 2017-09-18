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
