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

## X_Access_Token

x_access_token = base64(appId + ':' + sha256(uppercase(method + uri + payload + timestamp + nonce), appSecret))

+ appId: ask administrator for it
+ appSecret: ask administrator for it
+ method: get, post, put, delete
+ uri: path + query string
+ payload: json data in a post request
+ timestamp: current system time (POSIX time)
+ nonce: unique random string (UUID)