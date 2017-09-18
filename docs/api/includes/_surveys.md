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

