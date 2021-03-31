<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/3/26
 * Time: 2:29 下午
 */


namespace App\Http\Controllers;


use App\Http\Controllers\Core\Controller;
use Illuminate\Support\Facades\Redis;

class RedisController extends Controller
{
    //protected $redis;

    public function __construct()
    {
        //$this->redis = Redis::connection();
    }

    public function string()
    {
        /*
         SET                    将字符串值 value 关联到 key (会覆盖)
         SETNX                  只在键 key 不存在的情况下， 将键 key 的值设置为 value
         SETEX                  感觉有些鸡肋
         PSETEX                 和 SETEX 命令相似， 但它以毫秒为单位设置 key 的生存时间
         GET                    返回与键 key 相关联的字符串值
         GETSET                 将键 key 的值设为 value ， 并返回键 key 在被设置之前的旧值
         STRLEN                 返回键 key 储存的字符串值的长度
         APPEND                 键 key 已经存在并且它的值是一个字符串， 则拼接。 否则新建
         SETRANGE               从偏移量 offset 开始， 用 value 参数覆写(overwrite)键 key 储存的字符串值
         GETRANGE               返回键 key 储存的字符串值的指定部分
         INCR                   为键 key 储存的数字值加上一
         INCRBY                 为键 key 储存的数字值加上增量 increment
         INCRBYFLOAT            为键 key 储存的值加上浮点数增量 increment
         DECR                   为键 key 储存的数字值减去一
         DECRBY                 将键 key 储存的整数值减去减量 decrement
         MSET                   同时为多个键设置值
         MSETNX                 当且仅当所有给定键 都 不存在时， 为所有给定键设置值
         MGET                   返回给定的一个或多个字符串键的值
        */

        //$expireResolution: EX seconds, PX milliseconds  $flag: NX XX
        //Redis::set('zzz', '333', 'EX', 60, 'NX');
        //Redis::setnx('zzz', '222');
        //Redis::setex('zzz', 10, '111');
        //Redis::incrby('abc', 3);
        //$zzz = Redis::get('abc');
        //$exist = Redis::exists('zzz');
        //$res = Redis::del(['zzz']);
        //Redis::mset([
        //    'foo1' => '1',
        //    'foo2' => 2,
        //    'ccc' => 3
        //]);
        //Redis::incrby('aaa', 2);
        //$zzz = Redis::mget(['aaa', 'bbb', 'ccc', 'ddd']);
        //$zzz = Redis::keys('foo*');
        //Redis::renamenx('foo1', 'foo3');

        //var_dump($zzz);
    }

    public function set()
    {
        /*
         SADD                   将一个或多个 member 元素加入到集合 key 当中，已经存在于集合的 member 元素将被忽略
         SISMEMBER              判断 member 元素是否集合 key 的成员
         SPOP                   移除并返回集合中的一个随机元素
         SRANDMEMBER            返回集合中的一个随机元素
         SREM                   移除集合 key 中的一个或多个 member 元素，不存在的 member 元素会被忽略
         SMOVE                  将 member 元素从 source 集合移动到 destination 集合
         SCARD                  返回集合 key 的基数(集合中元素的数量)
         SMEMBERS               返回集合 key 中的所有成员，不存在的 key 被视为空集合
         SSCAN                  迭代集合键中的元素 (keys的高级使用)  !!!   http://redisdoc.com/database/scan.html#scan
         SINTER                 返回所有给定集合的交集
         SINTERSTORE            返回交集并存储至新集合
         SUNION                 返回所有给定集合的并集
         SUNIONSTORE            返回并集并存储至新集合
         SDIFF                  返回所有给定集合的差集
         SDIFFSTORE             返回差集并存储至新集合
        */

        //Redis::sadd('zsSet', 'aaa', 'bbb');
        //Redis::spop('zsSet');
        //Redis::srandmember('zsSet');
        //Redis::smove('zsSet', 'zzz', 'bbb');
        //$zzz = Redis::sscan('zsSet', 1, ['match' => "a*", 'count' => '2']);
        //$zzz = Redis::sinter(['zsSet', 'aaa']);

        //var_dump($zzz);
    }

    public function list()
    {
        /*
         LPUSH                  将一个或多个值 value 插入到列表 key 的表头
         LPUSHX                 将值 value 插入到列表 key 的表头，当且仅当 key 存在并且是一个列表
         RPUSH                  将一个或多个值 value 插入到列表 key 的表尾(最右边)
         RPUSHX                 将值 value 插入到列表 key 的表尾，当且仅当 key 存在并且是一个列表
         LPOP                   移除并返回列表 key 的头元素
         RPOP                   移除并返回列表 key 的尾元素
         RPOPLPUSH              弹尾插头
         LREM                   根据参数 count 的值，移除列表中与参数 value 相等的元素
         LLEN                   返回列表 key 的长度
         LINDEX                 返回列表 key 中，下标为 index 的元素
         LINSERT                将值 value 插入到列表 key 当中，位于值 pivot 之前或之后
         LSET                   将列表 key 下标为 index 的元素的值设置为 value
         LRANGE                 返回列表 key 中指定区间内的元素，区间以偏移量 start 和 stop 指定
         LTRIM                  只保留指定区间内的元素
         BLPOP                  LPOP key 命令的阻塞版本
         BRPOP                  RPOP key 命令的阻塞版本
         BRPOPLPUSH             RPOPLPUSH key 命令的阻塞版本
        */

        //Redis::rpush('zsList', json_encode(['a' => 1]));
        //Redis::lpush('zsList', 'zzz');
        //$length = Redis::llen('zsList');
        //$zzz = Redis::lrange('zsList', 0, -1);
        //$zzz = Redis::lindex('zsList', 0);
        //Redis::lset('zsList', 1, '111');
        //Redis::lrem('zsList', 2, 'Array');
        //Redis::ltrim('zsList', 0, 1);
        //Redis::linsert('zsList', 'after', '111', 'haha');
        //Redis::lpop('zsList');
        //Redis::rpop('zsList');
        //Redis::rpoplpush('zsList', 'zsList');
        //$zzz = Redis::blpop(['zsList', 'zzzList'], 10);

        //var_dump($zzz);
    }

    public function sortedSet()
    {
        /*
         ZADD                   将一个或多个 member 元素及其 score 值加入到有序集 key 当中
         ZSCORE                 返回有序集 key 中，成员 member 的 score 值
         ZINCRBY                为有序集 key 的成员 member 的 score 值加上增量 increment
         ZCARD                  返回有序集 key 的基数
         ZCOUNT                 返回有序集 key 中， score 值在 min 和 max 之间(默认包括 score 值等于 min 或 max )的成员的数量
         ZRANGE                 返回有序集 key 中，指定区间内的成员(正序)
         ZREVRANGE              返回有序集 key 中，指定区间内的成员(倒序)
         ZRANGEBYSCORE          返回有序集 key 中，所有 score 值介于 min 和 max 之间(包括等于 min 或 max )的成员(正序)
         ZREVRANGEBYSCORE       返回有序集 key 中，所有 score 值介于 min 和 max 之间(包括等于 min 或 max )的成员(倒序)
         ZRANK                  返回有序集 key 中成员 member 的排名。其中有序集成员按 score 值递增(从小到大)顺序排列
         ZREVRANK               返回有序集 key 中成员 member 的排名。(倒序)
         ZREM                   移除有序集 key 中的一个或多个成员，不存在的成员将被忽略
         ZREMRANGEBYRANK        移除有序集 key 中，指定排名(rank)区间内的所有成员
         ZREMRANGEBYSCORE       移除有序集 key 中，所有 score 值介于 min 和 max 之间(包括等于 min 或 max )的成员
         ZRANGEBYLEX            返回给定的有序集合键 key 中， 值介于 min 和 max 之间的成员
         ZLEXCOUNT              返回该集合中， 成员介于 min 和 max 范围内的元素数量
         ZREMRANGEBYLEX         移除该集合中， 成员介于 min 和 max 范围内的所有元素
         ZSCAN                  http://redisdoc.com/database/scan.html#scan
         ZUNIONSTORE            计算给定的一个或多个有序集的并集，并将该并集(结果集)储存到 destination
         ZINTERSTORE            计算给定的一个或多个有序集的交集，并将该交集(结果集)储存到 destination
        */


    }

    public function hash()
    {
        /*
         HSET                   将哈希表 hash 中域 field 的值设置为 value
         HSETNX                 当且仅当域 field 尚未存在于哈希表的情况下， 将它的值设置为 value
         HGET                   返回哈希表中给定域的值
         HEXISTS                检查给定域 field 是否存在于哈希表 hash 当中
         HDEL                   删除哈希表 key 中的一个或多个指定域，不存在的域将被忽略
         HLEN                   返回哈希表 key 中域的数量
         HSTRLEN                返回哈希表 key 中， 与给定域 field 相关联的值的字符串长度（string length）
         HINCRBY                为哈希表 key 中的域 field 的值加上增量 increment
         HINCRBYFLOAT           为哈希表 key 中的域 field 加上浮点数增量 increment
         HMSET                  同时将多个 field-value (域-值)对设置到哈希表 key 中
         HMGET                  返回哈希表 key 中，一个或多个给定域的值
         HKEYS                  返回哈希表 key 中的所有域
         HVALS                  返回哈希表 key 中所有域的值
         HGETALL                返回哈希表 key 中，所有的域和值
         HSCAN                  http://redisdoc.com/database/scan.html#scan
        */


    }

    public function hyperLogLog()
    {
        /*
         * 通过 HyperLogLog 数据结构， 用户可以使用少量固定大小的内存， 来储存集合中的唯一元素
         * （每个 HyperLogLog 只需使用 12k 字节内存，以及几个字节的内存来储存键本身）
         PFADD                  将任意数量的元素添加到指定的 HyperLogLog 里面
         PFCOUNT                返回储存在给定键的 HyperLogLog 的近似基数， 如果键不存在， 那么返回 0
         PFMERGE                将多个 HyperLogLog 合并（merge）为一个 HyperLogLog
        */


    }

    public function geo()
    {
        /*
         GEOADD                 将给定的空间元素（纬度、经度、名字）添加到指定的键里面
         GEOPOS                 从键里面返回所有给定位置元素的位置（经度和纬度）
         GEODIST                返回两个给定位置之间的距离
         GEORADIUS              以给定的经纬度为中心， 返回键包含的位置元素当中， 与中心的距离不超过给定最大距离的所有位置元素
         GEORADIUSBYMEMBER      以给定的位置元素为中心， ......
         GEOHASH                返回一个或多个位置元素的 Geohash 表示
        */


    }

    public function bit()
    {
        /*
         SETBIT                 对 key 所储存的字符串值，设置或清除指定偏移量上的位(bit)
         GETBIT                 对 key 所储存的字符串值，获取指定偏移量上的位(bit)
         BITCOUNT               计算给定字符串中，被设置为 1 的比特位的数量
         BITPOS                 返回位图中第一个值为 bit 的二进制位的位置
         BITOP                  对一个或多个保存二进制位的字符串 key 进行位元操作，并将结果保存到 destkey 上
         BITFIELD               http://redisdoc.com/bitmap/bitfield.html
        */


    }

    public function database()
    {
        /*
         EXISTS                 检查给定 key 是否存在
         TYPE                   返回 key 所储存的值的类型 1:string  2:set  3:list  4:zSet  5:hash
         RENAME                 将 key 改名为 newkey
         RENAMENX               当且仅当 newkey 不存在时，将 key 改名为 newkey
         MOVE                   将当前数据库的 key 移动到给定的数据库 db 当中
         DEL                    删除给定的一个或多个 key
         RANDOMKEY              从当前数据库中随机返回(不删除)一个 key
         DBSIZE                 返回当前数据库的 key 的数量
         KEYS                   查找所有符合给定模式 pattern 的 key
         SCAN                   类似于 php 的生成器 http://redisdoc.com/database/scan.html
         SORT                   返回或保存给定列表、集合、有序集合 key 中经过排序的元素
         FLUSHDB                清空当前数据库中的所有 key
         FLUSHALL               清空整个 Redis 服务器的数据(删除所有数据库的所有 key )
         SELECT                 切换到指定的数据库，数据库索引号 index 用数字值指定，以 0 作为起始索引值
         SWAPDB                 对换指定的两个数据库， 使得两个数据库的数据立即互换
        */


    }

    public function expire()
    {
        /*
         EXPIRE                 为给定 key 设置生存时间，当 key 过期时(生存时间为 0 秒)，它会被自动删除
         EXPIREAT               为给定 key 设置生存时间(时间戳)，当 key 过期时(生存时间为 0 )，它会被自动删除
         TTL                    以秒为单位，返回给定 key 的剩余生存时间(TTL, time to live)
         PERSIST                移除给定 key 的生存时间，将这个 key 修改为永不过期的 key
         PEXPIRE                和 EXPIRE 命令的作用类似，但是它以毫秒为单位
         PEXPIREAT              和 expireat 命令类似，但它以毫秒为单位
         PTTL                   和 TTL 命令类似，但它以毫秒为单位
        */


    }

    public function transaction()
    {
        /*
         MULTI                  标记一个事务块的开始
         EXEC                   执行所有事务块内的命令
         DISCARD                取消事务，放弃执行事务块内的所有命令
         WATCH                  监视一个(或多个) key ，如果在事务执行之前这个(或这些) key 被其他命令所改动，那么事务将被打断
         UNWATCH                取消 WATCH 命令对所有 key 的监视
        */


    }

    public function lua()
    {

    }

    public function persistence()
    {
        /*
         SAVE                   SAVE 命令执行一个同步保存操作，将当前 Redis 实例的所有数据快照(snapshot)以 RDB 文件的形式保存到硬盘
         BGSAVE                 在后台异步(Asynchronously)保存当前数据库的数据到磁盘
         BGREWRITEAOF           执行一个 AOF 文件 重写操作。重写会创建一个当前 AOF 文件的体积优化版本
         LASTSAVE               返回最近一次 Redis 成功将数据保存到磁盘上的时间，以 UNIX 时间戳格式表示
        */


    }

    public function pubsub()
    {
        /*
         PUBLISH                将信息 message 发送到指定的频道 channel
         SUBSCRIBE              订阅给定的一个或多个频道的信息
         PSUBSCRIBE             订阅一个或多个符合给定模式的频道
         UNSUBSCRIBE            客户端退订指定的频道
         PUNSUBSCRIBE           客户端退订符合给定模式的频道
         PUBSUB
             PUBSUB CHANNELS            列出当前的活跃频道
             PUBSUB NUMSUB              返回给定频道的订阅者数量， 订阅模式的客户端不计算在内
             PUBSUB NUMPAT              返回订阅模式的数量
        */


    }

    public function replication()
    {
        /*
         http://redis.io/topics/replication

         SLAVEOF                SLAVEOF 命令用于在 Redis 运行时动态地修改复制(replication)功能的行为
         ROLE                   返回实例在复制中担任的角色， 这个角色可以是 master 、 slave 或者 sentinel
        */


    }

    public function cliAndSer()
    {
        /*
         AUTH                   开启了密码保护的话， 使用 AUTH 命令解锁
         QUIT                   请求服务器关闭与当前客户端的连接
         INFO                   http://redisdoc.com/client_and_server/info.html
             server
             clients
             memory
             persistence
             stats
             replication
             cpu
             modules
             cluster
             keyspace
         SHUTDOWN               1、停止所有客户端 2、如果有至少一个保存点在等待，执行 SAVE 命令 3、如果 AOF 选项被打开，更新 AOF 文件 4、关闭 redis 服务器(server)
         TIME                   返回当前服务器时间
         CLIENT GETNAME         返回 CLIENT SETNAME 命令为连接设置的名字
         CLIENT KILL            关闭地址为 ip:port 的客户端
         CLIENT LIST            以人类可读的格式，返回所有连接到服务器的客户端信息和统计数据
         CLIENT SETNAME         为当前连接分配一个名字
        */


    }

    public function config()
    {
        /*
         CONFIG SET             CONFIG SET 命令可以动态地调整 Redis 服务器的配置(configuration)而无须重启
         CONFIG GET             CONFIG GET 命令用于取得运行中的 Redis 服务器的配置参数
         CONFIG RESETSTAT       重置 INFO 命令中的某些统计数据
         CONFIG REWRITE         CONFIG REWRITE 命令对启动 Redis 服务器时所指定的 redis.conf 文件进行改写
        */


    }

    public function debug()
    {
        /*
         PING                   使用客户端向 Redis 服务器发送一个 PING ，如果服务器运作正常的话，会返回一个 PONG
         ECHO                   打印一个特定的信息 message ，测试时使用
         OBJECT                 OBJECT 命令允许从内部察看给定 key 的 Redis 对象
             OBJECT REFCOUNT        返回给定 key 引用所储存的值的次数
             OBJECT ENCODING        返回给定 key 锁储存的值所使用的内部表示(representation)
             OBJECT IDLETIME        返回给定 key 自储存以来的空闲时间(idle， 没有被读取也没有被写入)，以秒为单位
         SLOWLOG                Slow log 是 Redis 用来记录查询执行时间的日志系统
         MONITOR                实时打印出 Redis 服务器接收到的命令，调试用
         DEBUG OBJECT           调试命令，它不应被客户端所使用
         DEBUG SEGFAULT         执行一个不合法的内存访问从而让 Redis 崩溃，仅在开发时用于 BUG 模拟
        */


    }

    public function internalCommand()
    {
        /*
         MIGRATE                将 key 原子性地从当前实例传送到目标实例的指定数据库上，一旦传送成功， key 保证会出现在目标实例上，而当前实例上的 key 会被删除
         DUMP                   序列化给定 key ，并返回被序列化的值
         RESTORE                反序列化给定的序列化值，并将它和给定的 key 关联
         SYNC
         PSYNC
        */


    }

    public function topic()
    {
        /*
         http://redisdoc.com/topic/index.html
        */
    }

}
