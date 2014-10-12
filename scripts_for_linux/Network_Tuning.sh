#!/bin/bash  
# Enable RPS (Receive Packet Steering)  

#from http://hi.baidu.com/higkoo/item/9f2b50d9adb177cd1a72b4a8

      
rfc=4096  
cc=$(grep -c processor /proc/cpuinfo)  
rsfe=$(echo $cc*$rfc | bc)  
sysctl -w net.core.rps_sock_flow_entries=$rsfe  
for fileRps in $(ls /sys/class/net/eth*/queues/rx-*/rps_cpus)  
do
    echo fff > $fileRps  
done
      
for fileRfc in $(ls /sys/class/net/eth*/queues/rx-*/rps_flow_cnt)  
do
    echo $rfc > $fileRfc  
done
      
#tail /sys/class/net/eth*/queues/rx-*/{rps_cpus,rps_flow_cnt}


#https://www.kernel.org/pub/linux/kernel/people/akpm/patches/2.6/2.6.13/2.6.13-mm1/broken-out/linus.patch
sysv-rc-conf irqbalance off

# 关闭网卡LRO和GRO   
ethtool -K eth0 gro off   
ethtool -K eth0 lro off

# 禁用ARP，增大backlog并发数   
sysctl -w net.ipv4.conf.all.arp_ignore=1
sysctl -w net.ipv4.conf.all.arp_announce=2
sysctl -w net.core.netdev_max_backlog=500000
