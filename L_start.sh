#！/bin/bash
sudo docker build -t hub_url_bin .

sudo docker run -d --name hub_url_bin -p 80:80 -p 20000:20000 -v D:/hubBox/hub_url_bin:/var/www hub_url_bin
