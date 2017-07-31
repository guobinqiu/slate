api位于src/Wenwen/FrontendBundle目录下

+ XXX/API (不依赖于特定版本的公共文件)
+ XXX/API/V1 (将来会有V2,V3,V4)

---

api按环境分开了，4种环境分别是

+ dev
	+ host: api.91jili.com.vag.91jili.com
	+ app_id: 19430461965976b27b6199c
	+ app_secret: 4da24648b8f1924148216cc8b49518e1
+ test
	+ host: api.91wenwen.com
	+ app_id: 19430461965976b27b6199c
	+ app_secret: 4da24648b8f1924148216cc8b49518e1
+ staging
	+ host: api.91jili.com
	+ app_id: 19430461965976b27b6199c
	+ app_secret: 4da24648b8f1924148216cc8b49518e1
+ production
	+ host: api.91wenwen.com
	+ app_id: 19430461965976b27b6199c
	+ app_secret: 4da24648b8f1924148216cc8b49518e1

以上内容均可在配置文件中随时修改（app上线前）。

---

运行条件

+ vagrant 访问时请配置host文件`XXX.XXX.XXX. XXX api.91jili.com.vag.91jili.com`
+ test 什么都不用做
+ staging 访问时需要Apache里增加一个虚拟机声明
+ production 访问时需要Apache里增加一个虚拟机声明

---

安全性方面，验证token有2个，app-access-token和user-access-token

+ app-access-token是每个请求都有的
+ user-access-token是需要读写用户相关信息时才会有

---

测试用例，包括用到的验证

+ CityControllerTest
	+ app-access-token
+ ProvinceControllerTest
	+ app-access-token
+ QQLoginControllerTest
	+ no authentication
+ SurveyControllerTest
	+ app-access-token
	+ user-access-token
+ UserControllerTest
	+ app-access-token

---

开发者帮助

+ 需要全局验证的，请继承TokenAuthenticatedFOSRestController类

+ 需要全局验证，同时需要验证登录的，请继承TokenAuthenticatedFOSRestController类，同时在需要的方法上添加@ValidateUserAccessToken注解

+ 不需要任何验证的，请继承FOSRestController类

+ 有限的请求头，跨域检查，允许值
	+ X-App-Access-Token
	+ X-Timestamp
	+ X-Nonce
	+ X-User-Access-Token
	
+ 允许的请求方法
	+ POST
	+ GET
	+ PUT
	+ DELETE
	+ PATCH

+ 有限的响应状态码，不要再多，多了乱
	+ const HTTP_OK = 200; // Success (GET, PUT, PATCH)
    + const HTTP_CREATED = 201; // Created (POST)
    + const HTTP_NO_CONTENT = 204; // No content (DELETE)
    + const HTTP_BAD_REQUEST = 400; // Validation error
    + const HTTP_UNAUTHORIZED = 401; // Authentication error
    + const HTTP_FORBIDDEN = 403; // Forbidden (user that only with given permissions can access)
    + const HTTP_NOT_FOUND = 404; // Not found
    + const HTTP_INTERNAL_SERVER_ERROR = 500; //Unexpected error

+ 已提供了未处理异常捕获机制，写不坏，好健壮
    
+ ApiUtil提供的一些在api开发过程中必然会用到的帮助方法	+ 常量定义
	+ 类型转换
	+ 响应格式化
