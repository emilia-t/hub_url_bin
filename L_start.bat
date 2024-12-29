docker build -t hub_url_bin .

docker run -d --name hub_url_bin -p 80:80 -p 443:443 -p 20000:20000 -p 30000:30000 -v D:/hubBox/hub_url_bin:/var/www hub_url_bin
