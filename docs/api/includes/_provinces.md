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


