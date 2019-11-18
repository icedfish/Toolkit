#! /usr/bin/env python
from scapy.all import *

class TrafficMonitor:
    def __init__(self):
        self.sport_list = {}
        self.dport_list = {}
        self.step = 10000
        self.count = 0

    def mapping(self, pkt):
        if self.count%self.step == 1 :
            self.cal_traffic()

        if pkt[TCP].sport not in self.sport_list : 
            self.sport_list[pkt[TCP].sport] = 0

        if pkt[TCP].dport not in self.dport_list : 
            self.dport_list[pkt[TCP].dport] = 0
        
        self.sport_list[pkt[TCP].sport] += len(pkt)
        self.dport_list[pkt[TCP].dport] += len(pkt)
        self.count += 1

    def cal_traffic(self):
        print("source port list:\n", self.sport_list)
        print("dest port list:\n", self.dport_list)

handler = TrafficMonitor()

print("*** Start sniff on \n")
sniff(prn=handler.mapping, filter="tcp", store=0, count=10*handler.step)