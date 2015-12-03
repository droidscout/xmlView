#!/usr/bin/python3

from datetime import date
from datetime import timedelta
from datetime import datetime
#import datetime
import sys
import time
import subprocess
import xml.etree.cElementTree as ElTree
import urllib3


prodURL = "http://172.29.139.157/L5SBESrv/GetBetData.ashx"
testURL = "http://172.29.139.199/L5SBESrv/GetBetData.ashx"
SLVoption = "slv=1"
PROGoption = "prg=1"
NOoption = ""
nevOption = "nevid=1"

http = urllib3.PoolManager()

#
# Color Codes for Printing
#


class BColors:
    HEADER = '<p style=\"font:14pt Helvetiva;\">'
    OKBLUE = '<p>'
    OKGREEN = '<p>'
    WARNING = '<p>'
    FAIL = '<p>'
    ENDC = '</p>'
    BOLD = '<p style=\"font-weight:bold;\">'
    UNDERLINE = '<p>'
    


class SportTypes:
    CODE = {'1': "Football", '2': "Basket", '3': "IceHockey", '4': "Tennis", '5': "Handball", '6': "Baseball",
            '7': "Volleyball", '8': "Golf", '9': "Polo", '10': "Antepost", '11': "Am. Football"}


class Logo:
    @staticmethod
    def clearscreen():
        1==2


#    @staticmethod
#    def printlogo():
#        localtime = time.asctime(time.localtime(time.time()))
#        kstr = ""
#        kstr += localtime + "<br>"
#        kstr += "<br>\033[38;5;202m<br>"
#        kstr += "IIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII&nbsp&nbsp&nbsp&nbspIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII?&nbsp&nbsp&nbsp&nbspIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIIIIIIIIIIIIIIIIII?&nbsp&nbsp+IIIIIIIIIIIIIII:&nbsp&nbsp~IIIIIIIIII&nbsp..IIIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIIII+,,,~,,?+&nbsp&nbsp:I,&nbsp&nbsp&nbsp,,+,,+I.&nbsp&nbsp&nbsp&nbsp&nbsp:II.&nbsp&nbsp+II:&nbsp&nbsp&nbsp?I?.&nbsp..,IIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIIII:&nbsp&nbsp=&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp~&nbsp&nbsp&nbsp&nbsp&nbsp,.&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp,I&nbsp&nbsp&nbspI&nbsp&nbsp&nbsp~:&nbsp&nbsp.=&nbsp&nbsp&nbsp..IIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIIII&nbsp&nbsp&nbspI&nbsp&nbsp&nbspII&nbsp&nbsp&nbspII&nbsp&nbsp&nbspII&nbsp&nbsp&nbsp&nbspIIIII.&nbsp&nbsp~I&nbsp&nbsp&nbspI&nbsp&nbsp&nbspII&nbsp&nbsp&nbspI&nbsp&nbsp&nbspIIIIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIIII&nbsp&nbsp&nbspI&nbsp&nbsp&nbspII&nbsp&nbsp&nbspI=&nbsp&nbsp~II&nbsp&nbsp&nbspII&nbsp&nbsp&nbsp,,&nbsp&nbsp?=&nbsp&nbsp:+&nbsp&nbsp.I~&nbsp&nbsp.I&nbsp&nbsp&nbspIIIIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIIII&nbsp&nbsp&nbspI&nbsp&nbsp&nbspI?&nbsp&nbsp.I.&nbsp&nbspIII&nbsp&nbsp&nbspI,&nbsp&nbsp+I&nbsp&nbsp&nbspI,&nbsp&nbsp?~&nbsp&nbsp:I&nbsp&nbsp&nbspII&nbsp&nbsp&nbspIIIIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIII+&nbsp&nbsp:I&nbsp&nbsp&nbspI=&nbsp&nbsp=I&nbsp&nbsp&nbsp&nbsp.I&nbsp&nbsp&nbspI,&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspI&nbsp&nbsp&nbspII&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp~II&nbsp&nbsp&nbsp&nbspIIIIIIIIIIIIIIIIIIIIII<br>"
#        kstr += "IIIIIIIIIIIIIIIIIIIIII?IIIIIIIII+?IIIIIIIIIIII++IIIIIII?IIIIIIIIIIIIIIIIIIIIIII\033[0m<br>"
#        return kstr


#
# Making actual request and responding with the data
#
class XMLConnector:
    def __init__(self, url=""):
        self.url = url
        xmlfeed = http.request('GET', self.url)
        #print(url)
        if xmlfeed.status == 200:
            self.data = xmlfeed.data
            #


# Run String through MiniDom
#
class XMLTree:
    def __init__(self, datafeed):
        treeelements = ElTree.fromstring(datafeed)
        self.et = treeelements


class XMLParser:
    events = {}

    def __init__(self, xmltoparse):

        events = {}

        for elem in xmltoparse.findall('./Event'):
            tempevent = elem.attrib
            tempoutcome = {}

            for tag in elem.findall('./Outcome'):
                tempoutcome[tag.attrib['ID']] = tag.attrib
            tempevent['outcomes'] = tempoutcome
            events[tempevent['ID']] = tempevent

        self.events = events

    def getevents(self):
        return self.events


#
# Getting the feed and parsing the XML
#
class XMLFeed:
    feed = {}
    cnt = {}
    plusfd = {}
    kompaktfd = {}
    activefd = {}
    inactivefd = {}
    blockedfd = {}

    def __init__(self, system="", feedtype=""):

        self.system = system
        self.feedtype = feedtype

        if self.system == "prod":
            self.url = prodURL
        elif self.system == "test":
            self.url = testURL
        if self.feedtype == "slv":
            self.url = self.url + "?" + SLVoption
        if self.feedtype == "prg":
            self.url = self.url + "?" + PROGoption
        if self.feedtype == "prgslv":
            self.url = self.url + "?slv=1&" + PROGoption
        if self.feedtype == "nevslv":
            today = date.today()
            datestring = today.strftime("%Y%m%d")
            self.url = self.url + "?" + nevOption + "&" + SLVoption + "&dt=" + datestring
        if self.feedtype == "nevslvtuesday":
            today = date.today()
            offset = (6 + today.weekday()) % 7
            lasttuesday = date.today()  - timedelta(days=offset)
            datestring = lasttuesday.strftime("%Y%m%d")
            self.url = self.url + "?" + nevOption + "&" + SLVoption + "&dt=" + datestring
        self.connection = XMLConnector(self.url)

        #
        # Getting data back from the XML RAW DATA
        #
        xmltempdump = XMLTree(self.connection.data)

        parsedfeed = XMLParser(xmltempdump.et)
        self.feed = parsedfeed.getevents()
        #
        # Eriks special ones
        #
        self.eInactive = dict([(k, v) for k, v in self.feed.items() if v['EventStatus'] == "Inactive"])
        self.eBlocked = dict([(k, v) for k, v in self.feed.items() if v['EventStatus'] == "Blocked"])
        self.eActive = dict([(k, v) for k, v in self.feed.items() if v['EventStatus'] == "Active"]) 
        self.eCanc = dict([(k, v) for k, v in self.feed.items() if v['EventStatus'] == "Cancelled"])
        self.ePay = dict([(k, v) for k, v in self.feed.items() if v['EventStatus'] == "Payable"])
        
        self.eEvents = self.eInactive.copy()
        self.eEvents.update(self.eBlocked)
        self.eEvents.update(self.eActive)

        self.eEventsToGo = dict()
        
        date_tomorrow = date.today() + timedelta(days=1)
        tomy = datetime.combine(date_tomorrow, datetime.min.time())
        i=1
        for k, ActiveCurrent in self.eEvents.items():
            date_kickoff = datetime.strptime(ActiveCurrent['Date'], '%Y-%m-%dT%H:%M:%S')
            if tomy < date_kickoff:
                self.eEventsToGo[k]=ActiveCurrent
            if tomy > date_kickoff:
                i+=1
        self.ekompaktfd = dict([(k, v) for k, v in self.eEventsToGo.items() if v['PlayIndex'] != "0"])  # Events with Kompakt ID
        if self.feedtype == "prg" or self.feedtype == "prgslv":
            self.feed = self.eEventsToGo
        

        #
        # Normal ones
        #
        self.kompaktfd = dict( [(k, v) for k, v in self.feed.items() if v['PlayIndex'] != "0"])  # Events with Kompakt ID
        self.activefd = dict( [(k, v) for k, v in self.feed.items() if v['EventStatus'] == "Active"])  # Events that have status active
        self.inactivefd = dict( [(k, v) for k, v in self.feed.items() if v['EventStatus'] == "Inactive"])  # Events that have status inactive
        self.blockedfd = dict( [(k, v) for k, v in self.feed.items() if v['EventStatus'] == "Blocked"])  # Events that have status blocked
        self.notinactivefd = dict([(k, v) for k, v in self.feed.items() if v['EventStatus'] != "Inactive"])  # Events that do not have the status inactive
        
        self.blockedrollingfd = dict([(k, v) for k, v in self.blockedfd.items() if v['EventType'] != "10"])
        self.notinactivenotkompaktfd = dict([(k, v) for k, v in self.notinactivefd.items() if v['PlayIndex'] != "0"])  # Events that are not inactive and have no kompakt ID
        self.kompaktErrors = dict([(k, v) for k, v in self.notinactivenotkompaktfd.items() if v['SE'] != "1"])  # Events that are not inactive, have no kompakt and are singles
        self.antepost = dict([(k, v) for k, v in self.feed.items() if v['EventType'] == "10"])
        self.rolling = dict([(k, v) for k, v in self.notinactivefd.items() if v['EventType'] != "10"])  # Rolling events that are not inactive
        self.rollingSingles = dict( [(k, v) for k, v in self.rolling.items() if v['SE'] == "1"])  # Rolling, not inactive, singles
        self.rollingSinglesNonKompakt = dict([(k, v) for k, v in self.rollingSingles.items() if v['PlayIndex'] == "0"])  # Rolling, not inactive, singles, not kompakt
        self.plus = dict([(k, v) for k, v in self.rolling.items() if v['PlayIndex'] == "0"])  # Rolling Plus
        self.plusActive = dict( [(k, v) for k, v in self.plus.items() if v['EventStatus'] == "Active"])  # Rolling Active Plus
        self.football = dict([(k, v) for k, v in self.feed.items() if v['EventType'] == "1"])
        self.fleagues = dict()
        for k, v in self.football.items():
            if v['League'] in self.fleagues.keys():
                self.fleagues[v['League']]+=1
            else:
                self.fleagues[v['League']]=1

        self.tennis = dict([(k, v) for k, v in self.feed.items() if v['EventType'] == "4"])
    #
    # Count of the kompakt events
    #
    def countplayindex(self):
        return len(self.kompaktfd)
    #
    # Count of the active events
    #
    def countactiveevents(self):
        return len(self.activefd)
    #
    # Count of the inactive events
    #
    def countinactiveevents(self):
        return len(self.inactivefd)
    #
    # Count of the blocked events
    #
    def countblockedevents(self):
        return len(self.blockedfd)
    #
    # Count of all events
    #
    def countevents(self):
        return len(self.feed)
    #
    # Count of the events and the status in a dictionary
    #
    def counteventandstatus(self):
        dct = {}
        for k, v in self.feed.items():
            try:
                len(dct[v['EventType']])
            except KeyError:
                dct[v['EventType']] = {}
                # I know, I know, programming with exceptions
            try:
                dct[v['EventType']][v['EventStatus']] += 1
            except KeyError:
                dct[v['EventType']][v['EventStatus']] = 1
        return dct
    #
    # print the dictionary as per method counteventandstatus
    #
    def printsportevents(self, eventtype):

        tmptotal = 0
        kstr = SportTypes.CODE[eventtype].ljust(20)
        kstr ="<tr>"
        kstr += "<td>"+SportTypes.CODE[eventtype]+"</td>"
        try:
            for k, v in self.cnt[eventtype].items():
                tmptotal += v
            kstr += "<td>" + str(tmptotal)+ "</td>"
        except KeyError:
            kstr += "<td>0</td>"

        try:
            kstr += "<td>"+str(self.cnt[eventtype]['Inactive'])+"</td>"
        except KeyError:
            kstr += "<td>0</td>"

        try:
            kstr += "<td>"+str(self.cnt[eventtype]['Active'])+"</td>"
        except KeyError:
            kstr += "<td>0</td>"

        try:
            kstr += "<td>"+str(self.cnt[eventtype]['Blocked'])+"</td>"
        except KeyError:
            kstr += "<td>0</td>"
        kstr += "<td></td>"
        kstr+="</tr>"
        return kstr

    #
    # Check to find if any kompakt event is not playable as single
    #

    def kompakttreblecheck(self):

        kstr = ""
        for k in self.kompaktErrors.keys():
            if kstr == "":
                kstr = "K:" + k + " "
            else:
                kstr += k + " "

        return kstr

    #
    # Check to find if any non-kompakt match is available as singles
    #
    def singlekompaktcheck(self):

        kstr = ""
        for k in self.rollingSinglesNonKompakt.keys():
            liga = self.feed[k]['League']
            if not (liga == "1.BL" or liga == "CL" or liga == "DFBP"):
                if kstr == "":
                    kstr = "S:" + k + " "
                else:
                    kstr += k + " "
        return kstr

    #
    # Boxing check
    #
    def boxcheck(self):
        kstr = ""
        if self.feedtype == "slv":

            for k, v in self.antepost.items():
                tempstrarr = v['Descr'].split("-")
                tempcnt = len(tempstrarr)
                antepostsporttype = tempstrarr[tempcnt - 1].lower()
                if "box" in antepostsporttype:
                    if kstr == "":
                        kstr = "SLVBOX:" + k + " "
                    else:
                        kstr += k + " "
        return kstr

    #
    # Tennis Check
    #
    def tennischeck(self):

        tempstr = ""

        if self.feedtype == "slv":
            tempstr = ""
            for k, v in self.tennis.items():
                tempstr += "TENNIS:" + k + " "
        kstr = tempstr
        return kstr

    #
    # SLV League Check
    #
    def leaguecheck(self):
        kstr = ""
        leaguelist = ["RL-W", "RL-O", "RL-N", "RL-S", "RL-B"]
        if self.feedtype == "slv":

            for k, v in self.football.items():
                for league in leaguelist:
                    # print(v['League'],league, v['League']==league)
                    if v['League'] == league:
                        kstr += league + ":" + k + " "
        return kstr

    #
    # SLV Market Kompakt Check
    #

    def marketkompaktcheck(self):
        kstr = ""
        deniedmarkets = [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20]

        # print(len(self.kompakt))
        for k, v in self.kompaktfd.items():

            marketkeys = v['outcomes'].keys()

            for market in deniedmarkets:
                marketstr = str(market)
                if marketstr in marketkeys:
                    tempstr = k + ":" + v['outcomes'][marketstr]['Descr'] + " "
                    kstr += tempstr

        return kstr

    #
    # SLV Market Plus Check
    #

    def marketpluscheck(self):
        kstr = ""
        deniedmarketsports = [[1,
                               [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 29, 30, 36, 37, 38,
                                39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60,
                                61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71]], [3, [25, 26]], [5, [25, 26]],
                              [2, [9, 11, 12, 14, 18, 20, 25, 26]], [11, [25, 26]]]

        for deniedSport in deniedmarketsports:
            # print(deniedsport[0])
            temp = dict([(k, v) for k, v in self.plusActive.items() if v['EventType'] == str(deniedSport[0])])
            for k, v in temp.items():
                marketkeys = v['outcomes'].keys()
                for market in deniedSport[1]:
                    marketstr = str(market)

                    if marketstr in marketkeys:
                        kstr += k + ":" + v['outcomes'][marketstr]['Descr'] + " "

        return kstr
    #
    # Count of all the outcomes in the program
    #
    def outcomecount(self):
        tint = 0
        for k, v in self.activefd.items():
            # print(k,len(v['outcomes']))
            tint += len(v['outcomes'])
        kstr = "Active: " + str(tint) + " "
        tint = 0
        for k, v in self.inactivefd.items():
            tint += len(v['outcomes'])

        kstr += "Inactive: " + str(tint) + " "
        return kstr

    #
    # Print blocked events
    #
    def blockedprint(self):
        feedupper = self.feedtype.upper()
        systemupper = self.system.upper()
        kstr = BColors.HEADER + BColors.BOLD + "Blocked Events:" + systemupper + " " + feedupper + BColors.ENDC + "\n"
        kstr += "<table>\n"
        kstr += "<th>" + "Code".ljust(20) + "</th>" + "<th>" + "Kickoff".ljust(30) + "</th>" + "<th>" + "League".ljust(10) + "</th>" + "<th>" + "Description".ljust(60) + "</th>" + "\n"

        for k, v in self.blockedrollingfd.items():
            kstr += "<tr>"
            kstr += "<td>" + k.ljust(20) + "</td>" + "<td>" + v['Date'].ljust(30) + "</td>" + "<td>" + v['League'].ljust(10) + "</td>" + "<td>" + v['Descr'].ljust(60) + "</td>"
            kstr += "</tr>"
        kstr += "</table>"
        return kstr

    #
    # Check for problems with Handicap
    #
    def hccheck(self):
        kstr = ""

        for k, v in self.feed.items():
            keysofevent = v.keys()
            outcomekeys = v['outcomes'].keys()
            outcomes = v['outcomes']

            if 'FinalWithHandicap' in keysofevent and '0' in outcomekeys and '2' in outcomekeys and '6' in outcomekeys:

                final_hc = float(v['FinalWithHandicap'])

                hsieg = float(outcomes['0']['Odd'])
                asieg = float(outcomes['2']['Odd'])
                hchsieg = float(outcomes['6']['Odd'])
                # hcasieg = float(outcomes['8']['Odd'])

                if final_hc > 0 and hsieg < asieg:
                    # Handicap in favor of Home team and home team winning odds lower than away team -> flipped
                    if hsieg < hchsieg:  # if handicap odds are higher, trading did compensate
                        kstr += "H+" + k + " "
                if final_hc < 0 and hsieg > asieg:  # Inverse than above
                    if hsieg > hchsieg:
                        print("a")
                        kstr += "H-" + k + " "

        return kstr
    #
    # Checking of the maximum limits for the odds
    # 50 for specific markets
    # 500 for the other
    #
    def oddcheck(self):
        kstr = ""
        for tempevent in self.rolling.items():
            outcomes = tempevent[1]['outcomes']

            for k, v in outcomes.items():
                if k == str(0) or k == str(1) or k == str(2):
                    if float(outcomes[k]['Odd']) > 50:
                        kstr += "O>" + tempevent[0] + ":"+v['Descr']+":" + outcomes[k]['Odd']
                else:
                    if float(outcomes[k]['Odd']) > 500:
                        kstr += "O>" + tempevent[0] + ":"+v['Descr']+":" + outcomes[k]['Odd']
        return kstr

    #
    # Empty Market Check
    #
    def emptymarketcheck(self):
        kstr=""
        tempset = set()
        for tempevent in self.activefd.items():
            outcomes = tempevent[1]['outcomes']

            for k, v in outcomes.items():
                if outcomes[k]['Market']=="":
                    tempset.add(tempevent[0])


        if len(tempset)!=0:
            kstr+=str(len(tempset))


        tempset = sorted(tempset)

        for theevent in tempset:
            kstr+=theevent+" "

        return kstr


    #
    # Convert specific Event to HTML
    #

    def eventtohtml(self, eventno=None):
        if eventno != "":
            tempevent = self.feed[eventno]
            return tempevent

    #
    # Condensed Status Print
    #

    def listnevids(self):
        nevidlist = []
        for k,v in self.feed.items():
            nevidlist.append(v['NevID'])

        return nevidlist


    def listactivenevids(self):
        nevidlist = []
        for k,v in self.activefd.items():
            nevidlist.append(v['NevID'])

        return nevidlist


    def statusprint(self):
        tempsystemtype = self.system + " " + self.feedtype
        tempstr = tempsystemtype.ljust(10)
        tempstr = tempstr.ljust(10) + ": "
        tempstr += str(self.countevents()).rjust(4) + " "
        tempstr += str(self.countplayindex()).rjust(4) + " "
        tempstr += self.kompakttreblecheck() + " "
        tempstr += self.singlekompaktcheck() + " "
        tempstr += self.hccheck()
        if self.feedtype == "slv":
            tempstr += self.boxcheck() + " "
        return tempstr

    #
    # Long Status Print
    #

    def longstatusprint(self):
        tempsystemtype = self.system + " " + self.feedtype
        self.cnt = self.counteventandstatus()
        tempstr =  "<h2>"+tempsystemtype+"</h2>"

        tempstr += "<table>"
        tempstr += "<colgroup><col span=\"1\" class=\"lightBG\"><col span=\"1\" class=\"darkBG\"><col span=\"1\" class=\"lightBG\"><col span=\"1\" class=\"darkBG\"><col span=\"1\" class=\"lightBG\"><col span=\"1\" class=\"darkBG\"></colgroup>"
        tempstr += "<th>Sport Type</th>"
        tempstr += "<th>All Events</th>"
        tempstr += "<th>Inactive Events</th>"
        tempstr += "<th>Active Events</th>"
        tempstr += "<th>Blocked Events</th>"
        tempstr += "<th>Kompakt Events</th>"
        tempstr += "</tr>"
        
        tempstr += "<tr>"
        tempstr += "<td>Totals</td>"
        tempstr += "<td>"+str(self.countevents())+"</td>"
        tempstr += "<td>"+str(self.countinactiveevents())+"</td>"
        tempstr += "<td>"+str(self.countactiveevents())+"</td>"
        tempstr += "<td>"+str(self.countblockedevents())+"</td>"
        tempstr += "<td>"+str(self.countplayindex())+"</td>"
        tempstr += "</tr>"


        for i in ('1', '2', '3', '4', '5', '10', '11'):
            tempstr += self.printsportevents(i)

        tempstr +="</tr></table>"
        tempstr +="<br /><br />"
        tempstr += "<table class=\"overall_tbl\">"
        if self.feedtype == "prgslv":
            tempstr += "<tr><td>SLV Odds: </td><td>" + self.outcomecount()+"</td></tr>"
        tempstr += "<tr><td>Check Results:</td><td></td></tr>"
        tempstr += "<tr><td>Single, not in Kompakt:</td><td>" + self.singlekompaktcheck() + "</td></tr>"
        tempstr += "<tr><td>Kompakt Event, not single: </td><td>"+ self.kompakttreblecheck()+ "</td></tr>"
        tempstr += "<tr><td>Handicap Issue: </td><td>" + self.hccheck() + "</td></tr>"
        tempstr += "<tr><td>Odd < 50/500 Check: </td><td>" + self.oddcheck() + "</td></tr>"
        tempstr += "<tr><td>Empty Market Descr: </td><td>" + self.emptymarketcheck() + "</td></tr>"
        if self.feedtype == "slv" or self.feedtype == "prgslv":
            tempstr += "<tr><td>SLV Sports Check:  </td><td>" + self.boxcheck() + self.tennischeck()+" </td></tr>"
            tempstr += "<tr><td>SLV Leagues Check: </td><td>" + self.leaguecheck() +  " </td></tr>"
            tempstr += "<tr><td>SLV Markets Check: </td><td>" + self.marketkompaktcheck() + self.marketpluscheck() +"</td></tr>"
        tempstr+="</table>"
        tempstr+="<br /><br />"
        tempstr+="<table>"

        if self.feedtype=="prg" or self.feedtype=="prgslv":

            tempstr += "<tr><td>Football Leagues</td><td></td></tr>"
            for k,v in sorted(self.fleagues.items()):
                tempstr += "<tr><td>" + str(k) + "</td><td>" + str(v) + "</td></tr>"
        
        tempstr +="</table>"
        return tempstr


def main():
    tagzeit = datetime.now()
    print("<html><head>")
    print("<link rel=\"stylesheet\" href=\"oddstyle.css\"")
    print("</head><body><h1>Odds -"+ str(tagzeit) +" </h1>")
    truth = True
    while truth:
        level = 0
        statusbarlength = 100
        delay = 1 / 12

        if len(sys.argv) == 3:
            if sys.argv[1] == "2ndlvl":
                level = 2
            if sys.argv[1] == "erik":
                level = 3
            try:
                delay = 1 / int(sys.argv[2])
            except ValueError:
                delay = 1 / 10

        productionxml = XMLFeed("prod", " ")
        productionxmlslv = XMLFeed("prod", "slv")
        
        productionOddsXML = XMLFeed("prod", "prg")
        productionOddsxmlslv = XMLFeed("prod", "prgslv")


        if date.today().weekday() != 1:
            nevnowxml = XMLFeed("prod", "nevslv")
            nevtuexml = XMLFeed("prod", "nevslvtuesday")

        # testXML = XMLFeed("test", " ")
        # testxmlslv = XMLFeed("test", "slv")
        if level != 3:
            Logo.clearscreen()
            #print(Logo.printlogo())
            print(productionxml.longstatusprint())
            print(productionxmlslv.longstatusprint())
            print()
            if date.today().weekday() != 1:
                eventsnow = set(nevnowxml.listactivenevids())
                eventsthen = set(nevtuexml.listnevids())
                tempstr="SLV Added Matches:".ljust(30) + BColors.FAIL
                if not eventsthen.issuperset(eventsnow):
                    for x in eventsnow.difference(eventsthen):
                        tempstr += "NEV"+ x + " "
                tempstr+=BColors.ENDC
                print(tempstr)

            i = 0 
            while i < statusbarlength:
                time.sleep(delay * 3)
                print("#", end='')
                sys.stdout.flush()
                i += 1

                Logo.clearscreen()
            #print(Logo.printlogo())
            print(productionxml.blockedprint())

            i = 0

            while i < statusbarlength:
                time.sleep(delay)
                sys.stdout.write("#")
                sys.stdout.flush()
                i += 1

        if level == 2:
            Logo.clearscreen()
            #(Logo.printlogo())

            print(subprocess.check_output(["./ps_check.py", ""]).decode("utf-8"))
            print(subprocess.check_output(["./ps_check.py", "1"]).decode("utf-8"))
            i = 0
            while i < statusbarlength:
                time.sleep(delay * 2)
                sys.stdout.write("#")
                sys.stdout.flush()
                i += 1

        truth = False
        if level == 3:
            Logo.clearscreen()
            print(productionOddsXML.longstatusprint())
            
            print(productionOddsxmlslv.longstatusprint())

        print("</body></html>")
if __name__ == '__main__':
    main()
