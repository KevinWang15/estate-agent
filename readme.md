# 说明
    
王轲的数据库期末Project
后端Repo，主要使用Laravel完成
    
前端Repo在 [https://github.com/KevinWang15/estate-agent-website](https://github.com/KevinWang15/estate-agent-website)

# 已经配置好的
 [http://139.196.50.217:7001/](http://139.196.50.217:7001/)

# 安装方法
请参考[Laravel的Docs](https://laravel.com/docs/5.2#installation)

    composer install            #安装依赖项
    chmod -R 777 storage/       #给storage目录权限
    cp .env.example .env        #创建配置文件
    vim .env                    #配置服务器信息
    php artisan key:generate    #生成Key

# Database Seeding
    
    #请注意：运行seed会清空已有数据
    php artisan db:seed
    php artisan seed:relations