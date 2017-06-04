create database if not exists p3_blog character set utf8 collate utf8_unicode_ci;
use p3_blog;

grant all privileges on p3_blog.* to 'root'@'localhost' identified by 'root';