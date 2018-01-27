## 介绍

配合`Laravel`和`GitBook`的`Markdown`生成器

## 安装

### 依赖库
`composer require temporaries/document`

### 发布配置
`php artisan vendor:publish --provider="Temporaries\Document\DocumentServiceProvider" --tag="config"`

### 生成 Markdown
`php artisan document:generate`

### 注解
可以在模型里添加注解

- DocName 别名
- DocRelated 关联的模型

例:

在 Activity 模型里添加
```
/**
 * @DocName=活动
 * @DocRelated=Member
 */
```

生成的markdown
# Activity 活动

Parameter                       | Type            | Length  | Comment
:------------------------------ | :-------------- | :------ | :----------
id                              | Integer         |         | 
name                            | String          | 64      | 名称
temple_id                       | Integer         |         | 寺庙ID
organizer                       | String          | 128     | 主办方
thumb                           | String          | 255     | 缩略图
content                         | Text            |         | 内容
comments                        | Integer         |         | 评论数
recommend                       | Integer         |         | 推荐：1是 0 否
price                           | Decimal         |         | 价格
limit_number                    | Integer         |         | 最多人数
registered_number               | Integer         |         | 已报名的人数`
start_at                        | DateTime        |         | 开始时间
end_at                          | DateTime        |         | 结束时间
created_at                      | DateTime        |         | 
updated_at                      | DateTime        |         | 
deleted_at                      | DateTime        |         | 
[Member](Member.md)             | Object          |         | 用户


