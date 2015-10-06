# SynKnot
SynKnot utility for DNS and PTR synchronization with Knot DNS server

Prerequisites:
 - own Knot DNS server (https://www.knot-dns.cz/)
 - own database of DNS and PTR records (or other kind of storage)
 - PHP-cli and GIT support on the DNS server

How to deploy SynKnot:
 - ssh to your DNS server
 - ```bash cd /opt/```
 - **git clone https://github.com/besthostingcz/synknot.git**
 - **cd /opt/synknot/**
 - **cp ./config.ini.dist ./config.ini**
 - change your preferences in the config.ini file
 - **cd ./src/SynKnot/Application/Adapters/**
 - **cp ./TestDNSRecordAdapter.php ./MyOwnDNSRecordAdapter.php**
 - **cp ./TestPTRAdapter.php ./MyOwnPTRAdapter.php**
 - edit the adapters, that they can return lists of DNS / PTR
 - link your new adapters at config.ini
 - **cd /opt/synknot/**"
 - run SynKnot synchronization: **php ./dns-sync.php dns-sync:reload**

For more commands, you can use standard Symfony console "php /opt/synknot/dns-sync.php"

More infomation about this project could be found at http://synknot.cz/. Don't hesitate to ask :)
