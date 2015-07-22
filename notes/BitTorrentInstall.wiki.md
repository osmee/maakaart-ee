# Summary

I use two CLI tools here to load and serve latest OSM planet from http://osm-torrent.torres.voyager.hr/ - rtorrent and rssdler. It is used in Ubuntu server, for desktop you have torrent GUI clients which just take RSS input easily.

# RSSdler
  1. Install soft. Note: _/suur_ is here special partition which accommodates easily hundreds of GB data)
```
sudo apt-get install python-feedparser
sudo apt-get install python-mechanize
mkdir /opt/rssdler
cd /opt/rssdler
wget http://rssdler.googlecode.com/files/rssdler-0.4.2.tar.gz
tar xvzf rssdler-0.4.2.tar.gz
cd rssdler042/
sudo python setup.py install
mkdir /suur/planet

```
  1. minimal configuration for OSM planet looks like following, in file **/opt/rssdler/rssdler042/config**:
```
[global]
downloadDir = /suur/planet/
workingDir = /opt/rssdler/
minSize = 0
log = 1
logFile = /var/log/rssdlerlog.txt
scanMins = 5
sleepTime = 5
runOnce = false
urllib = True

###################

[site1]
link = http://osm-torrent.torres.voyager.hr/files/rss.xml
```
  1. Start RSSdler as daemon. It should now reload RSS in every 5 minutes and creates new .torrent files to my download directory
```
rssdler -c /opt/rssdler/rssdler042/config -d
```

# Rtorrent
  1. Install and configure rtorrent
```
sudo apt-get install rtorrent
```
  1. Configure rtorrent to follow local .torrent files: create **~/.rtorrent.rc** file:
```
# Default directory to save the downloaded torrents.
directory = /suur/planet

# Default session directory. Make sure you don't run multiple instance
# of rtorrent using the same session directory. Perhaps using a
# relative path?
session = /home/jaakl/.torrents/session

# Watch a directory for new torrents, and stop those that have been
# deleted.
schedule = watch_directory,5,5,load_start=/suur/planet/*.torrent
```
  1. At last, start and keep running rtorrent (I use it in screen/byobu). I use it with specific incoming port as I have just one forwarded in the firewall
```
rtorrent -p 6881-6881
```

# TODO
  1. Older downloaded and .torrent files should be removed. Current system will download initially about 200GB and it is growing every week by 35GB or so.