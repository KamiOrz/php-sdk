
0.2.5 发布日期：2016-02-01
----
* 支持手机号码和密码登录
* 修复查询 `_User` 未传递 sessionToken 导致查询失败

0.2.4 发布日期：2016-01-26
----

* 修复短信验证码登录后 current user 为空的问题

0.2.3 发布日期：2016-01-12
----

* 修复 getCurrentUser 循环调用问题 close #48

0.2.2 发布日期：2016-01-06
----

* 修复保存关联文件的对象时的语法错误 close #46

0.2.1 发布日期：2015-12-31
----

* 修复类型不安全的字符串比较 close #43

0.2.0 发布日期：2015-11-13
----
* 支持 CQL 查询：LeanQuery::doCloudQuery
* 支持发送 Push 推送消息 (#23)
* 支持 GeoPoint 类型及地理位置查询 (#25)
* 支持 Role 和 ACL 权限管理 (#19)
* 修复: `LeanClient::useMasterKey()` 没有生效的问题 #21
* `LeanClient::decode()` 添加第二个参数以识别 ACL
  (**与上一版本不兼容**)

0.1.0 发布日期: 2015-10-30
----

