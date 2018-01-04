#phalcon修改版
phalcon修改版并不是修改了phalcon框架扩展源码，而是在框架的基础上增加了更加灵活的配置文件以及自定义类，如：DB类，日志类等，在此感谢phalcon作者提供这么好的框架！
# phalcon
Phalcon 是开源、全功能栈、使用 C 扩展编写、针对高性能优化的 PHP 5 框架。 开发者不需要学习和使用 C 语言的功能， 因为所有的功能都以 PHP 类的方式暴露出来，可以直接使用。 Phalcon 也是松耦合的，可以根据项目的需要任意使用其他对象。
#PHP版本要求
PHP>=5.5<7.0
#安装phalcon
windows环境克隆项目后将phalcon扩展文件copy到PHP扩展目录并修改PHP配置文件，加载扩展文件
Linux环境参考官方安装教程，[phalcon  Linux安装教程](http://docs.iphalcon.cn/reference/install.html#linux-solaris "phalcon  Linux安装教程")


#自定义类
- **DB类**，比框架自带的类更加易用，且支持多数据库读写分离，配置简单
- **日志类**，使用phalcon修改版可以自动记录日志，日志包含请求日志和错误日志，当然这些都是可以在配置文件中关闭或者开启的
- **数据返回类**，调用方便，可以返回格式统一的json数据
- **Token类**，如果你拿来作为接口服务使用又需要登录接口，则可以使用token类，验证token需要调用方将下发的token放在header头里面，key为token，如：token:xxxxxx
- **CURL远程请求类**，可以方便的实现请求网络资源，可以自定义header头，调用方便
- **配置文件获取类**，由于DB类支持数据库读写分离，所以自定义了配置文件sconfig.php，该配置文件的配置项可以使用配置文件获取类（Config.php）读取
#类的使用
## DB类：
**支持的方法：**
```php
connect('conn1')     ##指定要使用哪一个数据库配置，非必须
master(true/false)   ##指定是否只从主库操作，非必须
table(tablename)    ##指定表名，不需要表前缀，必须
field(fieldlist)           ##指定要查询的字段列表，非必须
where()                   ##指定操作条件，部分操作必须调用
order(order-rule)     ##指定排序规则，非必须
limit()                      ##指定要查询的数据，常用于分页
getlastsql()             ##获取最后一次执行的SQL
find()            		##查询单条数据，返回一维数组或空数组
select()         		##查询多条数据，返回二维数组或空数组
getField(fieldname)  	##获取指定字段的值，返回字符串
add(data)                	##新增数据，返回新增数据的ID或false
save(data)              	##更新数据，需要调用where方法，返回true/false
delete()                   ##	删除数据，需要调用where方法，返回true/false
setInc(fieldname,number) 	##字段自增值，数值可以不传，默认为1
setDec(fieldname,number) ##字段自减值，数值可以不传，默认为1
query(sql)			##执行自定义SQL，支持增删改查（返回结果不一样）
```
**实例：**
查询：
```php
$this->db->table('table_name')->select();      ##简易查询
$this->db->connect('conn1')->master(true)->table('table_name')->field('field list')->where('where')->order('order_rule')->limit(1)->select(); ##完整查询
##查询还支持find()，getField()方法，作用请查看>>>支持的方法
```
新增：
```
$this->db->table('table_name')->add(data_array);
```
修改：
```
$this->db->table('table_name')->where('where')->save(data_array);
##修改还支持setInc(),setDec方法
```
删除：
```
$this->db->table('table_name')->where('where')->delete();
```
如果以上方法满足不了，还可以使用query方法执行SQL语句，query方法支持增删改查，且能根据不同的操作类型给出不同的返回值，删除/修改返回布尔值，新增成功返回新增的ID，失败返回false，查询返回二维数组，如：
```
$this->db->query('sql');
```
