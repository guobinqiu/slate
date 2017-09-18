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
