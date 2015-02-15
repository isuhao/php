logfile=/srv/php/log/run.log
rm -rf $logfile
echo $(date) > $logfile
## 3 页面生成
## 3.1 生成公共页面
##/srv/php/timeexc.sh curl http://php.movie002.com/gendb/gen_share.php -o /srv/php/log/genshare.log
## 3.4 生成index
/srv/php/timeexc.sh curl http://php.movie002.com/genv/gen_index.php -o /srv/php/log/genindex.log
## 3.2 生成pages
/srv/php/timeexc.sh curl http://php.movie002.com/genv/gen_page.php?d=30 -o /srv/php/log/genpage.log
## 3.3 生成list
/srv/php/timeexc.sh curl http://php.movie002.com/genv/gen_list.php -o /srv/php/log/genlist.log
## 3.5 生成辅助页面，友情链接之类的
/srv/php/timeexc.sh curl http://php.movie002.com/genauthor/gen.php -o /srv/php/log/genauthor.log
## 3.6 生成sitemap
/srv/php/timeexc.sh curl http://php.movie002.com/genv/sitemap/gen.php -o /srv/php/log/gensitemap.log
