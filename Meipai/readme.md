# 简易美拍系统 API

基本实现：用户关注（Followship)、发布状态、时间轴（Feed 流）




##  架构
###  关注系统

因为需要频繁使用到用户关注，因此直接将用户关系存放在 Redis，每个用户设有 following 和 follower，并且由 Redis counter 负责做关注者和关注的计数。加以 AOF 持久化避免数据丢失。

### 状态系统

仅有少数文字性内容，固化在 MySQL 中

#### Feed 流
##### 思路

user:timeline 中存储状态的ID

完整的状态存储在独立的实例中， LRU 进行管理

使用推拉结合的方式。通过时间作为推拉的区分，每个用户的 timeline cache 有一定的 ttl（使用MySQL或者通过一个redis tag进行维护，避免使用keys）。对于所有未过期的 user:timeline 使用推方式载入数据。已过期（ttl范围外未被推送）的用户在首次登录时使用拉方式，并且更新维护的ttl redis tag。

用户 timeline 还有一个计数器，用于统计用户关注的所有状态的总量。在总量少于200条并且已经获取所有条状态的状况下，可直接返回，减少数据库负载。

计划每个用户的 timeline 只缓存200条状态，因为一般来说用户翻到更久的状态的概率更低，如果往下翻阅超过 200 条将会直接渗透redis向 MySQL 索取并且不再缓存。

#### 状态发布

PublishQueue 队列：入库 -> 单条状态缓存 -> 推流队列（PushQueue) -> 杂项（推送之类的）-> 完结

#### 用户时间轴

##### 缓存
- 用户订阅：timeline:<userID>
- 用户自身时间轴：stats:<userID>

 存储结构：有序集合 List



## API
### 用户
- 用户 /profile/{id}：ID={id} 的用户资料
- 非缓存 /profileUC/{id}

### 关注 [/ship/]
- 随机关注 randfo/{id}： ID= {id} 的用户随机关注［debug］
- 关注 follow：user_id 用户ID，target_id 目标ID
- 取消关注 unfollow：user_id 用户ID，target_id 目标ID
- 关注中 following/{id}：ID={id} 的用户关注中列表
- 关注者 follower/{id}：ID={id} 的用户关注者列表

### 状态
- 发布 /publish：id 用户ID，content 内容

### 时间轴
- 总时间轴 /timeline：所有状态
- 非缓存总时间轴 /timelineUC
- 用户时间轴 /timeline/{id}：ID={id} 的用户时间轴
- 非缓存用户时间轴 /timelineUC/{id}

### 清除缓存 [/killer/]
- 时间轴缓存 timeline：清除所有时间轴缓存
- 状态缓存 stat：清除所有状态缓存




## 压测
